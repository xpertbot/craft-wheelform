<?php
namespace Wheelform;

use Craft;
use craft\base\Plugin as BasePlugin;
use Wheelform\Models\Settings;

use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

class Plugin extends BasePlugin
{

    public $hasCpSettings = true;

    public $controllerNamespace = "Wheelform\\Controllers";

    public $schemaVersion = '1.0.2';

    public function init()
    {

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules[$this->id] = $this->id.'/form/index';

                //forms
                $event->rules[$this->id . '/form/new'] = $this->id.'/form/edit';
                $event->rules[$this->id . '/form/edit/<id:\d+>'] = $this->id.'/form/edit';
                $event->rules[$this->id . '/form/save'] = $this->id.'/form/save';

                //Entries
                $event->rules[$this->id . '/form/<id:\d+>/entries'] = $this->id.'/entries/index';
                $event->rules[$this->id . '/entry/<id:\d+>'] = $this->id.'/entries/view';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules[$this->id . '/message/send'] = $this->id.'/message/send';
            }
        );
    }

    public function getMailer(): Mailer
    {
        return new Mailer();
    }

    public function getCpNavItem(): Array
    {
        $ret = [
            'label' => $this->getSettings()->cp_label ? $this->getSettings()->cp_label : $this->name,
            'url' => $this->id,
        ];

        if (($iconPath = $this->cpNavIconPath()) !== null) {
            $ret['icon'] = $iconPath;
        }

        return $ret;
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): string
    {
        // Get and pre-validate the settings
        $settings = $this->getSettings();
        $settings->validate();

        return Craft::$app->view->renderTemplate('wheelform/_settings', [
            'settings' => $settings,
        ]);
    }
}
