<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;
use Wheelform\Helpers\FormField;
use Wheelform\Helpers\FormSettings;

//Using Active Record because it extends Models.
class Form extends ActiveRecord
{

    protected $_entryCount;
    protected $_fields;
    private $_errors = [];

    public function init()
    {

        if(! empty($this->fields))
        {
            $fields = json_decode($this->fields);
            foreach($fields as $field)
            {
                $this->_fields[] = new FormField($field);
            }
        }
        parent::init();
    }

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

    public function getEntryCount(): int
    {
         if ($this->isNewRecord) {
            return null; // this avoid calling a query searching for null primary keys
        }

        if($this->_entryCount == null){
            $this->_entryCount = $this->getEntries()->count();
        }

        return $this->_entryCount;
    }

    public function setFields(Array $fields = [])
    {
        if(! empty($fields)){
            foreach($fields as $field)
            {
                $formField = new FormField($field);

                if($formField->validate())
                {
                    $this->_fields[] = $formField;
                }
                else
                {
                    $this->_errors = $formField->getErrors();
                }
            }
        }
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function beforeValidate()
    {

        if(! empty($this->_errors))
        {
            return false;
        }

        $this->fields = json_encode($this->_fields);
        var_dump($this->fields);
        die;
        return true;
    }

}
