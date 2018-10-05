<?php
namespace wheelform\services;

use Craft;
use craft\helpers\Template;
use Yii;

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
            case "radio":
                if(empty($this->items)) {
                    break;
                }

                foreach($this->items as $key => $item) {
                    $html .= '<div class="wf-radio">';
                    $html .= "<input id=\"wf-radio-" . $this->order . '-' . $key . "\" name=\"{$this->name}\" class=\"wf-field {$this->fieldClass}\"
                        type='radio'".( ($item == $this->value) ? ' checked="checked"' : '' ). " value=\"{$item}\"/>";
                    $html .= "<label for=\"wf-radio-" . $this->order . '-' . $key . "\" class=\"wf-label\">{$item}</label>";
                    $html .= '</div>';
                }

                break;
            case "checkbox":
                if(empty($this->items))
                {
                    break;
                }
                if(! is_array($this->value)) {
                    $value = [];
                } else {
                    $value = $this->value;
                }

                foreach($this->items as $key => $item) {
                    $html .= '<div class="wf-checkbox">';
                    $html .= "<input id=\"wf-checkbox-" . $this->order . '-' . $key . "\" name=\"{$this->name}[]\" class=\"wf-field {$this->fieldClass}\"
                        type='checkbox'".( (in_array($item, $value)) ? ' checked="checked"' : '' ). " value=\"{$item}\"/>";
                    $html .= "<label for=\"wf-checkbox-" . $this->order . '-' . $key . "\" class=\"wf-label\">{$item}</label>";
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
                if($this->options->selectEmpty) {
                    $html .= "<option value=\"\"> -- </option>";
                }
                foreach($this->items as $key => $item) {
                    $html .= "<option value=\"{$item}\"".( ($item == $this->value) ? ' selected="selected"' : '' ). ">{$item}</option>";
                }
                $html .= '</select></div>';
                break;
            case "file":
                $html .= "<label for=\"{$this->generateId()}\" class=\"wf-label\">{$this->getLabel()}</label>";
                $html .= "<input id=\"{$this->generateId()}\"  name=\"{$this->name}\" class=\"wf-field {$this->fieldClass}\" type=\"file\" />";
                break;
            case "textarea":
                $value = empty($this->value) ? '' : $this->value;
                $html .= "<label for=\"{$this->generateId()}\" class=\"wf-label\">{$this->getLabel()}</label>";
                $html .= "<textarea id=\"{$this->generateId()}\" name=\"{$this->name}\" class=\"wf-field {$this->fieldClass}\">{$value}</textarea>";
                break;
            default:
                // Email, Text, Hidden
                $html .= "<label for=\"{$this->generateId()}\" class=\"wf-label\">{$this->getLabel()}</label>";
                $html .= "<input id=\"{$this->generateId()}\" name=\"{$this->name}\" class=\"wf-field {$this->fieldClass}\" type=\"{$this->type}\"
                    value=\"{$this->value}\" />";
                break;
        }
        $html .= "</div>";
        return Template::raw($html);
    }

    protected function generateId()
    {
        return "wf-" . trim(str_replace(['_', '-'], " ", $this->name)) . "-" . $this->order;
    }
}
