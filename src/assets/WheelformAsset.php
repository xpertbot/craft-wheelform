<?php
namespace Wheelform\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class WheelformAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = "@Wheelform/assets";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/cp-edit-form.js',
        ];

        parent::init();
    }
}
