<?php
namespace wheelform\models\fields;

use Craft;

class Email extends BaseFieldType
{
    public $name = "Email";

    public $type = "email";

    public function getConfig()
    {
        return [
            [
                'name' => 'is_reply_to',
                'type' => 'boolean',
                'label' => 'Reply-to Email',
                'value' => false,
            ],
            [
                'name' => 'placeholder',
                'type' => 'text',
                'label' => 'Placeholder',
                'value' => '',
            ],
            [
                'name' => 'is_user_notification_field',
                'type' => 'boolean',
                'label' => 'User Notification Field',
                'value' => false,
            ],
        ];
    }
}
