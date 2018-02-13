<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use Wheelform;

class DefaultController extends Controller
{
    function actionIndex()
    {
        $forms = Form::find()->all();

        return $this->renderTemplate('wheelform/_index.twig', [
            'wheelforms' => $forms,
        ]);
    }
}
