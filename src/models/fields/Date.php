<?php
namespace wheelform\models\fields;

class Date extends BaseFieldType
{
    public $name = "Date";

    public $type = "date";

    public function getConfig()
    {
        return [
            [
                'name' => 'placeholder',
                'type' => 'text',
                'label' => 'Placeholder',
                'value' => 'mm/dd/yyyy',
            ],
            [
                'name' => 'min_date',
                'type' => 'text',
                'label' => 'Min Date',
                'value' => '',
                'description' => 'In format yyyy-mm-dd. Leave empty for none.',
            ],
            [
                'name' => 'max_date',
                'type' => 'text',
                'label' => 'Max Date',
                'value' => '',
                'description' => 'In format yyyy-mm-dd. Leave empty for none.',
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
