<?php

namespace wheelform\models;

use Craft;
use craft\base\Model;
use wheelform\db\FormField;
use wheelform\models\fields\BaseFieldType;

class Settings extends Model
{
    public $from_email;

    public $from_name;

    public $cp_label;

    public $success_message;

    public $volume_id;

    public $recaptcha_version;

    public $recaptcha_public;

    public $recaptcha_secret;

    public $recaptcha_min_score;

    public $disabled_fields;

    private $availableFields = [];

    public function init(): void
    {
        parent::init();

        $activeRecord = new FormField;

        foreach ($activeRecord->getFieldTypeClasses() as $field) {
            $fieldClass = new $field;
            if (!($fieldClass instanceof BaseFieldType)) {
                continue;
            }

            $this->availableFields[] = $fieldClass->type;
        }
    }

    public function defineRules(): array
    {
        return [
            [['from_email', 'success_message'], 'required', 'message' => Craft::t('wheelform', 'From email / Success Message cannot be blank.')],
            ['recaptcha_version', 'in', 'range' => ["2", "3"]],
            ['recaptcha_version', 'default', 'value' => 2],
            ['recaptcha_min_score', 'default', 'value' => 0.5],
            ['from_email', 'email', 'message' => Craft::t('wheelform', 'From email is not a valid email address.')],
            [['success_message', 'cp_label', 'recaptcha_public', 'recaptcha_secret', 'from_name'], 'string'],
            [['volume_id'], 'integer'],
            [['disabled_fields'], 'validateDisabledFields'],
        ];
    }

    public function validateDisabledFields($attribute)
    {
        if (empty($this->$attribute)) {
            return;
        }

        foreach ($this->$attribute as $disabledField) {
            if (!in_array($disabledField, $this->availableFields)) {
                $this->addError($attribute, Craft::t('wheelform', "Unknown disabled field: '{attribute}'", ['attribute' => $attribute]));
                break;
            }
        }
    }
}
