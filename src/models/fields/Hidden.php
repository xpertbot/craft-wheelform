<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Hidden implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "hidden";
    }

    public function getName()
    {
        return "Hidden";
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
