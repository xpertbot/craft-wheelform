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

    private $submitButton;

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

        if(empty($this->submitButton)) {
            $this->submitButton = [];
        }

        $this->submitButton = array_replace_recursive($this->getDefaultSubmitButton(), $this->submitButton);

        if(! empty($this->buttonLabel) ) {
            $this->submitButton['label'] = $this->buttonLabel;
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
        $attributes = $this->generateFormAttributes();
        $html = sprintf("<form $attributes>");
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
            if(! empty($settings['recaptcha_version'] && $settings['recaptcha_version'] == '3')) {
                $html .= $this->renderRecaptchaV3Event();
            } else {
                $html .= "<div><script src=\"https://www.google.com/recaptcha/api.js\"></script><!-- Production captcha --><div class=\"g-recaptcha\" data-sitekey=\"{$settings['recaptcha_public']}\"></div></div>";
            }
        }

        if(! empty($this->instance->options['honeypot']) ) {
            $hpValue = empty($this->values[$this->instance->options['honeypot']]) ? '' : $this->values[$this->instance->options['honeypot']];
            $html .= "<input type=\"text\" class=\"wf-{$this->instance->options['honeypot']}-{$this->id}\"
                name=\"{$this->instance->options['honeypot']}\" value=\"{$hpValue}\" />";
        }
        if($this->hasList()) {
            $html .= $this->registerListAsset();
        }
        $html .= $this->renderSubmitButton();
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

    public function getId()
    {
        return $this->id;
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

    public function setButtonLabel($value)
    {
        $this->buttonLabel = $value;
    }

    public function setSubmitButton($value)
    {
        $this->submitButton = $value;
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
    protected function generateFormAttributes()
    {
        $defaultAttributes = [
            'method' => strtoupper($this->method),
            'id' => $this->generateId(),
            'class' => (empty($this->styleClass) ? "" : $this->styleClass),
            'style' => "",
            'enctype' => ( $this->isMultipart() ? "multipart/form-data" : ""),
        ];

        if(! is_array($this->attributes)) {
            $userAttributes = explode(' ', $this->attributes);
        } else {
            $userAttributes = $this->attributes;
        }

        if($this->hasStringKeys($userAttributes)) {
            $attributes = array_merge($defaultAttributes, $userAttributes);
        } else {
            // Attributes are values in the array as strings
            $attributes = $defaultAttributes;
            foreach($userAttributes as $attr) {
                $keywords = preg_split("/[\=]+/",trim($attr));
                if(count($keywords) == 2) {
                    $attributes[$keywords[0]] = str_replace(["\"", "'"], "", $keywords[1]);
                }
            }
        }

        $return = "";
        foreach($attributes as $key => $val) {
            $return .= "{$key}=\"{$val}\" ";
        }

        return trim($return);
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

    protected function renderRecaptchaV3Event()
    {
        $fieldId = "wheelform-g-recaptcha-token-". uniqid();
        $html = "<input type=\"hidden\" name=\"g-recaptcha-response\" id=\"{$fieldId}\" value=\"\" />";
        $html .= "<script>WheelformRecaptcha.callbacks.push(function(token){
            var field = document.getElementById('{$fieldId}');
            field.setAttribute('value', token);
        })</script>";
        return $html;
    }

    protected function renderSubmitButton()
    {
        if(! empty($this->submitButton['html'])) {
            return $this->submitButton['html'];
        }

        $attributes = "";
        if(!empty($this->submitButton["attributes"]) && is_array($this->submitButton["attributes"])) {
            foreach($this->submitButton["attributes"] as $att => $value) {
                $attributes .= "{$att}=\"{$value}\" ";
            }
        }
        $attributes = trim($attributes);

        if($this->submitButton['type'] == "button") {
            $html = "<button {$attributes}>{$this->submitButton['label']}</button>";
        } else {
            $html = "<input type=\"submit\" {$attributes} value=\"{$this->submitButton['label']}\" />";
        }

        return $html;
    }

    protected function hasStringKeys(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
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
