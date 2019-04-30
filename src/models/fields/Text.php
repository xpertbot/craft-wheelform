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
            ]
        ];
    }
}
