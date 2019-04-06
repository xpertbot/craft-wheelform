<?php
namespace wheelform\events;

use yii\base\Event;

/**
 * RegisterFieldsEvent class.
 */
class RegisterFieldsEvent extends Event
{
    /**
     * @var string[] List of registered component types classes.
     */
    public $fields = [];
}
