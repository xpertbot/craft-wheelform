<?php
namespace wheelform\models\fields;

class ToggleField extends BaseFieldType
{
    public $name = "Toggle";

    public $type = "toggle";

    public function getConfig()
    {
        return [
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
