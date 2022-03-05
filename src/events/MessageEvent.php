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
}
