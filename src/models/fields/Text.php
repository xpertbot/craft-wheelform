<?php
namespace wheelform\models\fields;

use Craft;

class Text extends BaseFieldType
{
    public $name = "Text";

    public $type = "text";

    public function getConfig()
    {
        return [
            [
                'name' => 'placeholder',
                'type' => 'text',
                'label' => 'Placeholder',
                'value' => '',
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
