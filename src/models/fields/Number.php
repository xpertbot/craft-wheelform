<?php
namespace wheelform\models\fields;

use Craft;

class Number extends BaseFieldType
{
    public $name = "Number";

    public $type = "number";

    public function getFieldRules()
    {
        return [
            ['value', 'number','message' => $this->label.Craft::t('wheelform', ' must be a number.')],
        ];
    }

    public function getConfig()
    {
        return [];
    }
}
