<?php

namespace wheelform\events;

use wheelform\models\Form;
use craft\events\CancelableEvent;

/**
 * Form event class.
 *
 * @author takuma <hannes.horneber@takuma.de>
 */
class FormEvent extends CancelableEvent
{
    // Properties
    // =========================================================================

    /**
     * @var Form|null The form model associated with the event.
     */
    public $form;

    /**
     * @var bool Whether the form is brand new
     */
    public $isNew = false;
}
