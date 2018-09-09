<?php
namespace wheelform\models\fields;

use craft\base\Model;

class File extends Model
{

    public $uploaded;

    public $name;

    public function rules()
    {
        return [
            [['name'], 'string'],
            [['uploaded'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function upload($path)
    {
        if ($this->validate())
        {
            $this->uploaded->saveAs($path . '/' . $this->name);
            return true;
        } else {
            return false;
        }
    }
}
