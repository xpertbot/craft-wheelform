<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use craft\web\UploadedFile;
use yii\web\Response;
use yii\web\HttpException;

use Wheelform\Plugin;
use Wheelform\Models\Form;
use Wheelform\Models\FormField;
use Wheelform\Models\Message;
use Wheelform\Models\MessageValue;

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

        $formModel = Form::findOne($form_id);

        if(empty($formModel))
        {
            throw new HttpException(404);
            return null;
        }

        $message = new Message();
        $message->form_id = $form_id;

        $errors = [];
        $values = [];

        if($formModel->active == 0)
        {
            $errors['form'] = ['Form is no longer active.'];
        }

        if($formModel->recaptcha == 1)
        {
            $userRes = $request->getBodyParam('g-recaptcha-response', '');
            if($this->validateRecaptcha($userRes, $settings->recaptcha_secret) == false)
            {
                $errors['recaptcha'] = ['The reCAPTCHA wasn\'t entered correctly. Try again.'];
            }
        }

        if(empty($errors))
        {
            foreach ($formModel->fields as $field)
            {
                $messageValue = new MessageValue;
                $messageValue->setScenario($field->type);
                $messageValue->field_id = $field->id;

                if($field->type == "file"){
                    $messageValue->value = UploadedFile::getInstanceByName($field->name);
                } else {
                    $messageValue->value = $request->getBodyParam($field->name, null);
                }

                if(! $messageValue->validate())
                {
                    $errors[$field->name] = $messageValue->getErrors('value');
                }
                else
                {
                    $values[] = $messageValue;
                }
            }

            //This should never error out based on current values
            if(! $message->save())
            {
                $errors['message'] = $message->getErrors();
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
        foreach($values as $value)
        {
            $message->link('value', $value);
        }

        if($formModel->send_email)
        {
            if (!$plugin->getMailer()->send($formModel->to_email, $formModel->name, $message)) {
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
            $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }
        $jsonRes = file_get_contents($url."?secret=".$secret."&response=".$userRes."&remoteip=".$ipAddress);

        $resp = json_decode($jsonRes);

        return $resp->success;
    }
}
