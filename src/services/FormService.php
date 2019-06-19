<?php
namespace wheelform\services;

use Craft;
use craft\helpers\Template;
use wheelform\db\Form;
use wheelform\db\FormField;
use wheelform\db\Message;
use wheelform\Plugin as Wheelform;
use wheelform\assets\ListFieldAsset;
use yii\base\ErrorException;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\web\View;

class FormService extends BaseService
{
    private $id;

    private $instance;

    private $fields = [];

    private $redirect;

    private $method = 'post';

    private $submitButton;

    private $_attributes;

    private $values;

    /**
     * @var boolean
     */
    private $_registerScripts = false;

    /**
     * @var boolean
     */
    private $_scriptsLoaded = false;

    /**
     * @var craft\web\View
     */
    protected $view;

    /**
     * @var boolean
     */
    private $_refreshCsrf = false;

    public function init()
    {
        $this->instance = Form::find()
            ->select('id, name, recaptcha, options')
            ->where(['id' => $this->id])
            ->one();

        if(empty($this->instance)) {
            throw new ErrorException("Wheelform Form ID not found");
        }

        $this->view = Craft::$app->getView();

        if(empty($this->submitButton)) {
            $this->submitButton = [];
        }

        $this->submitButton = array_replace_recursive($this->getDefaultSubmitButton(), $this->submitButton);

        $params = Craft::$app->getUrlManager()->getRouteParams();

        if(! empty($params['variables']['values']))
        {
            $this->values = $params['variables']['values'];
        }

        $this->_attributes = $this->getFormAttributes();

        if($this->_registerScripts) {
            $this->handleScripts();
        }
    }

    public function open()
    {
        $html = Html::beginForm("", $this->method, $this->_attributes);
        $html .= Html::hiddenInput('form_id', $this->id);
        $html .= Html::hiddenInput('action', "/wheelform/message/send");
        if($this->redirect) {
            $html .= Html::hiddenInput('redirect', $this->hashUrl($this->redirect));
        }

        if($this->_scriptsLoaded === false) {
            $this->handleScripts();
        }

        return Template::raw($html);
    }

    public function close()
    {
        $html = '';
        $settings = Wheelform::getInstance()->getSettings();
        $recaptcha_public = empty($settings['recaptcha_public']) ? "" : Craft::parseEnv($settings['recaptcha_public']);
        if(intval($this->instance->recaptcha) && ! empty($recaptcha_public)) {
            if(! empty($settings['recaptcha_version'] && $settings['recaptcha_version'] == '3')) {
                $html .= $this->renderRecaptchaV3Event();
            } else {
                $html .= "<div>";
                $html .= Html::jsFile('https://www.google.com/recaptcha/api.js');
                $html .= "<div class=\"g-recaptcha\" data-sitekey=\"{$recaptcha_public}\"></div>";
                $html .= "</div>";
            }
        }

        if(! empty($this->instance->options['honeypot']) ) {
            $hpValue = empty($this->values[$this->instance->options['honeypot']]) ? '' : $this->values[$this->instance->options['honeypot']];
            $html .= Html::textInput($this->instance->options['honeypot'], $hpValue, [
                'class' => "wf-{$this->instance->options['honeypot']}-{$this->id}",
            ]);
        }

        $html .= $this->renderSubmitButton();
        $html .= Html::endForm();
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

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->instance->name;
    }

    //Setters
    public function setConfig($config)
    {
        \Yii::configure($this, $config);

        $this->submitButton = array_replace_recursive($this->getDefaultSubmitButton(), $this->submitButton);

        return $this; // Don't break the chain in templates;
    }

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

    public function setSubmitButton($value)
    {
        $this->submitButton = $value;
    }

    public function setAttributes($value)
    {
        $this->_attributes = $value;
    }

    /**
     * @param bool $value
     * @return void
     */
    public function setRegisterScripts($value)
    {
        $this->_registerScripts = $value;
    }

    /**
     * @param bool $value
     * @return void
     */

    public function setRefreshCsrf($value)
    {
        $this->_refreshCsrf = $value;
    }

    // Protected
    protected function getFormAttributes()
    {
        $defaultAttributes = [
            'id' => $this->generateId(),
            'class' => '',
        ];

        $attributes = [];

        if(is_null($this->_attributes)) {
            $this->_attributes = [];
        }

        if(! empty($this->_attributes) && is_array($this->_attributes)) {
            // Merge Arrays
            $attributes = array_merge($defaultAttributes, $this->_attributes);
        }

        if($this->isMultipart()) {
            $attributes['enctype'] = "multipart/form-data";
        }

        return $attributes;
    }

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

    protected function renderRecaptchaV3Event()
    {
        $fieldId = "wheelform-g-recaptcha-token-". uniqid();
        $html = Html::hiddenInput('g-recaptcha-response', "", ['id' => $fieldId]);
        $html .= Html::script("WheelformRecaptcha.callbacks.push(function(token){
            var field = document.getElementById('$fieldId');
            field.setAttribute('value', token);
        })");
        return $html;
    }

    protected function renderSubmitButton()
    {
        if(! empty($this->submitButton['html'])) {
            return $this->submitButton['html'];
        }

        $attributes = [];
        if(!empty($this->submitButton["attributes"]) && is_array($this->submitButton["attributes"])) {
            $attributes = $this->submitButton["attributes"];
        }

        if($this->submitButton['type'] == "button") {
            $attributes['type'] = 'submit';
            $html = Html::button($this->submitButton['label'], $attributes);
        } else {
            $html = Html::input('submit', "wf-submit", $this->submitButton['label'], $attributes);
        }

        return $html;
    }

    protected function handleScripts()
    {
        $this->view->registerCsrfMetaTags();

        if($this->hasList()) {
            $this->view->registerAssetBundle(ListFieldAsset::class, View::POS_END);
        }

        if($this->_refreshCsrf) {
            $this->view->registerAssetBundle(YiiAsset::class);
            $this->view->registerJs("window.yii.refreshCsrfToken()");
        }

        $this->_scriptsLoaded = true;
    }

    //Private
    private function getDefaultSubmitButton()
    {
        return [
            'label' => Craft::t('app', "Send"),
            "type" => "input",
            "attributes" => [
                "class" => "",
            ],
            "html" => "",
        ];
    }
}
