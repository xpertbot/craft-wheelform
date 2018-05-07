<?php
namespace wheelform\controllers;

use Craft;
use craft\web\Controller;
use wheelform\models\Form;
use wheelform\models\FormField;
use wheelform\models\Message;
use wheelform\models\MessageValue;
use yii\web\HttpException;
use yii\base\Exception;
use yii\data\Pagination;
use yii\widgets\LinkPager;

class EntriesController extends Controller
{
    public function actionIndex()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();
        $form_id = intval($params['id']);
        $form = Form::findOne($form_id);
        if (! $form) {
            throw new HttpException(404);
        }

        $query = Message::find()->where(['form_id' => $form_id]);
        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count]);
        $pages->setPageSize(50);
        $entries = $query
            ->orderBy(['dateCreated' => SORT_DESC])
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $headerFields = FormField::find()->where(['form_id' => $form_id, 'index_view' => 1, 'active' => 1])->all();

        $pager = LinkPager::widget([
            'pagination' => $pages,
        ]);

        return $this->renderTemplate('wheelform/_entries.twig', [
            'entries' => $entries,
            'pager' => $pager,
            'headerFields' => $headerFields,
        ]);
    }

    public function actionView()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();
        $entry_id = intval($params['id']);
        $message = Message::find($entry_id)->with('form', 'value')->one();

        if (! $message) {
            throw new HttpException(404);
        }

        $message->read = 1;
        $message->save();

        return $this->renderTemplate('wheelform/_entry.twig', ['entry' => $message, 'form_id' => $message->form->id]);
    }

    public function actionUpdateEntry()
    {
        $this->requirePostRequest();

        $entryId = Craft::$app->getRequest()->getRequiredBodyParam('entry_id');
        $readStatus = Craft::$app->getRequest()->getRequiredBodyParam('read_status');
        $entry = Message::findOne($entryId);

        $entry->read = intval($readStatus);

        if (! $entry->update()) {
            if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asJson(['success' => false]);
            }

            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t udpate entry.'));

            // Send the entry back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'entry' => $entry
            ]);

            return null;
        }

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Entry updated.'));

        return $this->redirectToPostedUrl($entry);

    }

    public function actionDeleteEntry()
    {
        $this->requirePostRequest();

        $entryId = Craft::$app->getRequest()->getRequiredBodyParam('entry_id');
        $entry = Message::findOne($entryId);

        if (! $entry->delete()) {
            if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asJson(['success' => false]);
            }

            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t delete entry.'));

            // Send the entry back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'entry' => $entry
            ]);

            return null;
        }

        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Entry deleted.'));

        return $this->redirectToPostedUrl($entry);
    }
}
