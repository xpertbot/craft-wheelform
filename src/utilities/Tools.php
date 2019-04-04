<?php
namespace wheelform\utilities;

use Craft;
use craft\base\Utility;
use wheelform\db\Form;

use wheelform\assets\ToolsAsset;

class Tools extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('wheelform', 'Form Tools');
    }

    public static function id(): string
    {
        return 'wheelform-tools';
    }

    public static function iconPath()
    {
        return Craft::getAlias('@wheelform/icon.svg');
    }

    public static function contentHtml(): string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(ToolsAsset::class);
        $view->registerJs('new Craft.WheelformExport(\'export-form\');');
        $view->registerJs('new Craft.WheelformExportFields(\'export-fields\');');
        $view->registerJs('new Craft.WheelformImportFields(\'import-fields\');');

        $formRecords = Form::find()->where(['active' => 1])->orderBy(['name' => 'ASC'])->all();
        $formOptions = [];
        foreach($formRecords as $record)
        {
            $formOptions[] = [
                'label' => $record->name,
                'value' => $record->id,
            ];
        }

        return $view->renderTemplate('wheelform/utilities/tools', ['formOptions' => $formOptions]);
    }
}
