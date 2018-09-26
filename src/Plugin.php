<?php
namespace wheelform;

use Craft;
use craft\base\Plugin as BasePlugin;
use wheelform\fields\FormPickerField;
use wheelform\models\Settings;
use wheelform\models\Message;
use wheelform\utilities\Tools;
use wheelform\variables\WheelformVariable;

use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;

class Plugin extends BasePlugin
{

    public $hasCpSettings = true;

    public $controllerNamespace = "wheelform\\controllers";

    public $schemaVersion = '1.6.0';

    public function init()
    {
        parent::init();

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

        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function($event)
        {
            if(is_array($event->types))
            {
                $event->types[] = Tools::class;
            }
            return $event;
        });

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('wheelform', WheelformVariable::class);
        });

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = FormPickerField::class;
        });
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
            'badgeCount' => Message::getUnreadCount(),
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
        $volumeList = Craft::$app->getVolumes()->getAllVolumes();
        $emptyLabel =  Craft::t("wheelform", '-- Select Volume --');
        $volumes = [
            '' => $emptyLabel,
        ];
        if(! empty($volumeList))
        {
            foreach($volumeList as $v)
            {
                $volumes[$v->id] = $v->name;
            }
        }

        return Craft::$app->view->renderTemplate('wheelform/_settings', [
            'settings' => $settings,
            'volumes' => $volumes,
        ]);
    }
}
