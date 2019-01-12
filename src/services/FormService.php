<?php
namespace wheelform\services;

use Craft;
use craft\helpers\Template;
use wheelform\models\Form;
use wheelform\models\FormField;
use wheelform\models\Message;
use wheelform\Plugin as Wheelform;
use wheelform\assets\ListFieldAsset;
use yii\base\ErrorException;

class FormService extends BaseService
{
    private $id;

    private $instance;

    private $fields = [];

    private $redirect;

    private $method;

    private $buttonLabel;
    
    private $buttonClass;

    private $attributes;

    private $values;

    private $styleClass;

    public function init()
    {
        $this->instance = Form::find()
            ->select('id, name, recaptcha, options')
            ->where(['id' => $this->id])
            ->one();

        if(empty($this->instance)) {
            throw new ErrorException("Wheelform Form ID not found");
        }

        if(empty($this->buttonLabel)) {
            $this->buttonLabel = Craft::t('app', "Send");
        }
        
        if(empty($this->buttonClass)) {
            $this->buttonClass = "btn";
        }

        if(empty($this->method)) {
            $this->method = "POST";
        }

        $params = Craft::$app->getUrlManager()->getRouteParams();

        if(! empty($params['variables']['values']))
        {
            $this->values = $params['variables']['values'];
        }
    }

    public function open()
    {
        $enctype = '';
        if( $this->isMultipart() ) {
            $enctype = "enctype=\"multipart/form-data\"";
        }
        $method = strtoupper($this->method);
        $html = "<form id=\"{$this->generateId()}\" method=\"{$method}\" ";
        $html .= (empty($this->styleClass) ? '' : " class='{$this->styleClass}' ");
        $html .= (!empty($this->attributes) ? (is_array($this->attributes) ? implode(' ', $this->attributes) : $this->attributes ) : '');
        $html .= " {$enctype}>";
        $html .= $this->generateCsrf();
        $html .= "<input type=\"hidden\" name=\"form_id\" value=\"{$this->id}\" />";
        $html .= "<input type=\"hidden\" name=\"action\" value=\"wheelform/message/send\" />";
        if($this->redirect) {
            $html .=  "<input type=\"hidden\" name=\"redirect\" value=\"{$this->hashUrl($this->redirect)}\" />";
        }

        return Template::raw($html);
    }

    public function close()
    {
        $html = '';
        $settings = Wheelform::getInstance()->getSettings();
        if(intval($this->instance->recaptcha) && ! empty($settings['recaptcha_public'])) {
            $html .= "<div><script src=\"https://www.google.com/recaptcha/api.js\"></script><!-- Production captcha --><div class=\"g-recaptcha\" data-sitekey=\"{$settings['recaptcha_public']}\"></div></div>";
        }

        if(! empty($this->instance->options['honeypot']) ) {
            $hpValue = empty($this->values[$this->instance->options['honeypot']]) ? '' : $this->values[$this->instance->options['honeypot']];
            $html .= "<input type=\"text\" class=\"wf-{$this->instance->options['honeypot']}-{$this->id}\"
                name=\"{$this->instance->options['honeypot']}\" value=\"{$hpValue}\" />";
        }
        if($this->hasList()) {
            $html .= $this->registerListAsset();
        }
        $html .= "<input type=\"submit\" class=\"{$this->buttonClass}\" value=\"{$this->buttonLabel}\" />";
        $html .= '</form>';
        return Template::raw($html);
    }

    public function getFields()
    {
        if(! empty($this->fields))
        {
            return $this->fields;
        }

        $fields = FormField::find()->select('type, name, required, order, options')
            ->orderBy('order', 'ASC')
            ->where(['form_id' => $this->id, 'active' => 1])
            ->asArray()
            ->all();

        foreach($fields as $f){
            $f['value'] = (empty($this->values[$f['name']]) ? '' : $this->values[$f['name']] );
            $this->fields[] = new FieldService($f);
        }

        return $this->fields;
    }

    // Getters
    public function getEntries($start = 0, $limit = null)
    {
        $query = Message::find()
            ->with('value.field')
            ->where(['form_id' => $this->id])
            ->orderBy(['dateCreated' => SORT_DESC]);

        if(! is_null($limit) && is_numeric($limit)) {
            $query->offset($start)->limit($limit);
        }

        $entries = null;
        $models = $query->all();

        // create services that will display on the template
        foreach ($models as $model) {
            $message =  $this->loadMessage($model);
            $entries[] = $message;
        }

        return $entries;
    }

    public function getEntry($id) {
        $model = Message::find()
            ->with('value.field')
            ->where([
                'form_id' => $this->id,
                'id' => intval($id),
            ])
            ->one();
        return $this->loadMessage($model);
    }

    public function getRecaptcha()
    {
        return (bool) $this->instance->recaptcha;
    }


    //Setters
    public function setId($value)
    {
        $this->id = $value;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function setRedirect($value)
    {
        $this->redirect = $value;
    }

    public function setMethod($value)
    {
        $this->method = $value;
    }

    public function setButtonLabel($value)
    {
        $this->buttonLabel = $value;
    }
    
    public function setButtonClass($value)
    {
        $this->buttonClass = $value;
    }

    public function setAttributes($value)
    {
        $this->attributes = $value;
    }

    public function setStyleClass($value)
    {
        $this->styleClass = $value;
    }

    // Protected
    protected function generateId()
    {
        $name = $this->instance->name;
        $return = strtolower($name);
        $return = trim($return);
        $return = str_replace([" ", "_"], "-", $return);
        $return .= "-wheelform";
        return $return;
    }

    protected function hashUrl($url)
    {
        $security = Craft::$app->getSecurity();
        return $security->hashData($url);
    }

    protected function isMultipart()
    {
        $field = FormField::find()->where(['form_id' => $this->id, 'active' => 1, 'type' => 'file'])->one();
        return (! empty($field));
    }

    protected function hasList()
    {
        $field = FormField::find()->where(['form_id' =>$this->id, 'active' => 1, 'type' => 'list'])->one();
        return( ! empty($field));
    }

    protected function registerListAsset()
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(ListFieldAsset::class);
    }

    protected function generateCsrf()
    {
        $config = Craft::$app->getConfig()->getGeneral();
        if ($config->enableCsrfProtection === true)
        {
            $request = Craft::$app->getRequest();
            return '<input type="hidden" name="'.$config->csrfTokenName.'" value="'.$request->getCsrfToken().'">';
        }

        return '';
    }

    protected function loadMessage($model)
    {
        if(empty($model)) {
            return null;
        }

        $message = new MessageService();
        $message->id = $model->id;
        $message->date = $model->dateCreated;
        foreach ($model->value as $v) {
            $message->addField(new FieldService([
                'name' => $v->field->name,
                'type' => $v->field->type,
                'options' => $v->field->options,
                'order' => $v->field->order,
                'value' => $v->value,
            ]));
        }

        return $message;
    }
}
