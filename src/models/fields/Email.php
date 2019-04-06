<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;

class Email implements FieldInterface
{
    public function rules()
    {
        return [];
    }

    public function getType()
    {
        return "email";
    }

    public function getName()
    {
        return "Email";
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
