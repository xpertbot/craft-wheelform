<?php
namespace Wheelform;

use Craft;
use craft\base\Plugin as BasePlugin;
use Wheelform\Models\Settings;

class Plugin extends BasePlugin
{

    public $hasCpSettings = true;

    public function getMailer(): Mailer
    {
        return $this->get('Mailer');
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
