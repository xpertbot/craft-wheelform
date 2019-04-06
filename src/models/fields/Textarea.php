<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Textarea implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "textarea";
    }

    public function getName()
    {
        return "Textarea";
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
