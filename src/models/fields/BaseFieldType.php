<?php
namespace wheelform\models\fields;

use wheelform\interfaces\FieldInterface;
use yii\base\Model;

abstract class BaseFieldType extends Model implements FieldInterface
{
    public $class;

    public $name;

    public $type;

    public $required = 0;

    public $index_view = 0;

    public $order = 0;

    public $active = 1;

    public $options = [];

    public $config = [];

    public $fieldLabel = '';

    public function init()
    {
        $this->class = get_class($this);
        $this->config = $this->getFieldConfig();
    }

    final public function getFieldConfig()
    {
        $default = [
            [
                'name' => 'label',
                'type' => 'text',
                'label' => 'Label',
                'value' => '',
            ],
            [
                'name' => 'containerClass',
                'type' => 'text',
                'label' => 'Container Class',
                'value' => '',
            ],
            [
                'name' => 'fieldClass',
                'type' => 'text',
                'label' => 'Field Class',
                'value' => '',
            ],
        ];

        return array_merge_recursive($default, $this->getConfig());
    }
}
