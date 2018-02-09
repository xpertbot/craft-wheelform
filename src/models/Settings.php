<?php
namespace Wheelform\Models;

use craft\base\Model;

class Settings extends Model
{
    public $from_email;

    public function init()
    {
        parent::init();

    }

    public function rules()
    {
        return [
            [['from_email'], 'required'],
            [['from_email'], 'email'],
        ];
    }
}
