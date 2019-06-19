<?php
namespace wheelform\controllers;

use Craft;

use wheelform\events\MessageEvent;
use wheelform\db\Form;
use wheelform\db\Message;
use wheelform\db\MessageValue;
use wheelform\Plugin;
use yii\web\HttpException;
use wheelform\db\FormField;

class MessageController extends BaseController
{
    /**
     * @var Form
     */
    protected $formModel;

    /**
     * @var boolean
     */
    public $allowAnonymous = true;

    /**
     * @var string
     */
    const EVENT_BEFORE_SAVE = "beforeSave";

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

        $this->formModel = Form::find()->where(['id' => $form_id])->with('fields')->one();

        if(empty($this->formModel)) {
            throw new HttpException(404);
            return null;
        }

        $message = new Message();
        $message->form_id = $form_id;

        $errors = [];
        //Array of MessageValues to Link to Message;
        $entryValues = [];

        if($this->formModel->active == 0) {
            $errors['form'] = [Craft::t('wheelform', 'Form is no longer active.')];
        }

        if($this->formModel->recaptcha == 1) {
            $userRes = $request->getBodyParam('g-recaptcha-response', '');
            $recaptcha_secret = empty($settings->recaptcha_secret) ? "" : Craft::parseEnv($settings->recaptcha_secret);
            if($this->validateRecaptcha($userRes, $recaptcha_secret) == false)
            {
                $errors['recaptcha'] = [Craft::t('wheelform', "The reCAPTCHA wasn't entered correctly. Try again.")];
            }
        }

        if(! empty($this->formModel->options['honeypot'])) {
            $userHoneypot = $request->getBodyParam($this->formModel->options['honeypot'], '');
            if(! empty($userHoneypot))
            {
                $errors['honeypot'] = [Craft::t('wheelform', "Leave honeypot field empty.")];
            }
        }

        $visualFields = FormField::getVisualFields();
        if(empty($errors)) {
            // Get Form Fields to validate them
            foreach ($this->formModel->fields as $field) {
                if(in_array($field->type, $visualFields)) {
                    continue;
                }
                $messageValue = new MessageValue;
                $messageValue->setScenario($field->type);
                $messageValue->field_id = $field->id;
                $messageValue->value = $request->getBodyParam($field->name, null);

                if($messageValue->validate()) {
                    $entryValues[] = $messageValue;
                } else {
                    $errors[$field->name] = $messageValue->getErrors('value');
                }
            }

        }

        if (! empty($errors)) {
            $response = [
                'values' => $request->getBodyParams(),
                'errors' => $errors,
                'wheelformErrors' => $errors,
                'success' => false,
            ];

            if ($request->isAjax) {
                return $this->asJson($response);
            } else {
                Craft::$app->getUrlManager()->setRouteParams([
                    'variables' => $response,
                ]);
                return null;
            }
        }

        //Trigger Event to allow plugins to modify fields before being saved to the database
        $event = new MessageEvent([
            'form_id' => $this->formModel->id,
            'message' => $entryValues
        ]);
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);

        // Values for Mailer
        $senderValues = [];

        if(boolval($this->formModel->save_entry)) {
            $message->save();
            //$message->id does not exists if user turned off database saving
            Craft::$app->getSession()->setFlash('wheelformLastSubmissionId', $message->id, true);
        }

        if(is_array($event->message)) {
            foreach($event->message as $eventValue) {
                $field = $eventValue->field;
                $senderValues[$field->name] = [
                    'label' => $field->getLabel(),
                    'type' => $field->type,
                    'value' => $eventValue->value,
                ];

                if(boolval($this->formModel->save_entry)) {
                    $message->link('value', $eventValue);
                }
            }
        }

        if($this->formModel->send_email) {
            if (!$plugin->getMailer()->send($this->formModel, $senderValues)) {
                if ($request->isAjax) {
                    return $this->asJson(['errors' => $errors]);
                }

                Craft::$app->getSession()->setError(Craft::t('wheelform',
                    'There was a problem with your submission, please check the form and try again!'));
                Craft::$app->getSession()->setFlash('wheelformError', Craft::t('wheelform',
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
        Craft::$app->getSession()->setFlash('wheelformSuccess',$settings->success_message);

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
        $data = array(
            'secret' => $secret,
            'response' => $userRes,
            'remoteip' => $ipAddress,
        );
        $options = array(
            'http' => array (
                'header' => "Content-Type: application/x-www-form-urlencoded",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $jsonRes = file_get_contents($url, false, $context);

        $resp = json_decode($jsonRes);

        return $resp->success;
    }
}
