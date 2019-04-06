<?php
namespace wheelform\db;

use Craft;
use craft\db\ActiveRecord;
use wheelform\behaviors\JsonFieldBehavior;
use wheelform\validators\JsonValidator;
use wheelform\models\fields\Text;
use wheelform\models\fields\Textarea;
use wheelform\models\fields\Checkbox;
use wheelform\models\fields\Email;
use wheelform\models\fields\File;
use wheelform\models\fields\Hidden;
use wheelform\models\fields\ListField;
use wheelform\models\fields\Number;
use wheelform\models\fields\Radio;
use wheelform\models\fields\Select;
use wheelform\events\RegisterFieldsEvent;

class FormField extends ActiveRecord
{

    public const EVENT_REGISTER_FIELD_TYPES = "registerFieldTypes";

    protected $fields = [];

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

    public function behaviors()
    {
        return [
            'json_field_behavior' => [
                'class' => JsonFieldBehavior::class,
                'attributes' => ['options'],
            ]
        ];
    }

    public function getAllFields()
    {
        if(! empty($this->fields)) {
            return $this->fields;
        }

        $fields = [
            Text::class,
            Textarea::class,
            Checkbox::class,
            Email::class,
            File::class,
            Hidden::class,
            ListField::class,
            Number::class,
            Radio::class,
            Select::class,
        ];

        $event = new RegisterFieldsEvent([
            'fields' => $fields
        ]);

        $this->trigger(self::EVENT_REGISTER_FIELD_TYPES, $event);

        $this->fields = $event->fields;

        return $this->fields;
    }

    public function getDefaultOptions()
    {
        return [
            [
                'name' => 'required',
                'label' => 'Required',
                'type' => 'boolean',
            ],
            [
                'name' => 'index_view',
                'label' => 'Index View',
                'type' => 'boolean',
            ],
            [
                'name' => 'containerClass',
                'label' => 'Container Class',
                'type' => 'text',
            ],
            [
                'name' => 'fieldClass',
                'label' => 'Field Class',
                'type' => 'text',
            ],
        ];
    }
}
