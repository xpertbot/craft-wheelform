<?php
namespace Wheelform\Models;

use craft\base\Model;

class Settings extends Model
{
    public $toEmail;

    public $prependSender;

    public $prependSubject;

    public $allowAttachments = false;

    public $successFlashMessage;

    public function init()
    {
        parent::init();

        if ($this->prependSender === null)
        {
            $this->prependSender = \Craft::t('wheelform', 'On behalf of');
        }

        if ($this->prependSubject === null)
        {
            $this->prependSubject = \Craft::t('wheelform', 'New message from {siteName}', [
                'siteName' => \Craft::$app->getSites()->currentSite->name
            ]);
        }

        if ($this->successFlashMessage === null)
        {
            $this->successFlashMessage = \Craft::t('wheelform', 'Your message has been sent.');
        }
    }

    public function rules()
    {
        return [
            [['toEmail', 'successFlashMessage'], 'required'],
            [['toEmail', 'prependSender', 'prependSubject', 'successFlashMessage'], 'string'],
        ];
    }
}
