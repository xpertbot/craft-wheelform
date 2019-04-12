<?php
namespace wheelform\models\fields;

class Email extends BaseFieldType
{
    public $name = "Email";

    public $type = "email";

    public function rules()
    {
        return [];
    }

    public function getOptions()
    {
        return [
            'is_reply_to' => false,
            'is_user_notification_field' =>false,
        ];
    }
}
