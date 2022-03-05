<?php
namespace wheelform\services;

use Craft;
use craft\helpers\Template;
use yii\helpers\Html;

class FieldService extends BaseService
{
    private $type;

    private $options;

    private $order;

    private $required;

    private $name;

    private $value;

    /**
     * @var string
     */
    private $field_id;

    public function init()
    {
        if(! empty($this->options))
        {
            $this->options = json_decode($this->options);
        }
    }

    //Getters
    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getItems()
    {
        if(! empty($this->options->items)) {
            return $this->options->items;
        }
        return [];
    }

    public function getFieldClass()
    {
        if(! empty($this->options->fieldClass)) {
            return $this->options->fieldClass;
        }
        return '';
    }

    public function getContainerClass()
    {
        if(! empty($this->options->containerClass)) {
            return $this->options->containerClass;
        }
        return '';
    }

    public function getRequired()
    {
        return (bool) $this->required;
    }

    public function getLabel()
    {
        if(! empty($this->options->label))
        {
            return Craft::t('site', $this->options->label);
        }
        $label = trim(str_replace(['_', '-'], " ", $this->name));
        $label = ucfirst($label);
        return Craft::t('site', $label);
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
        if (empty($this->field_id)) {
            $this->field_id = $this->generateId();
        }

        return $this->field_id;
    }

    /**
     * @return bool
     */
    public function getdisplayLabel()
    {
        return (empty($this->options->display_label) ? false : true);
    }

    /**
     * @return null|stdClass
     */
    public function getOptions()
    {
        return (empty($this->options) ? null : $this->options);
    }

