<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Select implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "select";
    }

    public function getName()
    {
        return "Select";
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
