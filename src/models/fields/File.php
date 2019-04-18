<?php
namespace wheelform\models\fields;

use craft\base\Model;

class File extends Model
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
}
