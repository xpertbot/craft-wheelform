<?php
namespace Wheelform\Behaviors;

class FormFields
{
    protected $_values = [];

    public function getValues(): Array
    {
        return $this->$_values;
    }

}
