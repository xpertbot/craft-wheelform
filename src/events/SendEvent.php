<?php
namespace wheelform\events;

use yii\base\Event;

class SendEvent extends Event
{
    public $form_id;
    
    public $fromEmail;

    public $subject;

    public $message;

}
