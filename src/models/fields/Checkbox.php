<?php
namespace wheelform\models\fields;

class Checkbox extends BaseFieldType
{
    public $name = "Checkbox";

    public $type = "checkbox";

    public function fieldRules()
    {
        return [];
    }

    public function getOptions()
    {
        return [
            'test' => [
                'type' => 'string',
            ]
        ];
    }
}
