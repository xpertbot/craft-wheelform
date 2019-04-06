<?php
namespace wheelform\models\fields;

use craft\base\Model;
use wheelform\interfaces\FieldInterface;

class File extends Model implements FieldInterface
{
    public $name;

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

    public function getType()
    {
        return "file";
    }

    public function getName()
    {
        return "File";
    }

    public function getOptions()
    {
        return [
        ];
    }
}
