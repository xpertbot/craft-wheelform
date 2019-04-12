<?php
namespace wheelform\models\fields;

class Checkbox extends BaseFieldType
{
    public $name = "Checkbox";

    public $type = "checkbox";

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
