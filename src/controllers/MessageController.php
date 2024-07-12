<?php
namespace wheelform\controllers;

use Craft;
use craft\helpers\App;
use wheelform\events\MessageEvent;
use wheelform\events\ResponseEvent;
use wheelform\db\Form;
use wheelform\db\FormField;
use wheelform\db\Message;
use wheelform\db\MessageValue;
use wheelform\Plugin;
use wheelform\helpers\TagHelper;
use wheelform\services\MessageService;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response as YiiResponse;

class MessageController extends BaseController
{
    /**
     * @var Form
     */
    protected $formModel;

    /**
     * @var boolean
     */
    public array|int|bool $allowAnonymous = true;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var string
     */
    const EVENT_BEFORE_SAVE = "beforeSave";

    /**
     * @var string
     */
    const EVENT_BEFORE_VALIDATE = "beforeValidate";

    /**
     * @var string
     */
    const EVENT_AFTER_VALIDATE = "afterValidate";

    /**
     * @var string
     */
    const EVENT_BEFORE_RESPONSE = "beforeResponse";

    /**
     * @var array
     */
    private $config = [];

    public function actionSend()
    {
        $this->requirePostRequest();

        $request = Yii::$app->request;
        $plugin = Plugin::getInstance();
        $this->settings = $plugin->getSettings();

        $form_id = intval($request->post('form_id', "0"));
        if($form_id <= 0){
            throw new HttpException(404);
            return null;
        }

        $this->formModel = Form::find()->where(['id' => $form_id])->with('fields')->one();

        if(empty($this->formModel)) {
            throw new HttpException(404);
            return null;
        }

        $configService = Craft::$app->getConfig();
        $customConfig = $configService->getConfigFromFile('wheelform');
        if(! empty($customConfig) && is_array($customConfig)) {
            $this->config = $customConfig;
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
            $userRes = $request->post('g-recaptcha-response', '');
            $recaptcha_secret = '';
            if (!empty($this->settings->recaptcha_secret)) {
                if (version_compare(Craft::$app->getVersion(), '3.7.29') >= 0) {
                    $recaptcha_secret = App::parseEnv($this->settings->recaptcha_secret);
                } else {
                    $recaptcha_secret = Craft::parseEnv($this->settings->recaptcha_secret);
                }
            }

            if($this->validateRecaptcha($userRes, $recaptcha_secret) == false)
            {
                $errors['recaptcha'] = [Craft::t('wheelform', "The reCAPTCHA wasn't entered correctly. Try again.")];
            }
        }

        if(! empty($this->formModel->options['honeypot'])) {
            $honeypotFieldValue = isset($this->formModel->options['honeypot_value']) ? $this->formModel->options['honeypot_value'] : '';
            $userHoneypot = $request->post($this->formModel->options['honeypot'], $honeypotFieldValue);
            if (!empty($userHoneypot) && $userHoneypot !== $honeypotFieldValue) {
                $honeypot_error = Craft::t('wheelform', "Leave honeypot field empty.");
                if (!empty($this->config['honeypotField']['error_message']) && is_string($this->config['honeypotField']['error_message'])) {
                    $honeypot_error = $this->config['honeypotField']['error_message'];
                }
                $errors['honeypot'] = [$honeypot_error];
            }
        }

        $beforeValidateEvent = new MessageEvent([
            'form_id' => $this->formModel->id,
            'message' => $request->post(),
            'errors' => $errors,
        ]);

        //Trigger Event to allow plugins to do custom validation to the values
        $this->trigger(self::EVENT_BEFORE_VALIDATE, $beforeValidateEvent);
        $errors = $beforeValidateEvent->errors;

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
                $messageValue->value = $request->post($field->name, null);

                if($messageValue->validate()) {
                    $entryValues[] = $messageValue;
                } else {
                    $errors[$field->name] = $messageValue->getErrors('value');
                }
            }
        }

        //Trigger Event to allow plugins to do custom validation to the values
        $event = new MessageEvent([
            'form_id' => $this->formModel->id,
            'message' => $entryValues,
            'errors' => $errors,
        ]);
        $this->trigger(self::EVENT_AFTER_VALIDATE, $event);

        $errors = $event->errors;

        if (! empty($errors)) {
            $response = [
                'values' => $request->post(),
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
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);

        // Values for Mailer
        $senderValues = [];
        // Save Form entry to database
        $isSaveForm = boolval($this->formModel->save_entry);

        if($isSaveForm && $event->saveMessage) {
            $message->save();
            // Only save session values on non ajax request to prevent polluting the next request
            if (! $request->isAjax) {
                //$message->id does not exists if user turned off database saving
                Craft::$app->getSession()->setFlash('wheelformLastSubmissionId', $message->id, true);
            }
        }

        if(is_array($event->message)) {
            foreach($event->message as $eventValue) {
                $field = $eventValue->field;
                $senderValues[$field->name] = [
                    'label' => $field->getLabel(),
                    'type' => $field->type,
                    'value' => $eventValue->value,
                ];

                if($isSaveForm && $event->saveMessage) {
                    $message->link('value', $eventValue);
                }
            }
        }

        if($this->formModel->send_email && $event->sendMessage) {
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
                        'values' => $request->post(),
                    ]
                ]);

                return null;
            }
        }

        $success_message = $this->settings->success_message;

        if (! empty($this->formModel->options['submit_message'])) {
            // get submit_message tags
            $tags = TagHelper::getTags([$this->formModel->options['submit_message']]);
            if (!empty($tags)) {
                foreach(array_keys($tags) as $t) {
                    if (array_key_exists($t, $senderValues) && !empty($senderValues[$t]['value'])) {
                        $tags[$t] = $senderValues[$t]['value'];
                    }
                }
            }
            $success_message = Taghelper::replacePlaseholders($this->formModel->options['submit_message'], $tags);
        }

        // Ajax response
        $responseData = [
            'success' => true,
            'message' => $success_message,
            'errors' => [],
            'data' => [],
            'headers' => [],
            'redirect' => $this->request->getValidatedBodyParam('redirect'),
            'entry' => null,
        ];

        /**
         * Only create entry values if request is Ajax and the form values are saved
         */
        if($isSaveForm && $request->isAjax) {
            // Yii toArray does not handle nested objects correctly, need to manually create them
            $entry = new MessageService($message->id);
            $responseData['entry'] = $entry->toArray();
            $responseData['entry']['fields'] = [];
            foreach($entry->fields as $f) {
                $responseData['entry']['fields'][] = $f->toArray();
            }
        }

        $responseEvent = new ResponseEvent($responseData);
        $this->trigger(self::EVENT_BEFORE_RESPONSE, $responseEvent);

        if ($request->isAjax) {
            if (! empty($responseEvent->headers) && is_array($responseEvent->headers)) {
                $headers = Yii::$app->response->headers;
                foreach($responseEvent->headers as $k => $v) {
                    $headers->add($k, $v);
                }
            }
            return $this->asJson([
                'success' => $responseEvent->success,
                'message' => $responseEvent->message,
                'data' => $responseEvent->data,
                'errors' => $responseEvent->errors,
                'entry' => $responseEvent->entry,
            ]);
        }

        Craft::$app->getSession()->setNotice($responseEvent->message);
        Craft::$app->getSession()->setFlash('wheelformSuccess',$responseEvent->message);
        Craft::$app->getSession()->setFlash('wheelformSubmittedForm', $this->formModel->id);

        return $this->redirectToEventUrl($responseEvent->redirect, $message);
    }

    protected function validateRecaptcha(string $userRes, string $secret)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";

        if (!empty($this->config['google_recaptcha_url'])) {
            $url = $this->config['google_recaptcha_url'];
        }

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

        if ($this->settings['recaptcha_version'] == '3') {
          return $resp->success && $resp->score >= (float) $this->settings['recaptcha_min_score'];
        }

        return $resp->success;
    }

    /**
     * @param string $url Object containing properties that should be parsed for in the URL.
     * @param mixed $object Object containing properties that should be parsed for in the URL.
     * @param string|null $default The default URL to redirect them to, if no 'redirect' parameter exists. If this is left
     * null, then the current request’s path will be used.
     * @return YiiResponse
     * @throws BadRequestHttpException if the redirect param was tampered with
     */
    protected function redirectToEventUrl(string $url = null, $object = null, string $default = null): YiiResponse
    {
        if ($url === null) {
            if ($default !== null) {
                $url = $default;
            } else {
                $url = $this->request->getPathInfo();
            }
        } else if ($object) {
            $url = $this->getView()->renderObjectTemplate($url, $object);
        }

        return $this->redirect($url);
    }
}
