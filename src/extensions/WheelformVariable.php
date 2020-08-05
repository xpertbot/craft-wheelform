<?php
namespace wheelform\extensions;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use wheelform\services\WheelformService;

class WheelformVariable extends AbstractExtension implements GlobalsInterface
{

    public function getGlobals()
    {
        return array(
            'wheelform' => new WheelformService(),
        );
    }
}
