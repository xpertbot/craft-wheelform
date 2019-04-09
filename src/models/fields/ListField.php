<?php
namespace wheelform\models\fields;

class ListField extends BaseFieldType
{
    public $name = "List";

    public $type = "list";

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
