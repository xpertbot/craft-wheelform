<?php
namespace wheelform\models\fields;

use Craft;

class Textarea extends BaseFieldType
{
    public $name = "Textarea";

    public $type = "textarea";

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
