<?php
namespace wheelform;

use Craft;
use craft\helpers\StringHelper;
use craft\mail\Message as MailMessage;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Markdown;
use craft\helpers\FileHelper;
use wheelform\events\SendEvent;
use wheelform\models\Form;

class Mailer extends Component
{
    const EVENT_BEFORE_SEND = 'beforeSend';

    public function send(Form $form, array $message): bool
    {
        // Get the plugin settings and make sure they validate before doing anything
        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            throw new InvalidConfigException(Craft::t('wheelform', "Plugin settings need to be configured."));
        }

        // Prep the message Variables
        $defaultSubject = $form->name . " - " . Craft::t("wheelform", 'Submission');
        $from_email = $settings->from_email;
        $mailMessage = new MailMessage();
        $mailer = Craft::$app->getMailer();
        $text = '';

        $event = new SendEvent([
            'form_id' => $form->id,
            'subject' => $defaultSubject,
            'message' => $message,
        ]);

        $this->trigger(self::EVENT_BEFORE_SEND, $event);

        //gather message
        if(! empty($event->message))
        {
            foreach($event->message as $m)
            {
                $text .= ($text ? "\n" : '')."- **{$m['label']}:** ";

                switch ($m['type'])
                {
                    case 'file':
                        if(! empty($m['value'])){
                            $attachment = json_decode($m['value']);

                            $mailMessage->attach($attachment->tempName, [
                                'fileName' => $attachment->name,
                                'contentType' => FileHelper::getMimeType($attachment->tempName),
                            ]);
                            $text .= $attachment->name;
                        }
                        break;

                    case 'checkbox':
                        $text .= (is_array($m['value']) ? implode(', ', $m['value']) : $m['value']);
                        break;

                    default:
                        //Text, Email, Number
                        $text .= $m['value'];
                        break;
                }
            }
        }

        $html_body = $this->compileHtmlBody($text);

        $mailMessage->setFrom($from_email);
        $mailMessage->setSubject($event->subject);
        $mailMessage->setTextBody($text);
        $mailMessage->setHtmlBody($html_body);

        // Grab any "to" emails set in the form settings.
        $to_emails = StringHelper::split($form->to_email);

        foreach ($to_emails as $to_email) {
            $to_email = trim($to_email);
            $mailMessage->setTo($to_email);
            $mailer->send($mailMessage);
        }

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
