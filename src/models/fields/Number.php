<?php
namespace wheelform\models\fields;

class Number extends BaseFieldType
{
    public $name = "Number";

    public $type = "number";

    public function rules()
    {
        return [];
    }

    public function getOptions()
    {
        return [
        ];
    }
}
