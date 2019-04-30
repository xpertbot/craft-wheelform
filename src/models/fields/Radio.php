<?php
namespace wheelform\models\fields;

use Craft;

class Radio extends BaseFieldType
{
    public $name = "Radio";

    public $type = "radio";

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
