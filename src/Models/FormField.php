<?php
namespace Wheelform\Models;

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
            ['name', 'required'],
            ['form_id', 'integer'],
            ['required', 'integer', 'integerOnly' => true, 'min' => 0],
            ['required', 'default', 'value' => 0],
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
}
