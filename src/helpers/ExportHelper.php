<?php
namespace wheelform\helpers;

use Craft;
use craft\helpers\StringHelper;
use wheelform\db\Message;
use wheelform\db\FormField;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
* CsvExport
*
* helper class to output an CSV from a CActiveRecord array.
*
* example usage:
*
*     CsvExport::export(
*         People::model()->findAll(), // a CActiveRecord array OR any CModel array
*         array(
*             'idpeople'=>array('number'),      'number' and 'date' are strings used by CFormatter
*             'birthofdate'=>array('date'),
*         )
*     ,true,'registros-hasta--'.date('d-m-Y H-i').".csv");
*
*
* Please refer to CFormatter about column definitions, this class will use CFormatter.
*
* @author    Christian Salazar <christiansalazarh@gmail.com> @bluyell @yiienespanol (twitter)
* @licence Protected under MIT Licence.
* @date 07 october 2012.
*/
class ExportHelper
{

    public function getCsv($where)
    {
        $filename = 'wheelform_'.gmdate('ymd_His').'_'.strtolower(StringHelper::randomString(10)).'.csv';
        $file = Craft::$app->getPath()->getTempPath().'/'.StringHelper::toLowerCase($filename);
        $messageTable = Message::tableName();

        $query = Message::find()
            ->with(['field', 'value'])
            ->where([
                "{$messageTable}.form_id" => $where['form_id']
            ]);

        if(! empty($where['start_date']))
        {
            $query->andWhere([
                '>=', "{$messageTable}.dateCreated", $where['start_date']
            ]);
        }

        if(! empty($where['end_date']))
        {
            $query->andWhere([
                '<=', "{$messageTable}.dateCreated", $where['end_date']
            ]);
        }

        $messages = $query->all();
        $fieldModels = FormField::find()->where(['form_id' => $where['form_id']])->orderBy(['order' => SORT_ASC])->all();
        $headers = ArrayHelper::getColumn($fieldModels, 'name');
        $formatter = Craft::$app->getFormatter();
        array_unshift($headers, 'id');
        $headers[] = 'date_created';

        $fp = fopen($file, 'w+');

        //Add Headers
        fputcsv($fp, $headers);
        if(!empty($messages)) {
            $rows = [];
            //create Array for easy Import into CSV
            for ($i=0; $i < count($messages); $i++) {
                $rows[$i]['id'] = [
                    'value' => $messages[$i]->id,
                    'type' => 'text',
                ];
                foreach($messages[$i]->value as $model) {
                    $rows[$i][$model->field->name] = [
                        'type' => $model->field->type,
                        'value' => $model->value,
                    ];
                }
                $rows[$i]['date_created'] = [
                    'value' => $formatter->asDateTime($messages[$i]->dateCreated),
                    'type' => 'text',
                ];
            }

            foreach($rows as $data) {
                $row = [];
                foreach($headers as $header) {
                    if(empty($data[$header])) {
                        $row[] = '';
                    } else {
                        $m = $data[$header];
                        switch ($m['type']) {
                            case 'file':
                                if (empty($m['value'])) {
                                    $row[] = '';
                                } else {
                                    $attachment = json_decode($m['value']);
                                    $row[] = $attachment->name;
                                }
                                break;
                            default:
                                $row[] = $m['value'];
                                break;
                        }
                    }
                }
                fputcsv($fp, $row);
            }
        }
        fclose($fp);

        return $file;
    }

    public function getFields($where)
    {
        $filename = 'wheelform_fields_'.gmdate('ymd_His').'_'.strtolower(StringHelper::randomString(10)).'.json';
        $file = Craft::$app->getPath()->getTempPath().'/'.StringHelper::toLowerCase($filename);
        $fieldModel = FormField::find()->select(['name', 'type', 'required', 'index_view','order', 'active', 'options'])->where($where)->all();
        $data = Json::encode($fieldModel);

        $fp = fopen($file, 'w+');
        fwrite($fp, $data);
        fclose($fp);

        return $file;
    }
}
