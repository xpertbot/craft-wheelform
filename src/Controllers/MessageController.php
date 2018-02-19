<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use craft\web\UploadedFile;
use yii\web\Response;

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
        $form_id = intval($request->getBodyParam('form_id', "0"));
        if($form_id <= 0){
            throw new HttpException(404);
            return null;
        }
        $formModel = Form::findOne($form_id);
        $message = new Message();
        $message->form_id = $form_id;

        $errors = [];
        $values = [];
        foreach ($formModel->fields as $field)
        {
            $messageValue = new MessageValue;
            $messageValue->setScenario($field->type);
            $messageValue->field_id = $field->id;
            $messageValue->value = $request->getBodyParam($field->name, null);

            if(! $messageValue->validate())
            {
                $errors[$field->name] = $messageValue->getErrors('value');
            }
            else
            {
                $values[] = $messageValue;
            }
        }

        if (! empty($errors))
        {
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => [
                    'values' => $request->getBodyParams(),
                    'errors' => $errors,
                ],
            ]);
            return null;
        }

        //This should never error out based on current values
        if(! $message->save()){
            Craft::$app->getUrlManager()->setRouteParams([
                'variables' => [
                    'message' => $message,
                    'errors' => $errors,
                ],
            ]);
            return null;
        }

        foreach($values as $value)
        {
            $message->link('value', $value);
        }

        // if (!$plugin->getMailer()->send($message)) {
        //     if ($request->getAcceptsJson()) {
        //         return $this->asJson(['errors' => $errors]);
        //     }

        //     Craft::$app->getSession()->setError(Craft::t('wheelform',
        //         'There was a problem with your submission, please check the form and try again!'));
        //     Craft::$app->getUrlManager()->setRouteParams([
        //         'variables' => ['message' => $message]
        //     ]);

        //     return null;
        // }

        // if ($request->getAcceptsJson()) {
        //     return $this->asJson(['success' => true]);
        // }

        // Craft::$app->getSession()->setNotice($settings->successFlashMessage);
        return $this->redirectToPostedUrl($message);
    }
}
