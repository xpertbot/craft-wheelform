<?php
namespace wheelform\services;

use Craft;
use wheelform\Plugin as Wheelform;
use wheelform\services\MessageService;
use yii\base\BaseObject;
use yii\base\ErrorException;

class WheelformService extends BaseObject
{

    private $settings;

    public function init()
    {
        $this->settings = Wheelform::getInstance()->getSettings();
    }

    public function getForm(array $options = [])
    {

        if(empty($options['id'])) {
            throw new ErrorException("Form ID is required.");
        }

        return (new FormService($options));
    }

    public function getSettings($key = '')
    {

        if(! empty($key)) {
            return (empty($this->settings->{$key}) ? '' : $this->settings->{$key});
        }

        return $this->settings;

    }

    public function getLastSubmission()
    {
        $id = Craft::$app->getSession()->getFlash('wheelformLastSubmissionId');
        if (!$id) {
            return null;
        }

        return (new MessageService($id));
    }
}
