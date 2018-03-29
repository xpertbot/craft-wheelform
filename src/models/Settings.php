<?php
namespace Wheelform\Models;

use craft\base\Model;

class Settings extends Model
{
    public $from_email;

    public $cp_label;

    public $success_message;

    public $recaptcha_public;

    public $recaptcha_secret;

    public function init()
    {
        parent::init();

    }

    public function rules()
    {
        return [
            [['from_email', 'success_message'], 'required'],
            ['from_email', 'email'],
            [['success_message', 'cp_label', 'recaptcha_public', 'recaptcha_secret'], 'string'],
        ];
    }
}
