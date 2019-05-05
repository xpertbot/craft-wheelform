<?php
namespace wheelform\db;

use Craft;
use craft\db\ActiveRecord;
use wheelform\behaviors\JsonFieldBehavior;
use wheelform\validators\JsonValidator;
use wheelform\behaviors\FormFieldBehavior;

class FormField extends ActiveRecord
{

    const TEXT_SCENARIO = "text";
    const TEXTAREA_SCENARIO = "textarea";
    const NUMBER_SCENARIO = "number";
    const EMAIL_SCENARIO = "email";
    const CHECKBOX_SCENARIO = "checkbox";
    const RADIO_SCENARIO = "radio";
    const HIDDEN_SCENARIO = "hidden";
    const SELECT_SCENARIO = "select";
    const FILE_SCENARIO = "file";
    const LIST_SCENARIO = "list";
    const HTML_SCENARIO = "html";

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
            [['required', 'index_view', 'active', 'order'], 'integer', 'integerOnly' => true, 'min' => 0,
                'message' => Craft::t('wheelform', '{attribute} must be a number.')],
            [['active'], 'default', 'value' => 1],
            [['required', 'index_view'], 'default', 'value' => 0],
            ['type', 'in', 'range' => array_keys($this->getFieldTypeClasses())],
            ['options', JsonValidator::class],
        ];
    }

    public function getForm()
    {
        return $this->hasOne(Form::classname(), ['id' => 'form_id']);
    }

    public function beforeSave($insert)
    {
        $this->name = strtolower($this->name);
        $this->name = trim(preg_replace('/[^\w-]/', "", $this->name));
        return parent::beforeSave($insert);
    }

    public function getLabel()
    {
        if (! empty($this->options['label'])) {
            return \Craft::t('site', $this->options['label']);
        }
        $label = trim(str_replace(['_', '-'], " ", $this->name));
        $label = ucfirst($label);
        return $label;
    }

    public function getValues()
    {
        return $this->hasMany(MessageValue::className(), ['field_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'json_field_behavior' => [
                'class' => JsonFieldBehavior::class,
                'attributes' => ['options'],
            ],
            FormFieldBehavior::class,
        ];
    }

    public static function getVisualFields()
    {
        return [
            self::HTML_SCENARIO,
        ];
    }
}
