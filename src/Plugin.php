<?php
namespace wheelform;

use Craft;
use craft\base\Plugin as BasePlugin;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Utilities;
use craft\web\UrlManager;
use craft\services\Fields;
use craft\services\UserPermissions;
use wheelform\extensions\WheelformVariable;
use wheelform\fields\FormField;
use wheelform\db\Message;
use wheelform\models\Settings;
use wheelform\utilities\Tools;
use wheelform\services\permissions\WheelformPermissions;
use yii\base\Event;

class Plugin extends BasePlugin
{

    public $hasCpSettings = true;

    public $controllerNamespace = "wheelform\\controllers";

    public $schemaVersion = '2.0.0';

    public function init()
    {
        parent::init();

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'wheelform\\console\\controllers';
            //Don't do the rest that only apply for web
            return;
        }

        //Event to Register Control Panel Fields
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = FormField::class;
        });

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules[$this->id] = $this->id.'/form/index';

                //forms
                $event->rules[$this->id . '/form/new'] = $this->id.'/form/new';
                $event->rules[$this->id . '/form/edit/<id:\d+>'] = $this->id.'/form/edit';
                $event->rules[$this->id . '/form/save'] = $this->id.'/form/save';
                $event->rules[$this->id . '/form/delete'] = $this->id.'/form/delete';

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

        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function($event){
            $event->permissions[$this->name] = WheelformPermissions::getAllPermissions();
        });

        if (Craft::$app->request->getIsSiteRequest()) {
            // Add in our Twig extension
            $wheelform = new WheelformVariable();
            Craft::$app->view->registerTwigExtension($wheelform);
        }
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
