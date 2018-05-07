<?php

namespace wheelform\events;

use wheelform\models\Form;

/**
 * Form event class.
 *
 * @author takuma <hannes.horneber@takuma.de>
 */
class FormEvent extends CancelableEvent
{
    // Properties
    // =========================================================================
    const EVENT_BEFORE_FORM_SAVE = 'beforeWheelFormSave';
    const EVENT_AFTER_FORM_SAVE = 'afterWheelFormSave';
    const EVENT_BEFORE_FORM_SENT = 'beforeWheelFormSent';
    const EVENT_AFTER_FORM_SENT = 'afterWheelFormSent';

    /**
     * @var Form|null The form model associated with the event.
     */
    public $form;

    /**
     * @var bool Whether the form is brand new
     */
    public $isNew = false;
}
