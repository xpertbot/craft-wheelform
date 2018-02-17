<?php
namespace Wheelform\Helpers;

use yii\base\Model;

class FormField extends Model implements \JsonSerializable
{
    public $type;
    public $name;
    public $required;

    private $_defaultTypes = [
        'text',
        'dropdown',
        'email',
    ];
    private $_errors = [];

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function rules()
    {
        return [
            ['name', 'required'],
            ['type', 'required', 'when' => function($model){
                return (in_array($model->type, [
                    'text',
                    'dropdown',
                    'email',
                ]));
            }],
        ];
    }
}
