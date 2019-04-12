<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;
use yii\base\Model;

abstract class BaseFieldType extends Model implements FieldInterface
{
    public $name;

    public $type;

    public $required = 0;

    public $index_view = 0;

    public $order = 0;

    public $active = 1;

    public $options = [];

    public function init()
    {
        $this->options = $this->getFieldOptions();
    }

    public function getFieldOptions()
    {
        $default = [
           'label' => '',
           'containerClass' => '',
            'fieldClass' => '',
        ];

        return array_merge_recursive($default, $this->getOptions());
    }
}
