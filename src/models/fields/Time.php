<?php
namespace wheelform\models\fields;

class Time extends BaseFieldType
{
    public $name = "Time";

    public $type = "time";

    public function getConfig()
    {
        return [
            [
                'name' => 'min_time',
                'type' => 'text',
                'label' => 'Min Time',
                'value' => '',
                'description' => 'In format hh:mm:ss. Leave empty for none.',
            ],
            [
                'name' => 'max_time',
                'type' => 'text',
                'label' => 'Max Time',
                'value' => '',
                'description' => 'In format hh:mm:ss. Leave empty for none.',
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
