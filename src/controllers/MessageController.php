<?php
namespace wheelform\controllers;

use Craft;
use wheelform\Plugin;
use yii\web\Response;
use craft\elements\Asset;
use craft\helpers\Assets;

use craft\web\Controller;
use wheelform\models\Form;
use yii\web\HttpException;
use craft\web\UploadedFile;
use wheelform\models\Message;
use wheelform\models\FormField;
use wheelform\models\fields\File;
use wheelform\models\MessageValue;
use craft\errors\UploadFailedException;

class MessageController extends Controller
{

    public $allowAnonymous = true;

    public function actionSend()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $plugin = Plugin::getInstance();
        $settings = $plugin->getSettings();

        $form_id = intval($request->getBodyParam('form_id', "0"));
        if($form_id <= 0){
            throw new HttpException(404);
            return null;
        }

        $formModel = Form::find()->where(['id' => $form_id])->with('fields')->one();

        if(empty($formModel))
        {
            throw new HttpException(404);
            return null;
        }

        $message = new Message();
        $message->form_id = $form_id;

        $errors = [];
        //Array of MessageValues to Link to Message;
        $entryValues = [];
        //Array of values to pass for event;
        $senderValues = [];

        if($formModel->active == 0)
        {
            $errors['form'] = [Craft::t('wheelform', 'Form is no longer active.')];
        }

        if($formModel->recaptcha == 1)
        {
            $userRes = $request->getBodyParam('g-recaptcha-response', '');
            if($this->validateRecaptcha($userRes, $settings->recaptcha_secret) == false)
            {
                $errors['recaptcha'] = [Craft::t('wheelform', "The reCAPTCHA wasn't entered correctly. Try again.")];
            }
        }

        if(! empty($formModel->options['honeypot']))
        {
            $userHoneypot = $request->getBodyParam($formModel->options['honeypot'], '');
            if(! empty($userHoneypot))
            {
                $errors['honeypot'] = [Craft::t('wheelform', "Leave honeypot field empty.")];
            }
        }

        if(empty($errors))
        {
            foreach ($formModel->fields as $field)
            {
                $messageValue = new MessageValue;
                $messageValue->setScenario($field->type);
                $messageValue->field_id = $field->id;

                $senderValues[$field->name] = [
                    'label' => $field->getLabel(),
                    'type' => $field->type,
                ];

                if($field->type == "file")
                {
                    $folder_id = empty($settings->volume_id) ? NULL : $settings->volume_id;
                    $uploadedFile = UploadedFile::getInstanceByName($field->name);
                    $fileModel = new File();
                    try {
                        $assets = Craft::$app->getAssets();
                        $tempPath = $this->_getUploadedFileTempPath($uploadedFile);
                        //No folder to upload files has been selected
                        if(! is_numeric($folder_id)) {
                            $fileModel->name = $uploadedFile->name;
                            $fileModel->filePath = $tempPath;
                        } else {
                            $folder = Craft::$app->getVolumes()->getVolumeById($folder_id);
                            if (!$folder) {
                                throw new BadRequestHttpException('The target folder provided for uploading is not valid');
                            }
                            $tempName = $uploadedFile->baseName . '_'. uniqid() .'.' . $uploadedFile->extension;
                            $filename = Assets::prepareAssetName($tempName);

                            $asset = new Asset();
                            $asset->tempFilePath = $tempPath;
                            $asset->filename = $filename;
                            $asset->newFolderId = $folder->id;
                            $asset->volumeId = $folder->id;
                            $asset->avoidFilenameConflicts = true;
                            $asset->setScenario(Asset::SCENARIO_CREATE);

                            $result = Craft::$app->getElements()->saveElement($asset);

                            if($result) {
                                $fileModel->name = $asset->filename;
                                $fileModel->filePath = $folder->getRootPath() . '/' . $asset->filename;
                                $fileModel->assetId = $asset->id;
                                if($fileModel->validate()) {
                                    Craft::warning('File not uploaded', 'wheelform');
                                }
                            }
                        }
                    } catch (\Throwable $exception) {
                        return $exception->getMessage();
                    }

                    $messageValue->value = $fileModel;
                    $senderValues[$field->name]['value'] = json_encode($messageValue->value);
                } else {
                    $messageValue->value = $request->getBodyParam($field->name, null);
                    $senderValues[$field->name]['value'] = $messageValue->value;
                }

                if(! $messageValue->validate())
                {
                    $errors[$field->name] = $messageValue->getErrors('value');
                }
                else
                {
                    $entryValues[] = $messageValue;
                }
            }

            // Prevent mesage form being saved if error are present
            if (empty($errors) && boolval($formModel->save_entry))
            {
                // This should never error out based on current values
                if(! $message->save())
                {
                    $errors['message'] = $message->getErrors();
                }
            }
        }

        if (! empty($errors))
        {
            $response = [
                'values' => $request->getBodyParams(),
                'errors' => $errors,
                'success' => false,
            ];

            if ($request->isAjax) {
                return $this->asJson($response);
            }
            else
            {
                Craft::$app->getUrlManager()->setRouteParams([
                    'variables' => $response,
                ]);
                return null;
            }
        }

        //Link Values to Message
        if(boolval($formModel->save_entry))
        {
            foreach($entryValues as $value)
            {
                $message->link('value', $value);
            }
        }

        if($formModel->send_email)
        {
            if (!$plugin->getMailer()->send($formModel, $senderValues)) {
                if ($request->isAjax) {
                    return $this->asJson(['errors' => $errors]);
                }

                Craft::$app->getSession()->setError(Craft::t('wheelform',
                    'There was a problem with your submission, please check the form and try again!'));

                Craft::$app->getUrlManager()->setRouteParams([
                    'variables' => [
                        'values' => $request->getBodyParams(),
                    ]
                ]);

                return null;
            }
        }

        if ($request->isAjax) {
            return $this->asJson(['success' => true, 'message' => $settings->success_message]);
        }

        Craft::$app->getSession()->setNotice($settings->success_message);
        return $this->redirectToPostedUrl($message);
    }

    protected function validateRecaptcha(string $userRes, string $secret)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ipParts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = array_pop($ipParts);
        }
        $jsonRes = file_get_contents($url."?secret=".$secret."&response=".$userRes."&remoteip=".$ipAddress);

        $resp = json_decode($jsonRes);

        return $resp->success;
    }

    private function _getUploadedFileTempPath(UploadedFile $uploadedFile)
    {
        if ($uploadedFile->getHasError()) {
            throw new UploadFailedException($uploadedFile->error);
        }

        // Move the uploaded file to the temp folder
        try {
            $tempPath = $uploadedFile->saveAsTempFile();
        } catch (ErrorException $e) {
            throw new UploadFailedException(0);
        }

        if ($tempPath === false) {
            throw new UploadFailedException(UPLOAD_ERR_CANT_WRITE);
        }

        return $tempPath;
    }
}
