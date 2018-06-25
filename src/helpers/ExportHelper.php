<?php
namespace wheelform\helpers;

use Craft;
use craft\helpers\StringHelper;

use wheelform\models\Message;

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

        $headers = [];

        //List of all rows with index as row Number and associative array of header => value;
        $values = [];
        for($i = 0; $i < count($messages); $i++)
        {
            $values[$i]['id'] = $messages[$i]->id;
            foreach($messages[$i]->value as $v)
            {
                if(! in_array($v->field->name, $headers))
                {
                    $headers[] = $v->field->name;
                }
                $values[$i][$v->field->name] = (empty($v->value) ? '' : $v->value);
            }
            $values[$i]['date_created'] = $messages[$i]->dateCreated;
        }
        asort($headers);
        array_unshift($headers, 'id');
        $headers[] = 'date_created';

        array_unshift($values, $headers);

        $fp = fopen($file, 'w+');
        for($i = 0; $i < count($values); $i++)
        {
            //Add Headers
            if($i == 0)
            {
                fputcsv($fp, $values[$i]);
                continue;
            }
            //Build row
            $row = [];
            foreach($headers as $h){
                $row[] = (empty($values[$i][$h]) ? '' : $values[$i][$h]);
            }
            fputcsv($fp, $row);
        }
        fclose($fp);

        return $file;
    }
}
