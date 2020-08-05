<?php
namespace wheelform\services;

use craft\helpers\Template;
use yii\helpers\Html;

class MetaTagsService extends BaseService
{
    public function __invoke()
    {
        return Template::raw(Html::csrfMetaTags());
    }
}
