<?php
namespace wheelform\models\fields;

abstract class BaseFieldType
{
    public $name;

    public $type;

    public $required = 0;

    public $index_view = 0;

    public $order = 0;

    public $active = 1;

    public $options= [];

    public $config = [];

    public $label;

    public $value;

    public $fieldComponent = 'Field';

    public function __construct()
    {
        $this->config = $this->getFieldConfig();
    }

    public function getFieldConfig()
    {
        $default = [
            [
                'name' => 'label',
                'type' => 'text',
                'label' => 'Label',
                'value' => '',
            ],
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

        return array_merge_recursive($default, $this->getConfig());
    }

    public function getConfig()
    {
        return [];
    }
}
