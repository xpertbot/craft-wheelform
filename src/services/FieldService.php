<?php
namespace wheelform\services;

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
            return $this->options->label;
        }
        $label = trim(str_replace(['_', '-'], " ", $this->name));
        $label = ucfirst($label);
        return $label;
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
        return (isset($this->options->placeholder) ? $this->options->placeholder : "");
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
                    $html .= '<div class="wf-radio">';
                    $html .= Html::radio($this->name, ($item == $this->value), [
                        'id' => "wf-radio-" . $this->order . '-' . $key,
                        'class' => "wf-field " . $this->fieldClass,
                        'value' => $item,
                    ]);
                    $html .= Html::label($item, "wf-radio-" . $this->order . '-' . $key, [
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
                    $html .= '<div class="wf-checkbox">';
                    $html .= Html::checkbox($this->name . '[]', in_array($item, $value), [
                        'id' => "wf-checkbox-" . $this->order . '-' . $key,
                        'class' => 'wf-field '.$this->fieldClass,
                        'value' => $item,
                    ]);
                    $html .= Html::label($item, "wf-checkbox-" . $this->order . '-' . $key, [
                        'class' => 'wf-label',
                    ]);
                    $html .= '</div>';
                }
                break;
            case "select":
                if(empty($this->items)) {
                    break;
                }
                $html .= '<div class="wf-select">';
                $html .= "<label for=\"{$this->generateId()}\" class=\"wf-label\">{$this->getLabel()}</label>";
                $html .= "<select id=\"{$this->generateId()}\" name=\"{$this->name}\" class=\"wf-field {$this->fieldClass}\">";
                if(!empty($this->options->selectEmpty) && (bool) $this->options->selectEmpty) {
                    $html .= "<option value=\"\"> -- </option>";
                }
                foreach($this->items as $key => $item) {
                    $html .= "<option value=\"{$item}\"".( ($item == $this->value) ? ' selected="selected"' : '' ). ">{$item}</option>";
                }
                $html .= '</select></div>';
                break;
            case "file":
                $html .= Html::label($this->getLabel(), $this->generateId(), ['class' => 'wf-label']);
                $html .= Html::fileInput($this->name, null, [
                    'id' => $this->generateId(),
                    'class' => "wf-field " . $this->fieldClass,
                ]);
                break;
            case "textarea":
                $html .= Html::label($this->getLabel(), $this->generateId(), ['class' => 'wf-label']);
                $html .= Html::textarea($this->name, $this->value, [
                    'id' => $this->generateId(),
                    'placeholder' => $this->getPlaceholder(),
                    'class' => 'wf-field ' . $this->fieldClass,
                ]);
                break;
            case "list":
                $items = $this->getValue();
                $html .= Html::label($this->getLabel(), $this->generateId(), ['class' => 'wf-label']);
                $html .= "<div class=\"wf-list-container\">";
                $html .= Html::a(\Craft::t('wheelform', 'Add') . ' ' . $this->getLabel(), '#', [
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
                // Hidden doesn't put a label
                $html .= Html::input($this->type, $this->name, $this->value, [
                    'id' => $this->generateId(),
                    'placeholder' => $this->getPlaceholder(),
                    'class' => 'wf-field ' . $this->fieldClass,
                ]);
                break;
            case 'email':
            case 'number':
            case 'text':
                // Email, Text, Hidden
                $html .= Html::label($this->getLabel(), $this->generateId(), ['class' => 'wf-label']);
                $html .= Html::input($this->type, $this->name, $this->value, [
                    'id' => $this->generateId(),
                    'placeholder' => $this->getPlaceholder(),
                    'class' => 'wf-field ' . $this->fieldClass,
                ]);
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
