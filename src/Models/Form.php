<?php
namespace Wheelform\Models;

use craft\base\Model;

class Form extends Model
{
    public $name;
    public $settings;

    public function init()
    {
        parent::init();

        if ($this->name === null)
        {
            $this->name = \Craft::t('wheelform', 'Contact Form');
        }

    }

    public function rules()
    {
        return [
            [['name', 'settings'], 'required'],
            [['name', 'settings'], 'safe'],
        ];
    }
}
