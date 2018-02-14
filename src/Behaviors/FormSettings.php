<?php
namespace Wheelform\Behaviors;

class FormSettings
{
    protected $_settings;

    public function init()
    {
        parent::init();

        $this->_settings = [
            'to_email' => '',
        ];
    }

    public function getSettings()
    {
        return $this->_settings;
    }

    public function __get($setting)
    {
        if(array_key_exists($setting, $this->_settings)){
            return $this->_settings[$settings];
        }

        return null;
    }
}
