<?php
namespace wheelform\controllers;

use craft\web\Controller;

use wheelform\models\fields\BaseFieldType;
use wheelform\models\fields\Text;
use wheelform\models\fields\Textarea;
use wheelform\models\fields\Checkbox;
use wheelform\models\fields\Email;
use wheelform\models\fields\File;
use wheelform\models\fields\Hidden;
use wheelform\models\fields\ListField;
use wheelform\models\fields\Number;
use wheelform\models\fields\Radio;
use wheelform\models\fields\Select;

use wheelform\events\RegisterFieldsEvent;

class BaseController extends Controller
{

    public const EVENT_REGISTER_FIELD_TYPES = "registerFieldTypes";

    protected function getFieldTypes()
    {
        $fields = [
            Text::class,
            Textarea::class,
            Checkbox::class,
            Email::class,
            File::class,
            Hidden::class,
            ListField::class,
            Number::class,
            Radio::class,
            Select::class,
        ];

        $event = new RegisterFieldsEvent([
            'fields' => $fields
        ]);

        $this->trigger(self::EVENT_REGISTER_FIELD_TYPES, $event);

        $fieldTypes = [];
        foreach($event->fields as $class) {
            $field = new $class;
            if($field instanceof BaseFieldType) {
                $fieldTypes[] = $field;
            }
        }

        return $fieldTypes;
    }

    protected function getFieldByType($type)
    {
        $fields = $this->getFieldByTypes();
        foreach($fields as $field) {
            if($field->type == $type) {
                return $field;
            }
        }

        return null;
    }
}
