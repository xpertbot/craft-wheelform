<?php
namespace wheelform\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Asset bundle for the DB Backup utility
 */
class ExportAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = '@wheelform/assets';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/export.js',
        ];

        $this->css = [
        ];

        parent::init();
    }
}
