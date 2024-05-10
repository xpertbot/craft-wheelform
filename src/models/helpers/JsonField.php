<?php
namespace wheelform\models\helpers;

use InvalidArgumentException;
use yii\base\Arrayable;
use yii\helpers\Json;

class JsonField implements \ArrayAccess, Arrayable
{

    protected $value;

    public function __construct($value = [])
    {
        $this->set($value);
    }

    public function __toString()
    {
        return $this->value ? Json::encode($this->value) : '';
    }

    public function set($value)
    {
        if(is_null($value) || empty($value))
        {
            $value = [];
        }
        elseif(is_string($value))
        {
            $value = Json::decode($value);
            if(! is_array($value))
            {
                throw new InvalidArgumentException('Value is scalar');
            }
        }

        if(! is_array($value))
        {
            throw new InvalidArgumentException('Value is not array');
        }
        else
        {
            $this->value = $value;
        }
    }

    public function fields()
    {
        return [];
    }

    public function extraFields()
    {
        return [];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return empty($fields) ? $this->value : array_intersect_key($this->value, array_flip($fields));
    }

    public function isEmpty()
    {
        return !$this->value;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->value[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function &offsetGet(mixed $offset) : mixed
    {
        $null = null;
        if(isset($this->value[$offset]))
        {
            return $this->value[$offset];
        }
        else
        {
            return $null;
        }
    }

    /**
     * @inheritdoc
     */
    public function offsetSet(mixed $offset, mixed $set): void
    {
        if($offset === null)
        {
            $this->value[] = $set;
        }
        else
        {
            $this->value[$offset] = $set;
        }
    }
    /**
     * @inheritdoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->value[$offset]);
    }

}
