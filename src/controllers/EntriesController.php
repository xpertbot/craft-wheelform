<?php
namespace wheelform\controllers;

use Craft;
use craft\web\Controller;
use craft\helpers\Path;
use craft\helpers\DateTimeHelper;
use craft\helpers\UrlHelper;

use wheelform\db\Form;
use wheelform\db\FormField;
use wheelform\db\Message;
use wheelform\db\MessageValue;
use wheelform\helpers\ExportHelper;
use wheelform\widgets\LinkPager;


use yii\web\HttpException;
use yii\base\Exception;

class EntriesController extends Controller
{
    public function actionIndex()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();
        $form_id = intval($params['id']);

        $this->requirePermission('wheelform_view_entries_' . $form_id);

        $form = Form::findOne($form_id);
        if (! $form) {
            throw new HttpException(404);
        }

        $request = Craft::$app->getRequest();
        $query = Message::find()->where(['form_id' => $form_id]);
        $total = $query->count();
        $limit = (int) $request->getParam('limit', 50);
        $currentPage = (int) $request->getParam('page', 1);
        $offset = ($currentPage - 1) * $limit;

        $entries = $query
            ->orderBy(['dateCreated' => SORT_DESC])
            ->with('value')
            ->offset($offset)
            ->limit($limit)
            ->all();

        $pager = LinkPager::widget([
            'baseUrl' => $request->getPathInfo(),
            'limit' => $limit,
            'currentPage' => $currentPage,
            'totalCount' => $total,
            // 'firstPageLabel' => "First",
            // 'lastPageLabel' => "Last",
        ]);

        $headerFields = FormField::find()
            ->where(['form_id' => $form_id, 'index_view' => 1, 'active' => 1])
            ->orderBy(['order' => SORT_ASC])
            ->all();

        return $this->renderTemplate('wheelform/_entries.twig', [
            'form_id' => $form_id,
            'entries' => $entries,
            'pager' => $pager,
            'headerFields' => $headerFields,
        ]);
    }

    public function actionView()
    {
        $params = Craft::$app->getUrlManager()->getRouteParams();
        $entry_id = intval($params['id']);
        $message = Message::find()->where(['id' => $entry_id])->with('form')->one();

        if (! $message) {
            throw new HttpException(404);
        }

        $message->read = 1;
        $message->save();

        $messageValues = MessageValue::find()
            ->where(['message_id' => $message->id])
            ->joinWith('field')
            ->orderBy(FormField::tableName().'.order', SORT_ASC)
            ->all();

        return $this->renderTemplate('wheelform/_entry.twig', [
            'messageValues' => $messageValues,
            'entry' => $message,
            'form_id' => $message->form->id,
            'backUrl' => UrlHelper::cpUrl('wheelform/form/'.$message->form->id.'/entries'),
        ]);
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

    public function actionUpdateEntries()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $read_status = $request->getRequiredBodyParam('read_status');
        $params = [
            'read' => $read_status,
        ];
        $message_ids = $request->getBodyParam('message_id', []);
        $result = Message::updateAll($params, ['IN', "id", $message_ids]);

        if ($result === false) {
            if ($request->getAcceptsJson()) {
                return $this->asJson(['success' => false]);
            }

            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t udpate entries.'));

        } else {
            if ($request->getAcceptsJson()) {
                return $this->asJson(['success' => true]);
            }

            Craft::$app->getSession()->setNotice(Craft::t('app', 'Entries updated.'));
        }

        return $this->redirectToPostedUrl();

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

    public function actionDeleteEntries()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $message_ids = $request->getBodyParam('message_id', []);

        MessageValue::deleteAll(['in', 'message_id', $message_ids]);
        $result = Message::deleteAll(['in', 'id', $message_ids]);

        if ($result === false) {
            if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asJson(['success' => false]);
            }
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t delete entries.'));
        } else {
            if (Craft::$app->getRequest()->getAcceptsJson()) {
                return $this->asJson(['success' => true]);
            }
            Craft::$app->getSession()->setNotice(Craft::t('app', 'Entries deleted.'));
        }

        return $this->redirectToPostedUrl();
    }

    public function actionExport()
    {
        $params = Craft::$app->getRequest()->getRequiredBodyParam('params');
        $form_id = $params['form_id'];
        $where = [];

        if(empty($form_id))
        {
            throw new Exception(Craft::t('Form ID is required.', 'wheelform'));
        }

        try {
            $exportHelper = new ExportHelper();
            $where['form_id'] = $form_id;

            if(! empty($params['start_date']['date']))
            {
                $start_date = DateTimeHelper::toDateTime($params['start_date']);
                $start_date->setTimezone(new \DateTimeZone('UTC'));
                $where['start_date'] = $start_date->format('Y-m-d H:i:s');
            }
            if(! empty($params['end_date']['date']))
            {
                $end_date = DateTimeHelper::toDateTime($params['end_date']);
                $end_date->modify('+1 day');
                $end_date->setTimezone(new \DateTimeZone('UTC'));
                $where['end_date'] = $end_date->format('Y-m-d H:i:s');
            }

            $csvPath = $exportHelper->getCsv($where);
        } catch (\Throwable $e) {
            throw new Exception('Could not create csv: '.$e->getMessage());
        }

        if (!is_file($csvPath)) {
            throw new Exception("Could not create csv: the csv file doesn't exist.");
        }

        $filename = pathinfo($csvPath, PATHINFO_BASENAME);

        return $this->asJson([
            'csvFile' => pathinfo($filename, PATHINFO_FILENAME)
        ]);
    }


    public function actionDownloadFile()
    {
        $filename = Craft::$app->getRequest()->getRequiredQueryParam('filename');
        $filePath = Craft::$app->getPath()->getTempPath().DIRECTORY_SEPARATOR.$filename.'.csv';

        if (!is_file($filePath) || !Path::ensurePathIsContained($filePath)) {
            throw new NotFoundHttpException(Craft::t('wheelform', 'Invalid csv name: {filename}', [
                'filename' => $filename
            ]));
        }

        return Craft::$app->getResponse()->sendFile($filePath);
    }
}
