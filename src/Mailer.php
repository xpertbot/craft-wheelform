<?php
namespace wheelform;

use Craft;
use craft\helpers\StringHelper;
use craft\mail\Message;
use wheelform\events\FormEvent;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Markdown;
use craft\helpers\FileHelper;

use wheelform\models\Message as MessageModel;

class Mailer extends Component
{
    // const EVENT_BEFORE_SEND = 'beforeSend';

    // const EVENT_AFTER_SEND = 'afterSend';

    public function send(String $to_email, String $form_name, MessageModel $model): bool
    {
        // Get the plugin settings and make sure they validate before doing anything
        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            throw new InvalidConfigException(Craft::t('wheelform', "Plugin settings need to be configured."));
        }

        // send event before the form is sent
        $this->trigger(FormEvent::EVENT_BEFORE_FORM_SENT, new FormEvent([
            'form' => $model->getForm()
        ]));

        $mailer = Craft::$app->getMailer();

        // Prep the message
        $from_email = $settings->from_email;
        $subject = $form_name . " Submission";

        $text = "";

        $message = new Message();
        //gather values
        if ($model->value !== null) {
            foreach ($model->value as $valueModel) {
                $text .= ($text ? "\n" : '')."- **{$valueModel->field->getLabel()}:** ";

                switch ($valueModel->field->type)
                {
                    case 'file':
                        if(! empty($valueModel->value)){
                            $attachment = json_decode($valueModel->value);

                            $message->attach($attachment->tempName, [
                                'fileName' => $attachment->name,
                                'contentType' => FileHelper::getMimeType($attachment->tempName),
                            ]);
                            $text .= $attachment->name;
                        }
                        break;

                    case 'checkbox':
                        $text .= (is_array($valueModel->value) ? implode(', ', $valueModel->value) : $valueModel->value);
                        break;

                    default:
                        //Text, Email, Number
                        $text .= $valueModel->value;
                        break;
                }
            }
        }

        $html_body = $this->compileHtmlBody($text);

        $message->setFrom($from_email);
        $message->setSubject($subject);
        $message->setTextBody($text);
        $message->setHtmlBody($html_body);

        // Grab any "to" emails set in the plugin settings.
        $to_emails = StringHelper::split($to_email);

        foreach ($to_emails as $to_email) {
            $to_email = trim($to_email);
            $message->setTo($to_email);
            $mailer->send($message);
        }

        // send event before the form is sent
        $this->trigger(FormEvent::EVENT_AFTER_FORM_SENT, new FormEvent([
            'form' => $model->getForm()
        ]));

        return true;
    }

    public function compileHtmlBody(string $textBody): string
    {
        $html = Markdown::process($textBody);

        // Prevent Twig tags from getting parsed
        // TODO: probably safe to remove?
        $html = str_replace(['{%', '{{', '}}', '%}'], ['&lbrace;%', '&lbrace;&lbrace;', '&rbrace;&rbrace;', '%&rbrace;'], $html);

        return $html;
    }
}
