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
            [
                'name' => 'display_required_attribute',
                'type' => 'boolean',
                'label' => 'Display Required Attribute',
                'value' => false,
                'condition' => 'required', // using lodash _.get we can call nested object e.g. options.placeholder
                'display_side' => 'left',
            ]
        ];
    }
}
