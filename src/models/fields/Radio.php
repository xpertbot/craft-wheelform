<?php
namespace wheelform\models\fields;

class Radio extends BaseFieldType
{
    public $name = "Radio";

    public $type = "radio";

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
