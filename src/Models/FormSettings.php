<?php
namespace Wheelform\Models;

use craft\base\Model;

class FormSettings extends Model
{
    public $to_email;
    public $prepend_sender;
    public $prepend_subject;
    public $allow_attachments;
    public $success_message;

    public function rules()
    {
        return [
            [['to_email', 'prepend_subject'], 'required'],
            [['to_email'], 'email'],
            [['name', 'prepend_sender', 'prepend_subject', 'allow_attachments', 'success_message'], 'safe'],
        ];
    }
}
