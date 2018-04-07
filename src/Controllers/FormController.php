<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use Wheelform\Models\FormField;
use yii\web\HttpException;
use yii\base\Exception;
use yii\behaviors\SessionBehavior;

use Wheelform\Plugin;

class FormController extends Controller
{

    function actionIndex()
    {
        $forms = Form::find()->all();

        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            Craft::$app->getSession()->setError(Craft::t('wheelform', 'Plugin settings need to be configured.'));
        }

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
        $form->send_email = $request->getBodyParam('send_email', 0);
        $form->recaptcha = $request->getBodyParam('recaptcha', 0);
        $form->site_id = Craft::$app->sites->currentSite->id;

        $result = $form->save();

        Craft::$app->getUrlManager()->setRouteParams([
            'form' => $form
        ]);
        if(! $result){
            Craft::$app->getSession()->setError(Craft::t('wheelform', 'Couldn’t save form.'));
            return null;
        }

        //Check if fields are dirty
        $changedFields = $request->getBodyParam('changed_fields', 0);
        if($changedFields){
            //Rebuild fields
            $fieldList = $request->getBodyParam('fields', []);
            if(! empty($fieldList)){
                foreach($fieldList as $position => $fields){
                    if(is_array($fields))
                    {
                        foreach($fields as $id => $field)
                        {
                            if(intval($id) > 0)
                            {
                                //update Field Values
                                $formField = FormField::find()->where(['id' => $id])->one();
                                if(! empty($formField))
                                {
                                    $formField->setAttributes($field);

                                    if($formField->validate()){
                                        $formField->save();
                                    }
                                    else
                                    {
                                        //do nothing for now
                                    }
                                }
                            }
                            else
                            {
                                // new field
                                $formField = new FormField($field);
                                if($formField->validate())
                                {
                                    $form->link('fields', $formField);
                                }
                            }
                        }
                    }
                }
            }
        }

        Craft::$app->getSession()->setNotice(Craft::t('wheelform', 'Form saved.'));
        return $this->redirectToPostedUrl();
    }
}
