<?php
namespace Wheelform\Models;

use Craft;
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
            [['from_email', 'success_message'], 'required', 'message' => Craft::t('wheelform', 'From email cannot be blank.')],
            ['from_email', 'email', 'message' => Craft::t('wheelform', 'From email is not a valid email address.')],
            [['success_message', 'cp_label', 'recaptcha_public', 'recaptcha_secret'], 'string'],
        ];
    }
}
