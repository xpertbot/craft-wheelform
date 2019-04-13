<?php
namespace wheelform\models\fields;

class Hidden extends BaseFieldType
{
    public $name = "Hidden";

    public $type = "hidden";

    public function rules()
    {
        return [];
    }

    public function getConfig()
    {
        return [];
    }
}
