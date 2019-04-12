<?php
namespace wheelform\models\fields;

class Radio extends BaseFieldType
{
    public $name = "Radio";

    public $type = "radio";

    public function rules()
    {
        return [];
    }

    public function getOptions()
    {
        return [
            'items' => [],
        ];
    }
}
