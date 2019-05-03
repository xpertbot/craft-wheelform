<?php
namespace wheelform\models\fields;

class HtmlField extends BaseFieldType
{
    public $name = "HTML";

    public $type = "html";

    public $fieldComponent = 'Html';

    public function getFieldConfig()
    {
        return [
            [
                'name' => 'content',
                'type' => 'text',
                'label' => '',
                'value' => '',
            ],
        ];
    }
}
