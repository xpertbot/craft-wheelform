<?php
namespace wheelform\models\fields;

use Craft;

class Number extends BaseFieldType
{
    public $name = "Number";

    public $type = "number";

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
