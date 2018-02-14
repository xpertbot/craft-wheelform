<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use yii\web\Response;
use yii\web\HttpException;
use yii\base\Exception;
use yii\behaviors\SessionBehavior;

class FormController extends Controller
{
    function actionIndex()
    {
        $forms = Form::find()->all();

        return $this->renderTemplate('wheelform/_index.twig', [
            'wheelforms' => $forms,
        ]);
    }

    function actionEdit($variables = [])
    {
        if (! empty($variables['id'])) {
            $variables['form'] = Form::find()->where(["id" => $variables['id']])->one();
            if (! $variables['form']) {
                throw new HttpException(404);
            }
            $variables['form']->validate();
        } else {
            $params = Craft::$app->getUrlManager()->getRouteParams();

            if(empty($params['form'])){
                $variables['form'] = new Form();
            } else {
                $variables['form'] = $params['form'];
            }
        }

        // Render the template
        return $this->renderTemplate('wheelform/_edit-form.twig', $variables);
    }

    function actionSave()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $form_id = $request->getBodyParam('form_id');
        if ($form_id) {
            $form = Form::find()->where(['id' => $form_id])->one();
            if (! $form) {
                throw new Exception(Craft::t('wheelform', 'No form exists with the ID “{id}”.', array('id' => $form_id)));
            }
        } else {
            $form = new Form();
        }

        $form->form_name = $request->getBodyParam('form_name');
        $form->to_email = $request->getBodyParam('to_email');
        $form->fields = $request->getBodyParam('fields', '');
        $form->site_id = Craft::$app->sites->currentSite->id;
        $result = $form->save();

        Craft::$app->getUrlManager()->setRouteParams([
            'form' => $form
        ]);

        if($result){
            Craft::$app->getSession()->setNotice(Craft::t('wheelform', 'Form saved.'));
            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setError(Craft::t('wheelform', 'Couldn’t save form.'));
        return null;
    }
}
