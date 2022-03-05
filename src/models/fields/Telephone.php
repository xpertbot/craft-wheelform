<?php
namespace wheelform\models\fields;

class Telephone extends BaseFieldType
{
    public $name = "Telephone";

    public $type = "tel";

    public function getConfig()
    {
        return [
            [
                'name' => 'pattern',
                'type' => 'text',
                'label' => 'Pattern',
                'value' => '[0-9]{3}-[0-9]{3}-[0-9]{4}',
            ],
            [
                'name' => 'placeholder',
                'type' => 'text',
                'label' => 'Placeholder',
                'value' => '123-456-7890',
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
