<?php
namespace wheelform\console\controllers;

use Craft;
use craft\helpers\DateTimeHelper;
use wheelform\models\Message;
use yii\console\Controller;

/**
 * Console Command
 */
class MessageController extends Controller
{
    /**
     * @return int
     */
    public function actionPurge()
    {
        $config = Craft::$app->getConfig()->getConfigFromFile('wheelform');
        $result = 0;

        if(! empty($config) && array_key_exists('purgeMessages', $config) && array_key_exists('purgeMessagesDays', $config)) {

            if($config['purgeMessages'] === true && is_numeric($config['purgeMessagesDays'])) {
                $timestr = "-" . $config['purgeMessagesDays'] . " days";
                $timestamp = strtotime($timestr);
                $datetime = DateTimeHelper::toDateTime($timestamp);
                $datetime->setTimezone(new \DateTimeZone('UTC'));
                $mysqlDate = $datetime->format('Y-m-d H:i:s');

                $result = Message::deleteAll("dateCreated < '{$mysqlDate}'");
            }
        }

        return $result;
    }
}
