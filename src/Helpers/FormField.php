<?php
namespace Wheelform\Helpers;

class FormField implements \JsonSerializable
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

    public function __construct(Array $settings = [])
    {
        if(! empty($settings))
        {
            if(empty($settings['type']) || empty($settings['name']) )
            {
                $this->_errors['fields'] = "Field Type and Name are required";
                return $this;
            }
            if(! in_array($settings['type'], $this->_defaultTypes))
            {
                $this->_errors['fields'] = $settings['name']. " has not supported field type.";
                return $this;
            }
            $this->type = $settings['type'];
            $this->name = $settings['name'];
            $this->required = $settings['required'];
        }
    }

    public function jsonSerialize() {
        $return = [];
        $return['type'] = $this->type;
        $return['name'] = $this->name;
        $return['required'] = $this->required;

        return $return;

    }

    public function validate()
    {
        return (empty($this->_errors));
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}
