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
        $formatter = Craft::$app->getFormatter();
        $headers = [
            0 => 'id',
            9999 => 'Date Created',
        ];
        $message_array = [];
        $total_messages = count($messages);

        for ($i = 0; $i < $total_messages; $i++) {
            $message_array[$i] = [
                'id' => $messages[$i]->id,
                'date_created' => $formatter->asDateTime($messages[$i]->dateCreated),
                'fields' => [],
            ];
            foreach ($messages[$i]->value as $v) {
                $field = $v->field;
                if (!array_key_exists($field->id, $headers)) {
                    $headers[$field->id] = $field->label;
                }
                $message_array[$i]['fields'][$field->id] = [
                    'type' => $field->type,
                    'value' => $v->value,
                ];
            }
        }
        ksort($headers);

        $fp = fopen($file, 'w+');
        //Add Headers
        fputcsv($fp, array_values($headers));
        foreach ($message_array as $m) {
            $row = [];
            foreach (array_keys($headers) as $id) {
                switch($id) {
                    case 0:
                        $row[] = $m['id'];
                        break;
                    case 9999:
                        $row[] = $m['date_created'];
                        break;
                    default:
                        if (array_key_exists($id, $m['fields'])) {
                            switch ($m['fields'][$id]['type']) {
                                case 'file':
                                    if (empty($m['fields'][$id]['value'])) {
                                        $row[] = '';
                                    } else {
                                        $attachment = json_decode($m['fields'][$id]['value']);
                                        if(! empty($attachment) && !empty($attachment->name)) {
                                            $row[] = $attachment->name;
                                        } else {
                                            $row[] = '';
                                        }
                                    }
                                    break;
                                default:
                                    $row[] = $m['fields'][$id]['value'];
                                    break;
                            }
                        } else {
                            $row[] = '';
                        }
                        break;
                }
            }
            fputcsv($fp, $row);
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
