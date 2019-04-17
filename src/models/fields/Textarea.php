<?php
namespace wheelform\models\fields;

use Craft;

class Textarea extends BaseFieldType
{
    public $name = "Textarea";

    public $type = "textarea";

    public function getFieldRules()
    {
        return [
            [
                'value', 'string', 'message' => $this->label . Craft::t('wheelform', ' must be valid characters.')
            ]
        ];
    }

    public function getConfig()
    {
        return [];
    }
}
