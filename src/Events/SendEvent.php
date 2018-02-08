<?php
namespace Wheelform\Events;

use craft\contactform\models\Submission;
use craft\mail\Message;
use yii\base\Event;

class SendEvent extends Event
{

    public $submission;

    public $message;

    public $toEmails;

    public $isSpam = false;
}
