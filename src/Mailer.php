<?php
namespace Wheelform;

use Craft;
use craft\helpers\StringHelper;
use craft\mail\Message;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Markdown;

use Wheelform\Models\Message as MessageModel;

class Mailer extends Component
{
    // const EVENT_BEFORE_SEND = 'beforeSend';

    // const EVENT_AFTER_SEND = 'afterSend';

    public function send(String $to_email, String $form_name, MessageModel $messageModel): bool
    {
        // Get the plugin settings and make sure they validate before doing anything
        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            throw new InvalidConfigException('Form settings don’t validate.');
        }

        $mailer = Craft::$app->getMailer();

        // Prep the message
        $from_email = $settings->from_email;
        $subject = $form_name . " Submission";
        $text_body = $this->compileTextBody($messageModel);
        $html_body = $this->compileHtmlBody($text_body);

        $message = (new Message())
            ->setFrom($from_email)
            ->setSubject($subject)
            ->setTextBody($text_body)
            ->setHtmlBody($html_body);

        // if ($submission->attachment !== null) {
        //     foreach ($submission->attachment as $attachment) {
        //         $message->attach($attachment->tempName, [
        //             'fileName' => $attachment->name,
        //             'contentType' => FileHelper::getMimeType($attachment->tempName),
        //         ]);
        //     }
        // }

        // Grab any "to" emails set in the plugin settings.
        $to_emails = StringHelper::split($to_email);

        // Fire a 'beforeSend' event
        // $event = new SendEvent([
        //     'submission' => $submission,
        //     'message' => $message,
        //     'toEmails' => $toEmails,
        // ]);
        // $this->trigger(self::EVENT_BEFORE_SEND, $event);

        // if ($event->isSpam) {
        //     Craft::info('Form submission suspected to be spam.', __METHOD__);
        //     return true;
        // }

        foreach ($to_emails as $to_email) {
            $message->setTo($to_email);
            $mailer->send($message);
        }

        return true;
    }

    public function compileTextBody(MessageModel $message): string
    {

        $text = '';

        foreach ($message->value as $messageValue) {
            $text .= ($text ? "\n" : '')."- **{$messageValue->field->getLabel()}:** ";
            $text .= $messageValue->value;
        }

        return $text;
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
