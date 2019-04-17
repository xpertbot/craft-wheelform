<?php
namespace wheelform\models\fields;

use Craft;

class ListField extends BaseFieldType
{
    public $name = "List";

    public $type = "list";

    public function getFieldRules()
    {
        return [
            ['value', function($attribute, $params, $validator)
                {
                    if(! is_array($this->$attribute)) {
                        $this->addError($this->label . Craft::t('wheelform', ' must be an array.'));
                    }
                }
            ],
            [
                'value', 'each', 'rule' => ['string']
            ],
        ];
    }

    public function getConfig()
    {
        return [
        ];
    }
}
