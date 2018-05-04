<?php
namespace wheelform\assets;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

class WheelformFrontAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@wheelform/assets";

        $this->depends = [
            JqueryAsset::class,
        ];

        $this->js = [
            'js/front-end-form.js',
        ];

        parent::init();
    }
}
