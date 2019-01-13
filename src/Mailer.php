<?php
namespace wheelform;

use Craft;
use yii\base\Component;
use yii\helpers\Markdown;
use craft\services\Assets;
use wheelform\models\Form;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use wheelform\events\SendEvent;
use yii\base\InvalidConfigException;
use craft\mail\Message as MailMessage;

class Mailer extends Component
{
    const EVENT_BEFORE_SEND = 'beforeSend';
    const EVENT_AFTER_SEND = 'afterSend';

    protected $defaultConfig = [
        'template' => 'wheelform/_emails/general.twig',
    ];

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
        // Grab any "to" emails set in the form settings.
        $to_emails = StringHelper::split($form->to_email);
        $mailMessage = new MailMessage();
        $mailer = Craft::$app->getMailer();
        $text = '';

        $beforeEvent = new SendEvent([
            'form_id' => $form->id,
            'subject' => $defaultSubject,
            'message' => $message,
            'from' => $from_email,
            'to' => $to_emails,
            'reply_to' => '',
        ]);

        $this->trigger(self::EVENT_BEFORE_SEND, $beforeEvent);

        //gather message
        if(! empty($beforeEvent->message))
        {
            foreach($beforeEvent->message as $k => $m)
            {
                $text .= ($text ? "\n" : '')."- **{$m['label']}:** ";

                switch ($m['type'])
                {
                    case 'file':
                        if(! empty($m['value'])){
                            $attachment = json_decode($m['value']);
                            $mailMessage->attach($attachment->filePath, [
                                'fileName' => $attachment->name,
                                'contentType' => FileHelper::getMimeType($attachment->filePath),
                            ]);
                            $text .= $attachment->name;

                            // Prepare for Twig
                            $beforeEvent->message[$k]['value'] = $attachment;
                        }
                        break;
                    case 'checkbox':
                        $text .= (is_array($m['value']) ? implode(', ', $m['value']) : $m['value']);
                        break;
                    case 'list':
                        if(! is_array($m['value']) || empty($m['value'])) {
                            $text .= "";
                        } else {
                            foreach($m['value'] as $value) {
                                $text .= "\n*" . $value;
                            }
                        }
                        break;
                    default:
                        //Text, Email, Number
                        $text .= $m['value'];
                        break;
                }
            }
        }

        $html_body = $this->compileHtmlBody($beforeEvent->message);

        $mailMessage->setFrom($beforeEvent->from);
        $mailMessage->setSubject($beforeEvent->subject);
        $mailMessage->setTextBody($text);
        $mailMessage->setHtmlBody($html_body);

        if(! empty($beforeEvent->reply_to)) {
            $mailMessage->setReplyTo($beforeEvent->reply_to);
        }

        if(is_array($beforeEvent->to)) {
            foreach ($beforeEvent->to as $to_email) {
                $to_email = trim($to_email);
                $mailMessage->setTo($to_email);
                $mailer->send($mailMessage);
            }
        } else {
            $to_email = trim($beforeEvent->to);
            $mailMessage->setTo($to_email);
            $mailer->send($mailMessage);
        }

        $afterEvent = new SendEvent([
            'form_id' => $beforeEvent->form_id,
            'subject' => $beforeEvent->subject,
            'message' => $beforeEvent->message,
            'from' => $beforeEvent->from,
            'to' => $beforeEvent->to,
            'reply_to' => $beforeEvent->reply_to,
        ]);
        $this->trigger(self::EVENT_AFTER_SEND, $afterEvent);

        return true;
    }

    public function compileHtmlBody(array $variables)
    {
        $config = Craft::$app->getConfig();
        $customConfig =$config->getConfigFromFile('wheelform');
        $view = Craft::$app->getView();
        $templateMode = $view->getTemplateMode();

        $view->setTemplateMode($view::TEMPLATE_MODE_CP);
        $template = $this->defaultConfig['template'];

        if(is_array($customConfig)) {
            if(array_key_exists('template', $customConfig)) {
                $view->setTemplateMode($view::TEMPLATE_MODE_SITE);
                $template = $customConfig['template'];
            }
        }

        $html = $view->renderTemplate($template, [
            'fields' => $variables
        ]);

        // Reset
        $view->setTemplateMode($templateMode);

        return $html;
    }
}
