<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
class MessageValue extends Activerecord
{
    const TEXT_SCENARIO = "text";
    const NUMBER_SCENARIO = "number";
    const EMAIL_SCENARIO = "email";
    const CHECKBOX_SCENARIO = "checkbox";
    const FILE_SCENARIO = "file";

    public static function tableName(): String
    {
        return '{{%wheelform_message_values}}';
    }

    public function rules(): Array
    {
        return [
            [['field_id'], 'required'],
            [['message_id', 'field_id'], 'integer'],
            [['message_id', 'field_id'], 'filter', 'filter' => 'intval'],
            ['value', 'required', 'when' => function($model){
                return (bool)$model->field->required;
            }, 'message' => $this->field->getLabel().' cannot be blank.'],
            ['value', 'string', 'on' => self::TEXT_SCENARIO],
            ['value', 'email', 'on' => self::EMAIL_SCENARIO],
            ['value', 'number', 'on' => self::NUMBER_SCENARIO],
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

    public function getFileName()
    {

        if($this->field->type == self::FILE_SCENARIO)
        {
            $file = json_decode($this->value);
            return isset($file->name) ? $file->name : '';
        }

        return null;
    }
}
