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
            [['name', 'to_email'], 'required'],
            ['name', 'string'],
            [['to_email'], 'email'],
            ['active', 'integer', 'integerOnly' => true, 'min' => 0],
            ['active', 'default', 'value' => 0],
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

    public function unlinkFields()
    {
        $this->unlinkAll('fields', true);
    }

}
