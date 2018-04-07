<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;
use yii\validators\EmailValidator;
use craft\helpers\StringHelper;

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
            [['to_email'], 'validateToEmails'],
            [['active', 'send_email', 'recaptcha'], 'boolean'],
            [['active', 'send_email', 'recaptcha'], 'default', 'value' => 0],
        ];
    }

    public function getEntries()
    {
        return $this->hasMany(Message::className(), ['form_id' => 'id'])->orderBy(['id' => SORT_DESC]);
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

    public function validateToEmails($attribute, $params, $validator)
    {

        if(empty($this->{$attribute})){
            $this->addError($attribute, 'To Email field is Required.');
        }

        $emailList = StringHelper::split($this->{$attribute}, ',');

        $emailValidator = new EmailValidator();

        foreach($emailList as $to_email)
        {
            $to_email = trim($to_email);
            if($emailValidator->validate($to_email) === false)
            {
                //exit on first error
                $this->addError($attribute, 'One or many of the values are not valid emails.');
                break;
            }
        }

        return true;
    }

}
