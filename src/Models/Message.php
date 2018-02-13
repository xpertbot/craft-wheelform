<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
class Message extends Activerecord
{

    public $values;

    public $fromEmail;

    public $subject;

    public $message;

    public $attachment;

    public function rules()
    {
        return [
            [['fromEmail', 'message'], 'required'],
        ];
    }
}
