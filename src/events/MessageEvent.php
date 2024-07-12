<?php
namespace wheelform\events;

use yii\base\Event;

class MessageEvent extends Event
{
    public $form_id;

    public $message;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var bool
     */
    public bool $saveMessage = true;

    /**
     * @var bool
     */
    public bool $sendMessage = true;
}
