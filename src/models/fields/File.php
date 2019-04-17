<?php
namespace wheelform\models\fields;

class File extends BaseFieldType
{
    public $name = "File";

    public $type = "file";

    public $fieldName;

    public $filePath;

    public $assetId;

    public function getFieldRules()
    {
        //TODO This needs fixing
        return [
            [['name', 'filePath'], 'string'],
            [['name', 'filePath'], 'required'],
            ['assetId', 'integer'],
            ['value', 'file'],
        ];
    }

    public function getConfig()
    {
        return [];
    }
}
