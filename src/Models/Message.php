<?php
namespace Wheelform\Models;

use craft\db\ActiveRecord;

//Using Active Record because it extends Models.
class Message extends Activerecord
{

    public static function tableName(): String
    {
        return '{{%wheelform_messages}}';
    }

    public function rules(): Array
    {
        return [
            [['form_id', 'values'], 'required'],
            ['form_id', 'integer'],
            ['form_id', 'filter', 'filter' => 'intval'],
            ['values', 'safe'],
        ];
    }
}
