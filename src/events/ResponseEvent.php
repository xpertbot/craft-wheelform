<?php
namespace wheelform\events;

use yii\base\Event;

class ResponseEvent extends Event
{
    public $headers;

    public $message;

    public $success;

    public $data;

    public $errors;

    public $redirect;

    public $entry;
}
