<?php
namespace wheelform\models\fields;

class Text extends BaseFieldType
{
    public $name = "Text";

    public $type = "text";

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
