<?php
namespace Wheelform\Models;

use craft\base\Model;
use craft\web\UploadedFile;

class Message extends Model
{

    public $values;

    public $fromEmail;

    public $subject;

    public $message;

    public $attachment;

    public function attributeLabels()
    {
        return [
            'values' => \Craft::t('wheelform', 'Your Name'),
            'fromEmail' => \Craft::t('wheelform', 'Your Email'),
            'message' => \Craft::t('wheelform', 'Message'),
            'subject' => \Craft::t('wheelform', 'Subject'),
        ];
    }

    public function rules()
    {
        return [
            [['fromEmail', 'message'], 'required'],
        ];
    }
}
