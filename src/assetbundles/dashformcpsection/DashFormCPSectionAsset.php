<?php
/**
 * Wheel Form plugin for Craft CMS 3.x
 *
 * Free Form Builder with Database Integration
 *
 * @link      https://wheelinteractive.com
 * @copyright Copyright (c) 2018 Wheel Interactive
 */

namespace Dashform\assetbundles\dashformcpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Wheel Interactive
 * @package   DashForm
 * @since     0.1
 */
class DashFormCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@xpertbot/dashform/assetbundles/dashformcpsection/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/DashForm.js',
        ];

        $this->css = [
            'css/DashForm.css',
        ];

        parent::init();
    }
}
