<?php
namespace wheelform\services;

use wheelform\models\Message;
use craft\helpers\DateTimeHelper;

class MessageService extends BaseService
{
    protected $id;

    protected $form_id;

    protected $date;

    protected $fields;

    public function __construct($id = null)
    {
        if(! empty($id)) {
            $model = Message::find()->with('value.field')->where(['id' => $id])->one();
            if($model) {
                $this->id = $model->id;
                $this->form_id = $model->form_id;
                $this->date = $model->dateCreated;
                foreach($model->value as $v) {
                    $this->fields[] = new FieldService([
                        'name' => $v->field->name,
                        'type' => $v->field->type,
                        'options' => $v->field->options,
                        'order' => $v->field->order,
                        'value' => $v->value,
                    ]);
                }
            }
        }

        parent::__construct();

        return $this;
    }

    //Getters
    public function getId()
    {
        return $this->id;
    }

    public function getFormId()
    {
        return $this->form_id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getFields()
    {
        return $this->fields;
    }

    //Setters
}
