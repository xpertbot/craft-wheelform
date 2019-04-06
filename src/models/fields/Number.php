<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Number implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "number";
    }

    public function getName()
    {
        return "Number";
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
