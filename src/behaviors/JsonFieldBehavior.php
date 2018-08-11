<?php
namespace wheelform\behaviors;

use yii\base\Behavior;
use craft\db\ActiveRecord;
use wheelform\models\helpers\JsonField;

class JsonFieldBehavior extends Behavior
{

    public $attributes = [];

    public $emptyValue;

    public $encodeBeforeValidation = true;


    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT            => function () { $this->initialization(); },
            ActiveRecord::EVENT_AFTER_FIND      => function () { $this->decode(); },
            ActiveRecord::EVENT_BEFORE_INSERT   => function () { $this->encode(); },
            ActiveRecord::EVENT_BEFORE_UPDATE   => function () { $this->encode(); },
            ActiveRecord::EVENT_AFTER_INSERT    => function () { $this->decode(); },
            ActiveRecord::EVENT_AFTER_UPDATE    => function () { $this->decode(); },
            ActiveRecord::EVENT_BEFORE_VALIDATE => function () {
                if ($this->encodeBeforeValidation) {
                    $this->encodeValidate();
                }
            },
            ActiveRecord::EVENT_AFTER_VALIDATE  => function () {
                if ($this->encodeBeforeValidation) {
                    $this->decode();
                }
            },
        ];
    }


    protected function initialization()
    {
        foreach ($this->attributes as $attribute) {
            $this->owner->setAttribute($attribute, new JsonField());
        }
    }

    protected function decode()
    {
        foreach ($this->attributes as $attribute) {
            $value = $this->owner->getAttribute($attribute);
            if (!$value instanceof JsonField) {
                $value = new JsonField($value);
            }
            $this->owner->setAttribute($attribute, $value);
        }
    }

    protected function encode()
    {
        foreach ($this->attributes as $attribute) {
            $field = $this->owner->getAttribute($attribute);
            if (!$field instanceof JsonField) {
                $field = new JsonField($field);
            }
            $this->owner->setAttribute($attribute, (string)$field ?: $this->emptyValue);
        }
    }

    protected function encodeValidate()
    {
        foreach ($this->attributes as $attribute) {
            $field = $this->owner->getAttribute($attribute);
            if ($field instanceof JsonField) {
                $this->owner->setAttribute($attribute, (string)$field ?: null);
            }
        }
    }
}
