<?php
namespace wheelform\models\fields;

class Select extends BaseFieldType
{
    public $name = "Select";

    public $type = "select";

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
