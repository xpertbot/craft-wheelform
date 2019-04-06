<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Radio implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "radio";
    }

    public function getName()
    {
        return "Radio";
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
