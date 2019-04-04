<?php
namespace wheelform\db;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\DateTimeHelper;

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
        return $this->hasOne(Form::class, ['id' => 'form_id']);
    }

    public function getValue()
    {
        return $this->hasMany(MessageValue::class, ['message_id' => 'id']);
    }

    public function getField()
    {
        return $this->hasMany(FormField::class, ['id' => 'field_id'])
            ->via('value');
    }

    public static function getUnreadCount()
    {
        return self::find()->where(['read' => 0, ])->count();
    }

    public function getValueById(int $valueId)
    {
        return $this->getValue()->where(['field_id' => $valueId])->one();
    }

    public function afterFind()
    {
        $this->dateCreated = DateTimeHelper::toDateTime($this->dateCreated, false);

        parent::afterFind();
    }
}
