<?php
namespace wheelform\models\fields;

class Text extends BaseFieldType
{
    public $name = "Text";

    public $type = "text";

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
