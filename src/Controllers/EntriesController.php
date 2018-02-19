<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use yii\web\HttpException;
use yii\base\Exception;

class EntriesController extends Controller
{
    public function actionIndex()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();
        $form = Form::findOne(intval($params['id']));
        if (! $form) {
            throw new HttpException(404);
        }

        return $this->renderTemplate('wheelform/_entries.twig', ['entries' => $form->entries]);
    }
}
