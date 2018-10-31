<?php
namespace wheelform\services;

use Craft;
use craft\helpers\Template;
use wheelform\models\Form;
use wheelform\models\FormField;
use wheelform\models\Message;
use wheelform\Plugin as Wheelform;
use yii\base\ErrorException;

class FormService extends BaseService
{
    private $id;

    private $instance;

    private $fields = [];

    private $entries = [];

    private $redirect;

    private $method;

    private $buttonLabel;

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
        $html .= "<input type=\"submit\" value=\"{$this->buttonLabel}\" />";
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
    public function getEntries()
    {
        if(! empty($this->entries))
        {
            return $this->entries;
        }

        $entries = array();

        $query = Message::find()
            ->with('field')
            ->where(['form_id' => $this->id])
            ->orderBy(['dateCreated' => SORT_DESC])
            ->all();

        // map fields and values to entries array
        foreach ($query as $entry) {
            $item = $this->getValues($entry);
            // ignore any empty items
            if (array_key_exists('fields', $item)) {
                $entries[] = $item;
            }
        }
        return $entries;
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

    protected function getValues($formEntry) {
        $values = array(
            'id' => $formEntry->id,
            'form_id' => $formEntry->form_id,
            'date' => $formEntry->dateCreated
        );
        foreach ($formEntry->field as $field) {
            $values['fields'][$field->name] = array (
                'name' => $field->name,
                'label' => $field->label,
                'value' => $formEntry->getValueById($field->id)->value,
                'type' => $field->type
            );
        }
        return $values;
    }
}
