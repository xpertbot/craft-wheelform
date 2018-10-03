<?php
namespace wheelform\services;

use Craft;
use wheelform\Plugin as Wheelform;
use yii\base\BaseObject;
use yii\base\ErrorException;

class WheelformService extends BaseObject
{
    private $formService;

    private $settings;

    public function init()
    {
        $this->settings = Wheelform::getInstance()->getSettings();
    }

    public function getForm(array $options = [])
    {
        if(! empty($this->formService)) {
            throw new ErrorException("Form already loaded.");
        }

        if(empty($options['id'])) {
            throw new ErrorException("Form ID is required.");
        }

        $this->formService = new FormService($options);
        return $this->formService;
    }

    public function getSettings($key = '')
    {

        if(! empty($key)) {
            return (empty($this->settings->{$key}) ? '' : $this->settings->{$key});
        }

        return $this->settings;

    }
}
