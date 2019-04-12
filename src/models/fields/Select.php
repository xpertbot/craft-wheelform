<?php
namespace wheelform\models\fields;

class Select extends BaseFieldType
{
    public $name = "Select";

    public $type = "select";

    public function rules()
    {
        return [];
    }

    public function getOptions()
    {
        return [
            'validate' => true,
            'items' => [],
        ];
    }
}
