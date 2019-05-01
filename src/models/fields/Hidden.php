<?php
namespace wheelform\models\fields;

class Hidden extends BaseFieldType
{
    public $name = "Hidden";

    public $type = "hidden";

    public function getFieldConfig()
    {
        return [
            [
                'name' => 'containerClass',
                'type' => 'text',
                'label' => 'Container Class',
                'value' => '',
            ],
            [
                'name' => 'fieldClass',
                'type' => 'text',
                'label' => 'Field Class',
                'value' => '',
            ],
        ];
    }
}
