<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
//__get() in BaseActiveRecord does not allow properties to be predefined it sets them to null;
class Form extends ActiveRecord
{

    protected $_entryCount;

    public static function tableName(): String
    {
        return '{{%wheelform_forms}}';
    }

    public function rules(): Array
    {
        return [
            [['form_name', 'to_email'], 'required'],
            ['form_name', 'string'],
            [['to_email'], 'email'],
            [['form_name', 'to_email'], 'safe'],
        ];
    }

    public function getEntries()
    {
        return $this->hasMany(Message::className(), ['form_id' => 'id']);
    }

    public function getFields()
    {
        return $this->hasMany(FormField::className(), ['form_id' => 'id']);
    }

    public function getEntryCount(): int
    {
         if ($this->isNewRecord)
         {
            return null; // this avoid calling a query searching for null primary keys
        }

        if($this->_entryCount == null)
        {
            $this->_entryCount = $this->getEntries()->count();
        }

        return $this->_entryCount;
    }

}
