<?php
namespace wheelform\models\fields;

class Textarea extends BaseFieldType
{
    public $name = "Textarea";

    public $type = "textarea";

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
