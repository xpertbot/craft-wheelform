<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
class Form extends ActiveRecord
{
    public $name;
    public $settings;

    protected $_entriesCount;

    public static function tableName(): String
    {
        return '{{%wheelform_forms}}';
    }

    public function init()
    {
        parent::init();

        if ($this->name === null)
        {
            $this->name = \Craft::t('wheelform', 'Contact Form');
        }

    }

    public function rules(): Array
    {
        return [
            [['name', 'settings'], 'required'],
            [['name', 'settings'], 'safe'],
        ];
    }

    public function getEntries()
    {
        return $this->hasMany(Message::className(), ['form_id' => 'id']);
    }

    public function getEntryCount()
    {
         if ($this->isNewRecord) {
            return null; // this avoid calling a query searching for null primary keys
        }

        if($this->_entriesCount == null){
            $this->_entriesCount = $this->getEntries()->count();
        }

        return $this->_entriesCount;
    }
}
