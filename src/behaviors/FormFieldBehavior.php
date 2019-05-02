<?php
namespace wheelform\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class FormFieldBehavior extends Behavior
{
    public $model = null;

    const TEXT_SCENARIO = "text";
    const TEXTAREA_SCENARIO = "textarea";
    const NUMBER_SCENARIO = "number";
    const EMAIL_SCENARIO = "email";
    const CHECKBOX_SCENARIO = "checkbox";
    const RADIO_SCENARIO = "radio";
    const HIDDEN_SCENARIO = "hidden";
    const SELECT_SCENARIO = "select";
    const FILE_SCENARIO = "file";
    const LIST_SCENARIO = "list";
    const HTML_SCENARIO = "html";

    public function events()
    {
        return [
            ActiveRecord::EVENT_INIT => function () { $this->loadModel(); },
            ActiveRecord::EVENT_AFTER_FIND => function () { $this->loadModel(); },
        ];
    }

    public function loadModel()
    {
        $type = $this->owner->getAttribute('type');
        if($type) {
            $fieldTypes = $this->getFieldTypeClasses();
            if(array_key_exists($type, $fieldTypes)) {
                $this->model = (new $fieldTypes[$type]);
            }
        }
    }

    public function getFieldTypeClasses()
    {
        return  [
            self::TEXT_SCENARIO => \wheelform\models\fields\Text::class,
            self::TEXTAREA_SCENARIO => \wheelform\models\fields\Textarea::class,
            self::CHECKBOX_SCENARIO => \wheelform\models\fields\Checkbox::class,
            self::EMAIL_SCENARIO => \wheelform\models\fields\Email::class,
            self::FILE_SCENARIO => \wheelform\models\fields\File::class,
            self::HIDDEN_SCENARIO => \wheelform\models\fields\Hidden::class,
            self::LIST_SCENARIO => \wheelform\models\fields\ListField::class,
            self::NUMBER_SCENARIO => \wheelform\models\fields\Number::class,
            self::RADIO_SCENARIO => \wheelform\models\fields\Radio::class,
            self::SELECT_SCENARIO => \wheelform\models\fields\Select::class,
            self::HTML_SCENARIO => \wheelform\models\fields\HtmlField::class,
        ];
    }
}
