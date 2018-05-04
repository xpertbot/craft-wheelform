<?php
namespace wheelform\models;

use Craft;
use craft\db\ActiveRecord;

class FormField extends ActiveRecord
{

    const FIELD_TYPES = [
        'text',
        'email',
        'number',
        'checkbox',
        'radio',
        'hidden',
        'select',
        'file',
    ];

    public static function tableName(): String
    {
        return '{{%wheelform_form_fields}}';
    }

    public function rules()
    {
        return [
            ['id', 'unique', 'message' => Craft::t('wheelform', '{attribute}:{value} already exists!')],
            ['name', 'required', 'message' => Craft::t('wheelform', 'Name cannot be blank.')],
            ['name', 'string'],
            ['form_id', 'integer', 'message' => Craft::t('wheelform', 'Form Id must be a number.')],
            [['required', 'index_view', 'active'], 'integer', 'integerOnly' => true, 'min' => 0, 'message' => Craft::t('wheelform', '{attribute} must be a number.')],
            [['active'], 'default', 'value' => 1],
            [['required', 'index_view'], 'default', 'value' => 0],
            ['type', 'in', 'range' => self::FIELD_TYPES],
        ];
    }

    public function getForm()
    {
        return $this->hasOne(Form::classname(), ['id' => 'form_id']);
    }

    public function beforeSave($insert)
    {
        $this->name = strtolower($this->name);
        $this->name = trim(preg_replace('/[^a-z0-9_]/', "_", $this->name));
        return parent::beforeSave($insert);
    }

    public function getLabel()
    {
        $label = trim(str_replace('_', " ", $this->name));
        $label = ucfirst($label);
        return $label;
    }

    public function getValues()
    {
        return $this->hasMany(MessageValue::className(), ['field_id' => 'id']);
    }
}
