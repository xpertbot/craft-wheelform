<?php
namespace wheelform\models\fields;

class Consent extends BaseFieldType
{
    public $name = "Consent";

    public $type = "consent";

    public function getConfig()
    {
        return [
            [
                'name' => 'display_label',
                'type' => 'boolean',
                'label' => 'Display group label',
                'value' => '',
            ],
            [
                'name' => 'display_required_attribute',
                'type' => 'boolean',
                'label' => 'Display Required Attribute',
                'value' => false,
                'condition' => 'required', // using lodash _.get we can call nested object e.g. options.placeholder
                'display_side' => 'left',
            ],
            [
                'name' => 'admin_consent_value',
                'type' => 'text',
                'label' => 'Admin Consent Value',
                'value' => '',
                'placeholder' => 'I Agree',
                'display_side' => 'left',
            ],
            [
                'name' => 'consent_message',
                'type' => 'textarea',
                'label' => 'Display consent message',
                'value' => '',
                'display_side' => 'both',
            ],
        ];
    }
}
