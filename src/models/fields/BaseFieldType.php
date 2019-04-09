<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;
use yii\base\Model;

abstract class BaseFieldType extends Model implements FieldInterface
{
    public $name;

    public $type;

    public $required = 0;

    public $index_view;

    public $order = 0;

    public $active = 1;

    public $options = [];

    public function getDefaultOptions()
    {
        return [
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
