<?php
namespace Wheelform\Models;

use Craft;
use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
class Message extends ActiveRecord
{

    public static function tableName(): String
    {
        return '{{%wheelform_messages}}';
    }

    public function rules(): Array
    {
        return [
            ['form_id', 'required', 'message' => Craft::t('wheelform', 'Form Id cannot be blank.')],
            ['form_id', 'integer', 'message' => Craft::t('wheelform', 'Form Id must be a number.')],
            ['read', 'integer', 'message' => Craft::t('wheelform', '{attribute} must be a number.')],
            [['form_id', 'read'], 'filter', 'filter' => 'intval'],
        ];
    }

    public function getForm()
    {
        return $this->hasOne(Form::classname(), ['id' => 'form_id']);
    }

    public function getValue()
    {
        return $this->hasMany(MessageValue::className(), ['message_id' => 'id']);
    }
}
