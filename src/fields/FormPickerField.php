<?php

namespace wheelform\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use wheelform\models\Form;


class FormPickerField extends Field implements PreviewableFieldInterface
{
    public static function displayName(): string
    {
        return \Craft::t('wheelform', 'Form Picker');
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $formRecords = Form::find()->orderBy(['name' => 'ASC'])->all();
        $formOptions = [];
        foreach ($formRecords as $record)
        {
            $formOptions[] = [
                'label' => $record->name,
                'value' => $record->id,
            ];
        }

        return Craft::$app->getView()->renderTemplate('_includes/forms/select', [
            'name' => $this->handle,
            'value' => $value,
            'options' => $formOptions,
        ]);
    }
}