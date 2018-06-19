<?php
namespace wheelform\utilities;

use Craft;
use craft\base\Utility;

use wheelform\assets\ExportAsset;

class Export extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('wheelform', 'Form Export');
    }

    public static function id(): string
    {
        return 'wheelform-export';
    }

    public static function iconPath()
    {
        return Craft::getAlias('@wheelform/icon.svg');
    }

    public static function contentHtml(): string
    {
        $view = Craft::$app->getView();

        $view->registerAssetBundle(ExportAsset::class);
        $view->registerJs('new Craft.WheelformExport(\'export-form\');');

        return $view->renderTemplate('wheelform/utilities/export');
    }
}
