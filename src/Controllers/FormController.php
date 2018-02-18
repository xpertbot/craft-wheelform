<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use Wheelform\Models\FormField;
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

    function actionEdit()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();

        if (! empty($params['id']))
        {
            $form = Form::findOne(intval($params['id']));
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
        return $this->renderTemplate('wheelform/_edit-form.twig', ['form' => $form, 'fieldTypes' => FormField::FIELD_TYPES]);
    }

    function actionSave()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $form_id = $request->getBodyParam('form_id');
        if ($form_id)
        {
            $form = Form::findOne(intval($form_id));
            if (! $form) {
                throw new Exception(Craft::t('wheelform', 'No form exists with the ID “{id}”.', array('id' => $form_id)));
            }
        }
        else
        {
            $form = new Form();
        }

        $form->name = $request->getBodyParam('name');
        $form->to_email = $request->getBodyParam('to_email');
        $form->active = $request->getBodyParam('active', 0);
        $form->site_id = Craft::$app->sites->currentSite->id;

        //Check if fields are dirty
        $changedFields = $request->getBodyParam('changed_fields', 0);
        if($changedFields){
            //Delete all fields.
            $form->unlinkFields();

            //Rebuild fields
            $fields = $request->getBodyParam('fields', []);
            if(! empty($fields)){
                foreach($fields as $field){
                   $formField = new FormField($field);
                   if($formField->validate())
                   {
                        $form->link('fields', $formField);
                   }

                }
            }
        }

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
