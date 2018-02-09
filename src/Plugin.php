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
