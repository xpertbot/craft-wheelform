<?php
namespace wheelform\models\fields;

use Craft;

class Select extends BaseFieldType
{
    public $name = "Select";

    public $type = "select";

    public function getFieldRules()
    {
        return [
            [
                'value', 'string', 'message' => $this->label . Craft::t('wheelform', ' must be valid characters.')
            ],
            [
                'value', 'in', 'range' => function(){
                    return (empty($this->options['items']) ? [] : $this->options['items']);
                }, 'when' => function($model){
                    return boolval($model->options['validate']);
                },
                "allowArray" => true,
                'message' => $this->label . Craft::t('wheelform', ' has invalid options.')
            ],
        ];
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
