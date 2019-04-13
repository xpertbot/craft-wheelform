<?php
namespace wheelform\models\fields;

class File extends BaseFieldType
{
    public $name = "File";

    public $type = "file";

    public $fieldName;

    public $filePath;

    public $assetId;

    public function rules()
    {
        return [
            [['name', 'filePath'], 'string'],
            [['name', 'filePath'], 'required'],
            ['assetId', 'integer'],
        ];
    }

    public function getConfig()
    {
        return [];
    }
}
