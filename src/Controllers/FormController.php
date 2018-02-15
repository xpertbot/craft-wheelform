<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use yii\web\Response;
use yii\web\HttpException;
use yii\base\Exception;
use yii\behaviors\SessionBehavior;
use Wheelform\Helpers\FormFields;

class FormController extends Controller
{
    function actionIndex()
    {
        $forms = Form::find()->all();

        return $this->renderTemplate('wheelform/_index.twig', [
            'wheelforms' => $forms,
        ]);
    }

    function actionEdit()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();

        if (! empty($params['id']))
        {
            $form = Form::find()->where(["id" => $params['id']])->one();
            if (! $form) {
                throw new HttpException(404);
            }
        }
        elseif(! empty($params['form']))
        {
            $form = $params['form'];
        }
        else
        {
            $form = new Form();
        }

        // Render the template
        return $this->renderTemplate('wheelform/_edit-form.twig', ['form' => $form]);
    }

    function actionSave()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $form_id = $request->getBodyParam('form_id');
        if ($form_id)
        {
            $form = Form::find()->where(['id' => $form_id])->one();
            if (! $form) {
                throw new Exception(Craft::t('wheelform', 'No form exists with the ID “{id}”.', array('id' => $form_id)));
            }
        }
        else
        {
            $form = new Form();
        }

        $fields = $request->getBodyParam('fields', '');
        if(! empty($fields)){
            $form->setFields($fields);
        }

        $form->form_name = $request->getBodyParam('form_name');
        $form->to_email = $request->getBodyParam('to_email');

        $form->site_id = Craft::$app->sites->currentSite->id;
        $result = $form->save();

        Craft::$app->getUrlManager()->setRouteParams([
            'form' => $form
        ]);

        if($result)
        {
            Craft::$app->getSession()->setNotice(Craft::t('wheelform', 'Form saved.'));
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setError(Craft::t('wheelform', 'Couldn’t save form.'));
        return null;
    }
}
