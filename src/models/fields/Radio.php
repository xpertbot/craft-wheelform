<?php
namespace wheelform\models\fields;

use Craft;

class Radio extends BaseFieldType
{
    public $name = "Radio";

    public $type = "radio";

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
        return [
            [
                'name' => 'items',
                'type' => 'list',
                'label' => 'Options',
                'value' => [],
            ],
        ];
    }
}
