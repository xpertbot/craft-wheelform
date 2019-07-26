<?php
namespace wheelform\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class WheelformCpAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@wheelform/assets";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/wheelform-bundle.js?v2.2.2',
        ];

        $this->css = [
            'https://use.fontawesome.com/releases/v5.0.13/css/all.css',
            'css/cp-wheelform.css',
        ];

        parent::init();
    }
}
