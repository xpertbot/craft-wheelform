<?php
namespace Wheelform\assets;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

class WheelformFrontAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@Wheelform/assets";

        $this->depends = [
            JqueryAsset::class,
        ];

        $this->js = [
            'js/front-end-form.js',
        ];

        parent::init();
    }
}
