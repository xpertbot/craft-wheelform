<?php
namespace wheelform\services;

use wheelform\db\Message;

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
                    $this->fields[$v->field->name] = (new FieldService([
                        'name' => $v->field->name,
                        'type' => $v->field->type,
                        'options' => $v->field->options,
                        'order' => $v->field->order,
                        'value' => $v->value,
                    ]));
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
    public function setId($id) {
        $this->id = $id;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function addField($field) {
        $this->fields[$field->name] = $field;
    }
}
