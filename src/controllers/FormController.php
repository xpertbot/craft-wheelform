<?php
namespace wheelform\controllers;

use Craft;
use craft\web\Controller;
use yii\web\HttpException;
use yii\base\Exception;
use yii\behaviors\SessionBehavior;

use wheelform\models\FormField;
use wheelform\models\Form;
use wheelform\Plugin;

class FormController extends Controller
{

    function actionIndex()
    {
        $forms = Form::find()->orderBy(['dateCreated' => SORT_ASC])->all();

        $settings = Plugin::getInstance()->getSettings();
        if (!$settings->validate()) {
            Craft::$app->getSession()->setError(Craft::t('wheelform', 'Plugin settings need to be configured.'));
        }

        return $this->renderTemplate('wheelform/_index.twig', [
            'wheelforms' => $forms,
            'title' => $settings->cp_label,
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
        return $this->renderTemplate('wheelform/_edit-form.twig', [
            'form' => $form
        ]);
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
        $form->save_entry = intval($request->post('save_entry', 0));
        $form->site_id = Craft::$app->sites->currentSite->id;

        $result = $form->save();

        Craft::$app->getUrlManager()->setRouteParams([
            'form' => $form
        ]);
        if(! $result){
            Craft::$app->getSession()->setError(Craft::t('wheelform', 'Couldn’t save form.'));
            return null;
        }

        //Rebuild fields
        $oldFields = FormField::find()->select('id')->where(['form_id' => $form->id])->all();
        $newFields = $request->getBodyParam('fields', []);
        //Get ID of fields that are missing on the oldFields compared to newfields
        $toDeleteIds = $this->getToDeleteIds($oldFields, $newFields);

        if(! empty($newFields))
        {
            foreach($newFields as $field)
            {
                //If field name is empty skip it, but don't delete it, only delete it if delete icon is clicked.
                if(empty($field['name'])) continue;

                if(intval($field['id']) > 0)
                {
                    //update Field Values
                    $formField = FormField::find()->where(['id' => $field['id']])->one();
                    if(! empty($formField))
                    {
                        $formField->setAttributes($field, false);

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
                    //unassign id to not autopopulate it on model; PostgreSQL doesn't like set ids
                    unset($field['id']);

                    // new field
                    $formField = new FormField();
                    $formField->setAttributes($field, false);

                    if($formField->save())
                    {
                        $form->link('fields', $formField);
                    }
                }
            }
        }

        if(! empty($toDeleteIds))
        {
            $db = Craft::$app->getDb();
            $db->createCommand()->update(
                FormField::tableName(),
                [
                    'active' => 0,
                    'required' => 0,
                    'index_view' => 0,
                ],
                "id IN (".implode(', ', $toDeleteIds).")"
            )->execute();
        }

        Craft::$app->getSession()->setNotice(Craft::t('wheelform', 'Form saved.'));
        return $this->redirectToPostedUrl();
    }

    // currently this field only accepts json fields
    public function actionGetFields()
    {
        $req =  Craft::$app->getRequest();

        if(! $req->getAcceptsJson())
        {
            throw new HttpException(404);
        }

        $formId = $req->get('form_id');
        if(! is_numeric($formId) || empty($formId))
        {
            throw new HttpException(404);
        }

        $fields = FormField::find()->where(['form_id' => $formId, 'active' => 1])->orderBy('order', SORT_ASC)->all();

        return $this->asJson($fields);
    }

    protected function getToDeleteIds(array $oldFields, array $newFields): array
    {
        $toDelete = [];

        if(! empty($oldFields))
        {
            foreach($oldFields as $field)
            {
                $index = array_search($field->id, array_column($newFields, 'id'));

                // $index is missing add field->id to toDelete array
                if($index === false)
                {
                    $toDelete[] = strval($field->id);
                }
            }
        }

        return $toDelete;
    }
}
