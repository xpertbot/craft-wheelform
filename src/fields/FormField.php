<?php
namespace wheelform\fields;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use yii\db\Schema;
use wheelform\models\Form;
use wheelform\services\WheelformService;


class FormField extends Field
{
    public static function displayName(): string
    {
        return 'Wheelform';
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_INTEGER;
    }

    public function getSettingsHtml(): string
    {
        return "";
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $forms = Form::find()->select('id,name')->where(['active' => 1])->all();
        $formOptions = [];
        $formOptions[] = [
            'label' => " -- ",
            'value' => 0,
        ];

        foreach($forms as $form) {
            $formOptions[] = [
                'label' => $form->name,
                'value' => $form->id,
            ];
        }

        return Craft::$app->getView()->renderTemplate('wheelform/_includes/_form_field', [
            'formOptions' => $formOptions,
            'formName' => $this->handle,
            'value' => $value,
        ]);
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        return intval($value);
    }
}
