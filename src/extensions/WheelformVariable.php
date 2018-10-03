<?php
namespace wheelform\extensions;

use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use wheelform\services\WheelformService;

class WheelformVariable extends Twig_Extension implements Twig_Extension_GlobalsInterface
{

    public function getGlobals()
    {
        return array(
            'wheelform' => new WheelformService(),
        );
    }
}
