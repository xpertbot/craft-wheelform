<?php
namespace wheelform\models\fields;

class Number extends BaseFieldType
{
    public $name = "Number";

    public $type = "number";

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
