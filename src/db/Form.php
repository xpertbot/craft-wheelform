<?php
namespace wheelform\db;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\StringHelper;
use wheelform\db\Message;
use wheelform\behaviors\JsonFieldBehavior;
use wheelform\validators\JsonValidator;
use yii\validators\EmailValidator;

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
            [['name', 'to_email'], 'required', 'message' => Craft::t('wheelform', '{attribute} cannot be blank.')],
            ['name', 'string'],
            [['to_email'], 'validateToEmails'],
            [['active', 'send_email', 'recaptcha', 'save_entry'], 'boolean'],
            [['active', 'send_email', 'recaptcha'], 'default', 'value' => 0],
            [['save_entry'], 'default', 'value' => 1],
            ['options', JsonValidator::class, 'merge' => true],
        ];
    }

    public function getEntries()
    {
        return $this->hasMany(Message::className(), ['form_id' => 'id'])->orderBy(['id' => SORT_DESC]);
    }

    public function getFields()
    {
        return $this->hasMany(FormField::className(), ['form_id' => 'id'])
            ->orderBy(['order' => SORT_ASC])
            ->andOnCondition([FormField::tableName().'.active' => 1]);
    }

    public function getEntryCount(): int
    {
        if ($this->isNewRecord) {
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
            $this->addError($attribute, Craft::t('wheelform', 'To Email field is Required.'));
        }

        $emailList = StringHelper::split($this->{$attribute}, ',');

        $emailValidator = new EmailValidator();

        foreach($emailList as $to_email)
        {
            $to_email = trim($to_email);
            if($emailValidator->validate($to_email) === false)
            {
                //exit on first error
                $this->addError($attribute, Craft::t('wheelform', 'One or many of the values are not valid emails.'));
                break;
            }
        }

        return true;
    }

    public function hasNew()
    {
        $entries = $this->getEntries()->where(['read' => 0])->one();

        return (! empty($entries));
    }

    public function behaviors()
    {
        return [
            'json_field_behavior' => [
                'class' => JsonFieldBehavior::class,
                'attributes' => ['options'],
            ]
        ];
    }

}
