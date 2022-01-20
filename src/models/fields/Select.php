<?php
namespace wheelform\models\fields;

use Craft;

class Select extends BaseFieldType
{
    public $name = "Select";

    public $type = "select";

    public function getConfig()
    {
        return [
            [
                'name' => 'selectEmpty',
                'type' => 'boolean',
                'label' => 'Select Empty',
                'value' => false,
            ],
            [
                'name' => 'validate',
                'type' => 'boolean',
                'label' => 'Validate Options',
                'value' => false,
            ],
            [
                'name' => 'multiple',
                'type' => 'boolean',
                'label' => 'Multiple',
                'value' => false,
            ],
            [
                'name' => 'items',
                'type' => 'list',
                'label' => 'Options',
                'value' => [],
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
