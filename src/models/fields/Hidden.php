<?php
namespace wheelform\models\fields;

use Craft;

class Hidden extends BaseFieldType
{
    public $name = "Hidden";

    public $type = "hidden";

    public function getFieldRules()
    {
        return [
            ['value', 'string', 'message' => $this->label . Craft::t('wheelform', ' must be valid characters.')]
        ];
    }

    public function getConfig()
    {
        return [];
    }
}
