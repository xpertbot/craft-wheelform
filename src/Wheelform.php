<?php
namespace Wheelform;

use Craft;
use craft\base\Plugin;
use Wheelform\models\Settings;

class Wheelform extends Plugin
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
        $settings = $this->getSettings();
        $settings->validate();

        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate('wheelform/_settings', [
            'settings' => $settings,
            'overrides' => array_keys($overrides),
        ]);
    }
}
