<?php
namespace wheelform\models\fields;

class Email extends BaseFieldType
{
    public $name = "Email";

    public $type = "email";

    public function fieldRules()
    {
        return [];
    }

    public function getOptions()
    {
        return [
            'user_validation' => 'boolean'
        ];
    }
}
