<?php
namespace wheelform\models\fields;

class Checkbox extends BaseFieldType
{
    public $name = "Checkbox";

    public $type = "checkbox";

    public function getConfig()
    {
        return [
            [
                'name' => 'items',
                'type' => 'list',
                'label' => 'Options',
                'value' => [],
            ],
        ];
    }
}
