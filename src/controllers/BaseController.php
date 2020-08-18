<?php
namespace wheelform\controllers;

use craft\web\Controller;

use wheelform\models\fields\BaseFieldType;

use wheelform\events\RegisterFieldsEvent;
use wheelform\db\FormField;
use wheelform\Plugin;

class BaseController extends Controller
{

    const EVENT_REGISTER_FIELD_TYPES = "registerFieldTypes";

    protected function getFieldTypes()
    {
        $plugin = Plugin::getInstance();
        $settings = $plugin->getSettings();

        $activeRecord = new FormField;
        $fields = $activeRecord->getFieldTypeClasses();

        $event = new RegisterFieldsEvent([
            'fields' => $fields
        ]);

        $this->trigger(self::EVENT_REGISTER_FIELD_TYPES, $event);

        $fieldTypes = [];
        foreach($event->fields as $class) {
            $field = new $class;
            if (!($field instanceof BaseFieldType)) {
                continue;
            }

            if (is_array($settings->disabled_fields) && in_array($field->type, $settings->disabled_fields)) {
                continue;
            }

            $fieldTypes[] = $field;
        }

        return $fieldTypes;
    }
}
