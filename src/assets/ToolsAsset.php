<?php
namespace wheelform\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * Asset bundle for the DB Backup utility
 */
class ToolsAsset extends AssetBundle
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
            'js/tools.js',
        ];

        $this->css = [
        ];

        parent::init();
    }
}
