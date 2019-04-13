<?php
namespace wheelform\models\fields;

class Select extends BaseFieldType
{
    public $name = "Select";

    public $type = "select";

    public function rules()
    {
        return [];
    }

    public function getConfig()
    {
        return [
            [
                'name' => 'validate',
                'type' => 'boolean',
                'label' => 'Validate Options',
                'value' => false,
            ],
            [
                'name' => 'items',
                'type' => 'list',
                'label' => 'Options',
                'value' => [],
            ],
        ];
    }
}
