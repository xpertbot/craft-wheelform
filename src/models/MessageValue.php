<?php
namespace wheelform\models;

use Craft;
use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
class MessageValue extends ActiveRecord
{
    const TEXT_SCENARIO = "text";
    const NUMBER_SCENARIO = "number";
    const EMAIL_SCENARIO = "email";
    const CHECKBOX_SCENARIO = "checkbox";
    const RADIO_SCENARIO = "radio";
    const HIDDEN_SCENARIO = "hidden";
    const SELECT_SCENARIO = "select";
    const FILE_SCENARIO = "file";

    public static function tableName(): String
    {
        return '{{%wheelform_message_values}}';
    }

    public function rules(): Array
    {
        return [
            [['field_id'], 'required', 'message' => Craft::t('wheelform', 'Field ID cannot be blank.')],
            [['message_id', 'field_id'], 'integer', 'message' => Craft::t('wheelform', '{attribute} must be a number.')],
            [['message_id', 'field_id'], 'filter', 'filter' => 'intval'],
            ['value', 'required', 'when' => function($model){
                return (bool)$model->field->required;
            }, 'message' => $this->field->getLabel().Craft::t('wheelform', ' cannot be blank.')],
            ['value', 'string', 'on' => self::TEXT_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be valid characters.')],
            ['value', 'string', 'on' => self::HIDDEN_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be valid characters.')],
            ['value', 'string', 'on' => self::SELECT_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be valid characters.')],
            ['value', 'string', 'on' => self::RADIO_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be valid characters.')],
            ['value', 'email', 'on' => self::EMAIL_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' is not a valid email address.')],
            ['value', 'number', 'on' => self::NUMBER_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be a number.')],
            ['value', 'file', 'on' => self::FILE_SCENARIO],
            ['value', 'each', 'rule' => ['string'], 'on' => self::CHECKBOX_SCENARIO],
        ];
    }

    public function getMessage()
    {
        return $this->hasOne(Message::classname(), ['id' => 'message_id']);
    }

    public function getField()
    {
        return $this->hasOne(FormField::classname(), ['id' => 'field_id']);
    }

    public function getValue()
    {
        if($this->field->type == self::FILE_SCENARIO)
        {
            $file = json_decode($this->value);
            return isset($file->name) ? $file->name : '';
        }
        else
        {
            return empty($this->value) ? '' : $this->value;
        }
    }

    public function beforeSave($insert)
    {
        if($this->field->type == self::CHECKBOX_SCENARIO && ! empty($this->value))
        {
            $this->value = implode(', ', $this->value);
        }

        return parent::beforesave($insert);
    }
}
