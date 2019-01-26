<?php
namespace wheelform\controllers;

use Craft;
use wheelform\Plugin;
use craft\helpers\Path;
use yii\base\Exception;

use craft\web\Controller;
use craft\web\UploadedFile;
use wheelform\models\Form;
use yii\web\HttpException;
use wheelform\models\FormField;
use yii\behaviors\SessionBehavior;
use wheelform\helpers\ExportHelper;
use wheelform\models\tools\ImportFile;

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
        $form->options = $request->post('options', []);
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
    public function actionGetSettings()
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

        $form = Form::find()->where(['id' => $formId])->with('fields')->asArray()->one();

        return $this->asJson($form);
    }

    public function actionExportFields()
    {
        $req = Craft::$app->getRequest();

        if(! $req->getAcceptsJson())
        {
            throw new HttpException(404);
        }
        $params = $req->getRequiredBodyParam('params');
        $form_id = $params['form_id'];
        $where = [];

        if(empty($form_id))
        {
            throw new Exception(Craft::t('Form ID is required.', 'wheelform'));
        }

        try {
            $exportHelper = new ExportHelper();
            $where['form_id'] = $form_id;

            $jsonPath = $exportHelper->getFields($where);
        } catch (\Throwable $e) {
            throw new Exception('Could not create JSON: '.$e->getMessage());
        }

        if (!is_file($jsonPath)) {
            throw new Exception("Could not create JSON: the JSON file doesn't exist.");
        }

        $filename = pathinfo($jsonPath, PATHINFO_BASENAME);

        return $this->asJson([
            'jsonFile' => pathinfo($filename, PATHINFO_FILENAME)
        ]);
    }

    public function actionImportFields()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $req =  Craft::$app->getRequest();
        $form_id = $req->post('form_id', NULL);
        if(empty($form_id)) {
            return $this->asJson([
                'success' => false,
                'errors' => [Craft::t('wheelform', 'Form ID is required')],
            ]);
        }
        $fileModel = new ImportFile();
        $fileModel->jsonFile = UploadedFile::getInstanceByName('fields_file');

        if(! $fileModel->validate()) {
            return $this->asJson([
                'success' => false,
                'errors' => $fileModel->getErrors('jsonFile'),
            ]);
        }
        $temp = $fileModel->getTempPath();
        $jsonFields = file_get_contents($temp);
        $fields = json_decode($jsonFields, true);
        if(empty($jsonFields) || (json_last_error() !== JSON_ERROR_NONE)) {
            return $this->asJson([
                'success' => false,
                'errors' => [Craft::t('wheelform', 'Empty or invalid Json')],
            ]);
        }

        $errors = [];
        if(is_array($fields)) {
            foreach($fields as $f) {
                $fieldModel = new FormField();
                $f['form_id'] = $form_id;
                $fieldModel->setAttributes($f, false);
                if($fieldModel->validate()) {
                    $fieldModel->save();
                } else {
                    $errors = $fieldModel->getErrors();
                }
            }
        }

        return $this->asJson([
            'success' => Craft::t('wheelform', 'Fields Imported'),
            'errors' => $errors,
        ]);
    }

    public function actionDownloadFile()
    {
        $filename = Craft::$app->getRequest()->getRequiredQueryParam('filename');
        $filePath = Craft::$app->getPath()->getTempPath().DIRECTORY_SEPARATOR.$filename.'.json';

        if (!is_file($filePath) || !Path::ensurePathIsContained($filePath)) {
            throw new NotFoundHttpException(Craft::t('wheelform', 'Invalid json name: {filename}', [
                'filename' => $filename
            ]));
        }

        return Craft::$app->getResponse()->sendFile($filePath);
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
