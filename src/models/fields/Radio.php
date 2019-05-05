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
                'name' => 'display_label',
                'type' => 'boolean',
                'label' => 'Display group label',
                'value' => '',
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
