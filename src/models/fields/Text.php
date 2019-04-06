<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Text implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "text";
    }

    public function getName()
    {
        return "Text";
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
