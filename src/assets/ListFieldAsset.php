<?php
namespace wheelform\assets;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

class ListFieldAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@wheelform/assets";

        $this->js = [
            'js/list-field.js',
        ];

        $this->depends = [
            JqueryAsset::class,
        ];

        parent::init();
    }
}
