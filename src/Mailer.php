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

    protected $form = null;

    public function send(Form $form, array $message): bool
    {
        // Get the plugin settings and make sure they validate before doing anything
        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            throw new InvalidConfigException(Craft::t('wheelform', "Plugin settings need to be configured."));
        }

        $this->form = $form;

        // Prep the message Variables
        $defaultSubject = $this->form->name . " - " . Craft::t("wheelform", 'Submission');
        $from_email = $settings->from_email;
        // Grab any "to" emails set in the form settings.
        $to_emails = StringHelper::split($this->form->to_email);
        $mailMessage = new MailMessage();
        $mailer = Craft::$app->getMailer();
        $text = '';

        $beforeEvent = new SendEvent([
            'form_id' => $this->form->id,
            'subject' => $defaultSubject,
            'message' => $message,
            'from' => $from_email,
            'to' => $to_emails,
            'reply_to' => '',
            'email_html' => '',
            'template' => '',
            'template_mode' => ''
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

        if(! empty($beforeEvent->email_html)) {
            $html_body = $beforeEvent->email_html;
        } else {
            $template = $this->getBodyTemplate($beforeEvent);
            $html_body = $this->compileHtmlBody($beforeEvent->message, $template['file'], $template['mode']);
        }

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
            'email_html' => $beforeEvent->email_html,
        ]);
        $this->trigger(self::EVENT_AFTER_SEND, $afterEvent);

        return true;
    }

    public function compileHtmlBody(array $variables, $templateFile, $templateMode)
    {
        $view = Craft::$app->getView();
        $originalTemplateMode = $view->getTemplateMode();

        $view->setTemplateMode(constant('craft\web\View::' . $templateMode));

        $html = $view->renderTemplate($templateFile, [
            'fields' => $variables
        ]);

        // Reset
        $view->setTemplateMode($originalTemplateMode);

        return $html;
    }

    /**
     * Evaluate what template should be used to render the body of the e-mail.
     *
     * Templates can have 3 sources.
     * - Default
     * - Overriden in the config file
     * - Overriden in the EVENT_BEFORE_SEND
     *
     * @param SendEvent $event
     *
     * @return array
     */
    public function getBodyTemplate(SendEvent $event) : array
    {
        $config = Craft::$app->getConfig();
        $customConfig =$config->getConfigFromFile('wheelform');

        // Set default values.
        $templateMode = 'TEMPLATE_MODE_CP';
        $template = $this->defaultConfig['template'];

        // In the config a global default can be set and/or specific for each form.
        if (is_array($customConfig)) {
            // If a global default template is set in config use that one.
            if (array_key_exists('template', $customConfig)) {
                $templateMode = 'TEMPLATE_MODE_SITE';
                $template = $customConfig['template'];
            }

            // Check if the current form has it's own template.
            if (array_key_exists('templates', $customConfig)) {
                $templates = $customConfig['templates'];
                // Search in the provided templates if there is one set for the current form id.
                $templateKey = array_search($event->form_id, array_column($templates, 'form_id'));
                $templateMode = 'TEMPLATE_MODE_SITE';
                $template = $templates[$templateKey]['path'];
            }
        }

        // Lastly check if the template is overridden in the Before Send Event.
        // This allows overriding templates for individual mails.
        if ($event->template) {
            $template = $event->template;
        }
        if ($event->template_mode) {
            $templateMode = $event->template_mode;
        }

        return [
            'file' => $template,
            'mode' => $templateMode
        ];
    }
}
