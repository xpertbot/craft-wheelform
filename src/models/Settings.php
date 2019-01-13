<?php
namespace wheelform\models;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    public $from_email;

    public $cp_label;

    public $success_message;

    public $volume_id;

    public $recaptcha_version;

    public $recaptcha_public;

    public $recaptcha_secret;

    public function init()
    {
        parent::init();

    }

    public function rules()
    {
        return [
            [['from_email', 'success_message'], 'required', 'message' => Craft::t('wheelform', 'From email / Success Message cannot be blank.')],
            ['recaptcha_version', 'in', 'range' => ["2", "3"]],
            ['recaptcha_version', 'default', 'value' => 2],
            ['from_email', 'email', 'message' => Craft::t('wheelform', 'From email is not a valid email address.')],
            [['success_message', 'cp_label', 'recaptcha_public', 'recaptcha_secret'], 'string'],
            [['volume_id'], 'integer'],
        ];
    }
}
