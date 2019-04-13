<?php
namespace wheelform\models\fields;

class ListField extends BaseFieldType
{
    public $name = "List";

    public $type = "list";

    public function rules()
    {
        return [];
    }

    public function getConfig()
    {
        return [
        ];
    }
}
