<?php
namespace Wheelform\Models;

use craft\base\Model;

class FormFields extends Model
{
    public $form_id;
    protected $_values = [];

    public function getValues(): Array
    {
        return $this->$_values;
    }

    public function rules()
    {
        return [
            [['form_id'], 'required'],
        ];
    }
}