    public function getOrder($value)
    {
        return $this->order;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getPlaceholder()
    {
        return (isset($this->options->placeholder) ? Craft::t('site', $this->options->placeholder) : "");
    }

    public function getPattern()
    {
        return (isset($this->options->pattern) ? Craft::t('site', $this->options->pattern) : "");
    }

    public function getMinDate()
    {
        return (isset($this->options->min_date) ? Craft::t('site', $this->options->min_date) : "");
    }

    public function getMaxDate()
    {
        return (isset($this->options->max_date) ? Craft::t('site', $this->options->max_date) : "");
    }

    public function getContent()
    {
        if(empty($this->options->content)) {
            return "";
        }

        return Template::raw($this->options->content);
    }

    public function getFileExtensions()
    {
        if ($this->type !== 'file') {
            return Null;
        }

        return empty($this->options->extensions) ? '' : $this->options->extensions;
    }

    //Setter
    public function setType($value)
    {
        $this->type = $value;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function setOptions($value)
    {
        $this->options = $value;
    }

    public function setRequired($value)
    {
        $this->required = $value;
    }

    public function setOrder($value)
    {
        $this->order = $value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    // Methods
    public function render()
    {
        $html = "<div class=\"wf-group {$this->containerClass}\">";
        $html_default_args = [
            'class' => 'wf-field ' . $this->fieldClass,
        ];
        if ($this->required && !empty($this->options->display_required_attribute)) {
            $html_default_args['required'] = 'required';
        }

        switch($this->type)
        {
            case "html":
                if(! empty($this->options->content)) {
                    $html .= Template::raw($this->options->content);
                }
                break;
            case "radio":
                if(empty($this->items)) {
                    break;
                }

                if(! empty($this->options->display_label)) {
                    $html .= '<div class="wf-group-heading">' . Html::label($this->getLabel(), null, [
                        'class' => 'wf-label',
                    ]) . '</div>';
                }

                foreach($this->items as $key => $item) {
                    $itemTranslated = Craft::t('site', $item);
                    $args = array_merge($html_default_args, [
                        'id' => "wf-radio-" . $this->order . '-' . $key,
                        'value' => $itemTranslated,
                    ]);
                    $html .= '<div class="wf-radio">';
                    $html .= Html::radio($this->name, ($itemTranslated == $this->value), $args);
                    $html .= Html::label($itemTranslated, "wf-radio-" . $this->order . '-' . $key, [
                        'class' => 'wf-label'
                    ]);
                    $html .= '</div>';
                }

                break;
            case "checkbox":
                if(empty($this->items)) {
                    break;
                }
                if(! is_array($this->value)) {
                    $value = [];
                } else {
                    $value = $this->value;
                }

                if(! empty($this->options->display_label)) {
                    $html .= '<div class="wf-group-heading">' . Html::label($this->getLabel(), null, [
                        'class' => 'wf-label',
                    ]) . '</div>';
                }

                foreach($this->items as $key => $item) {
                    $itemTranslated = Craft::t('site', $item);
                    $args = array_merge($html_default_args, [
                        'id' => "wf-checkbox-" . $this->order . '-' . $key,
                        'value' => $itemTranslated,
                    ]);
                    $html .= '<div class="wf-checkbox">';
                    $html .= Html::checkbox($this->name . '[]', in_array($itemTranslated, $value), $args);
                    $html .= Html::label($itemTranslated, "wf-checkbox-" . $this->order . '-' . $key, [
                        'class' => 'wf-label',
                    ]);
                    $html .= '</div>';
                }
                break;
            case "select":
                if(empty($this->items)) {
                    break;
                }
                $fieldName = $this->name.(!empty($this->options->multiple) ? '[]' : '');
                $html .= '<div class="wf-select">';
                $html .= "<label for=\"{$this->getFieldId()}\" class=\"wf-label\">{$this->getLabel()}</label>";
                $html .= "<select id=\"{$this->getFieldId()}\" name=\"{$fieldName}\" class=\"wf-field {$this->fieldClass}\"";
                $html .= (!empty($this->options->display_required_attribute) ? ' required="required"' : '');
                $html .= (!empty($this->options->multiple) ? ' multiple' : '');
                $html .= '>';
                if(!empty($this->options->selectEmpty) && (bool) $this->options->selectEmpty) {
                    $html .= "<option value=\"\"> -- </option>";
                }
                foreach($this->items as $key => $item) {
                    $itemTranslated = Craft::t('site', $item);
                    $html .= "<option value=\"{$itemTranslated}\"".( ($itemTranslated == $this->value) ? ' selected="selected"' : '' ). ">{$itemTranslated}</option>";
                }
                $html .= '</select></div>';
                break;
            case "file":
                $args = array_merge($html_default_args, [
                    'id' => $this->getFieldId(),
                    'class' => "wf-field " . $this->fieldClass,
                ]);
                $html .= Html::label($this->getLabel(), $this->getFieldId(), ['class' => 'wf-label']);
                $html .= Html::fileInput($this->name, null, $args);
                break;
            case "textarea":
                $args = array_merge($html_default_args, [
                    'id' => $this->getFieldId(),
                    'placeholder' => $this->getPlaceholder(),
                ]);
                $html .= Html::label($this->getLabel(), $this->getFieldId(), ['class' => 'wf-label']);
                $html .= Html::textarea($this->name, $this->value, $args);
                break;
            case "list":
                $items = $this->getValue();
                $html .= Html::label($this->getLabel(), $this->getFieldId(), ['class' => 'wf-label']);
                $html .= "<div class=\"wf-list-container\">";
                $html .= Html::a(\Craft::t('site', 'Add') . ' ' . $this->getLabel(), '#', [
                    'class' => 'wf-list-add',
                    'data-field-name' => $this->name,
                ]);
                $html .= "<br />";
                if($items && is_array($items)) {
                    foreach($items as $item) {
                        $html .= Html::textInput($this->name . '[]', $item, [
                            'class' => 'wf-field wf-list-entry ' . $this->fieldClass
                        ]);
                    }
                }
                $html .= "</div>";
                break;
            case 'hidden':
                // Hidden doesn't use a label
                $html .= Html::input($this->type, $this->name, $this->value, [
                    'id' => $this->getFieldId(),
                    'placeholder' => $this->getPlaceholder(),
                ]);
                break;
            case 'email':
            case 'number':
            case 'text':
            case 'tel':
            case 'date':
                $field_args = [
                    'id' => $this->getFieldId(),
                    'placeholder' => $this->getPlaceholder(),
                ];
                if ($this->type == 'tel' && !empty($this->getPattern())) {
                    $field_args['pattern'] = $this->getPattern();
                }
                if ($this->type == 'date' && (!empty($this->getMinDate()) || !empty($this->getMaxDate()))) {
                    if (!empty($this->getMinDate())) {
                        $field_args['min'] = $this->getMinDate();
                    }
                    if (!empty($this->getMaxDate())) {
                        $field_args['max'] = $this->getMaxDate();
                    }
                }
                $args = array_merge($html_default_args, $field_args);
                $html .= Html::label($this->getLabel(), $this->getFieldId(), ['class' => 'wf-label']);
                $html .= Html::input($this->type, $this->name, $this->value, $args);
                break;
            default:
                    $html .= "";
                    break;
        }
        $html .= "</div>";
        return Template::raw($html);
    }

    protected function generateId()
    {
        return "wf-" . trim(str_replace([' ', '_'], "-", $this->name)) . "-" . $this->order;
    }
}
