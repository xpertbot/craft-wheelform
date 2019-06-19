<?php
namespace wheelform\services;

use craft\helpers\Template;
use wheelform\Plugin as Wheelform;
use yii\helpers\Html;

class RecaptchaV3Service extends BaseService
{
    public function __invoke($attributes)
    {
        $settings = Wheelform::getInstance()->getSettings();
        $recaptcha_public = empty($settings->recaptcha_public) ? "" : \Craft::parseEnv($settings->recaptcha_public);
        if(empty($recaptcha_public)) {
            return NULL;
        }
        if(array_key_exists('action', $attributes)) {
            $action = $attributes['action'];
        } else {
            $uri = \Craft::$app->getRequest()->getUrl();
            if($uri == '/') {
                $action = "homepage";
            } else {
                $action = ltrim($uri, '/');
            }
        }

        $html = Html::jsFile("https://www.google.com/recaptcha/api.js?render={$recaptcha_public}&onload=wheelformRecaptchaV3onload");
        $html .= Html::script("
        var WheelformRecaptcha = {
            callbacks: [],
        };
        var wheelformRecaptchaV3onload = function() {
            grecaptcha.ready(function() {
                grecaptcha.execute('{$recaptcha_public}', {action: '{$action}'}).then(function(token) {
                    if(WheelformRecaptcha.callbacks.length > 0) {
                        for(var i = 0; i < WheelformRecaptcha.callbacks.length; i++) {
                            var callback = WheelformRecaptcha.callbacks[i];
                            callback(token);
                        }
                    }
                });
            });
        }");
        return Template::raw($html);
    }
}
