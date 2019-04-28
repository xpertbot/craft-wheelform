<?php
namespace wheelform\models\fields;

use Craft;

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

    public function __construct()
    {
        $this->config = $this->getFieldConfig();
    }

    public function rules()
    {
        $customRules = $this->getFieldRules();
        $rules = [
            ['value', 'required', 'when' => function($model){
                    return (bool) $model->required;
                }, 'message' => $this->label . Craft::t('wheelform', ' cannot be blank.')
            ]
        ];

        return array_merge_recursive($rules, $customRules);
    }

    final public function getFieldConfig()
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
}
