<?php
namespace wheelform\fields;

use Craft;
use craft\base\Field;
use craft\base\ElementInterface;
use yii\db\Schema;
use wheelform\db\Form;
use wheelform\services\WheelformService;
use wheelform\services\FormService;


class FormField extends Field
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Wheelform';
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_INTEGER;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): string
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($service, ElementInterface $element = null): string
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
            'value' => empty($service->id) ? null : $service->id,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($service, ElementInterface $element = null)
    {
        $value = $service;
        if($service) {
            $value = $service->id;
        }

        return parent::serializeValue($value);
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if($value instanceof FormService) {
            // FormService already initialized
            return $value;
        } elseif(is_numeric($value)) {
            $id = intval($value);
            $form = Form::find()->where(['id' => $id])->one();
            if($form) {
                return (new WheelformService)->getForm(['id' => $form->id]);
            }
        }

        return null;
    }
}
