<?php
namespace Wheelform\Controllers;

use Craft;
use craft\web\Controller;
use Wheelform\Models\Form;
use Wheelform\Models\FormField;
use Wheelform\Models\Message;
use Wheelform\Models\MessageValue;
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
        $message = Message::findOne($entry_id);

        if (! $message) {
            throw new HttpException(404);
        }

        $message->read = 1;
        $message->save();

        $values = MessageValue::find()->where(['message_id' => $entry_id])->with('field')->all();

        return $this->renderTemplate('wheelform/_entry.twig', ['values' => $values, 'form_id' => $message->form->id]);
    }
}
