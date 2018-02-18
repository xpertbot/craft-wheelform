<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;

class FormField extends ActiveRecord
{

    const FIELD_TYPES = [
        'text',
        'email',
        'dropdown',
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
            ['name', 'safe'],
            ['required', 'integer', 'integerOnly' => true, 'min' => 0],
            ['required', 'default', 'value' => 0],
            ['type', 'required', 'when' => function($model){
                return (in_array($model->type, self::FIELD_TYPES));
            }],
        ];
    }

    public function getForm()
    {
        return $this->hasOne(Form::classname(), ['id' => 'form_id']);
    }
}
