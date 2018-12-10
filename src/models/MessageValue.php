<?php
namespace wheelform\models;

use Craft;
use craft\helpers\Html;
use craft\db\ActiveRecord;
use craft\helpers\Template;

//Using Active Record because it extends Models.
class MessageValue extends ActiveRecord
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
                }, 'message' => $this->field->getLabel().Craft::t('wheelform', ' cannot be blank.')
            ],
            ['value', 'string', 'on' => [
                    self::TEXT_SCENARIO,
                    self::TEXTAREA_SCENARIO,
                    self::HIDDEN_SCENARIO,
                    self::SELECT_SCENARIO,
                    self::RADIO_SCENARIO,
                ],
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be valid characters.')
            ],
            ['value', 'email', 'on' => self::EMAIL_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' is not a valid email address.')],
            ['value', 'number', 'on' => self::NUMBER_SCENARIO,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' must be a number.')],
            ['value', 'file', 'on' => self::FILE_SCENARIO],
            ['value', function($attribute, $params, $validator){
                if(! is_array($this->$attribute)) {
                    $this->addError($this->field->getLabel().Craft::t('wheelform', ' must be an array.'));
                }
            }, 'on' => self::LIST_SCENARIO
        ],
            ['value', 'each', 'rule' => ['string'], 'on' => [
                    self::CHECKBOX_SCENARIO,
                    self::LIST_SCENARIO,
                ]
            ],
            ['value', 'in', 'range' => function(){
                    return (empty($this->field->options['items']) ? [] : $this->field->options['items']);
                }, 'when' => function($model){
                    return boolval($model->field->options['validate']);
                },
                "allowArray" => true,
                'message' => $this->field->getLabel().Craft::t('wheelform', ' has invalid options.')
            ],
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
        if($this->field->type == self::FILE_SCENARIO) {
            $file = json_decode($this->value);
            if(! empty($file->assetId)) {
                $asset = Craft::$app->getAssets()->getAssetById($file->assetId);
                $title = Html::encode($asset->title);
                $url = $asset->getUrl();
                if($url) {
                    $anchor = '<a href="' . $url . '" target="_blank">' . $title . '</a>';
                    return Template::raw($anchor);
                }
                return $title;
            }

            return isset($file->name) ? $file->name : '';
        } elseif($this->field->type == self::LIST_SCENARIO) {
            return json_decode($this->value);
        } else {
            return empty($this->value) ? '' : $this->value;
        }
    }

    public function beforeSave($insert)
    {
        if($this->field->type == self::CHECKBOX_SCENARIO && ! empty($this->value))
        {
            $this->value = implode(', ', $this->value);
        }

        if($this->field->type == self::LIST_SCENARIO && ! empty($this->value))
        {
            $this->value = json_encode($this->value);
        }

        return parent::beforesave($insert);
    }
}
