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
                'name' => 'items',
                'type' => 'list',
                'label' => 'Options',
                'value' => [],
            ],
        ];
    }
}
