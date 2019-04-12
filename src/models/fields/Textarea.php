<?php
namespace wheelform\models\fields;

class Textarea extends BaseFieldType
{
    public $name = "Textarea";

    public $type = "textarea";

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
