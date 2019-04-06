<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Checkbox implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "checkbox";
    }

    public function getName()
    {
        return "Checkbox";
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
