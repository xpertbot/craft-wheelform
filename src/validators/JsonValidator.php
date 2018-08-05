<?php
namespace wheelform\validators;

use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\validators\Validator;
use wheelform\models\helpers\JsonField;

class JsonValidator extends Validator
{

    public $merge = false;

    public $errorMessages = [];

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!$value instanceof JsonField) {
            try {
                $new = new JsonField($value);
                if ($this->merge) {
                    /** @var BaseActiveRecord $model */
                    $old = new JsonField($model->getOldAttribute($attribute));
                    $new = new JsonField(array_merge($old->toArray(), $new->toArray()));
                }
                $model->$attribute = $new;
            } catch (InvalidParamException $e) {
                $this->addError($model, $attribute, $this->getErrorMessage($e));
                $model->$attribute = new JsonField();
            }
        }
    }

    protected function getErrorMessage($exception)
    {
        $code = $exception->getCode();
        if (isset($this->errorMessages[$code])) {
            return $this->errorMessages[$code];
        }
        return $exception->getMessage();
    }
}
