<?php
namespace wheelform\events;

use yii\base\Event;

class SendEvent extends Event
{
    public $form_id;

    public $subject;

    public $message;

    public $from;

    public $to;

    public $reply_to;

    public $email_html;

}
