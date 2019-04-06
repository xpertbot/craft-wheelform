<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class ListField implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "list";
    }

    public function getName()
    {
        return "List";
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
