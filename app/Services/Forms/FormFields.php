<?php

/**
 * Overwrite "AbstractFormFields" and create your own form field template
 */

namespace Services\Forms;

use MaplePHP\Form\AbstractFormFields;
use MaplePHP\Form\Arguments;
use Services\ServiceProvider;

class FormFields extends AbstractFormFields
{

    protected $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Input field (without container)
     * @return string
     */
    public function input(): string
    {
        if (isset($this->attrArr['data-clear'])) {
            $this->attr(["required" => "required"]);
        }
        $this->attr(["type" => ($this->attrArr['type'] ?? "text"), "name" => $this->name, "data-name" => $this->dataName, "value" => $this->value]);
        return "<input {$this->attr}>";
    }

    /**
     * Input text
     * @return string
     */
    public function text(): string
    {
        if (($this->attrArr['type'] ?? "") === "password") {
            return $this->password();
        }
        return $this->container(function () {
            return $this->input();
        });
    }

    /**
     * Input text
     * @return string
     */
    public function password(): string
    {
        if (isset($this->attrArr['data-clear'])) {
            $this->attr(["required" => "required"]);
        }
        return $this->container(function () {
            $this->attr(["type" => "password"]);
            $out = "";
            $out .= '<div class="relative holder-10">';
            $out .= '<a class="abs right block middle over-1 pad wa-show-password-btn" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentcolor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="17" cy="15" r="1"/><circle cx="16" cy="16" r="6"/><path d="M2 16S7 6 16 6s14 10 14 10-5 10-14 10S2 16 2 16Z"/></svg></a>';
            $out .= $this->input();
            $out .= '</div>';
            return $out;
        });
    }

    /**
     * Input hidden
     * @return string
     */
    public function hidden(): string
    {
        $this->attr(["type" => "hidden"]);
        return $this->input();
    }

    /**
     * Input text
     * @return string
     */
    public function date(): string
    {
        if (isset($this->attrArr['data-clear'])) {
            $this->attr(["required" => "required"]);
        }

        return $this->container(function () {
            $this->attr(["type" => "date"]);
            return $this->input();
        });
    }

    /**
     * Input text
     * @return string
     */
    public function datetime(): string
    {
        if (isset($this->attrArr['data-clear'])) {
            $this->attr(["required" => "required"]);
        }

        return $this->container(function () {
            $this->attr(["type" => "datetime-local"]);
            return $this->input();
        });
    }

    /**
     * Input textarea
     * @return string
     */
    public function textarea(): string
    {
        return $this->container(function () {
            $this->attr(["name" => $this->name, "data-name" => $this->dataName]);
            return "<textarea {$this->attr}>{$this->value}</textarea>";
        });
    }


    /**
     * Input select list
     * @return string
     */
    public function select(): string
    {
        return $this->container(function () {

            $name = $this->name;
            if (isset($this->attrArr['multiple'])) {
                $name .= "[]";
            }
            $this->attr(["name" => $name, "data-name" => $this->dataName, "autocomplete" => "off"]);

            $out = "<select {$this->attr}>";
            foreach ($this->items as $val => $item) {
                $selected = ($this->isChecked($val)) ? "selected=\"selected\" " : null;
                $out .= "<option {$selected}value=\"{$val}\">{$item}</option>";
            }
            $out .= "</select>";
            return $out;
        });
    }

    /**
     * Input radio
     * @return string
     */
    public function radio(): string
    {
        return $this->container(function () {
            $out = "";
            $this->attr(["type" => "radio", "name" => $this->name, "data-name" => $this->dataName]);
            foreach ($this->items as $val => $item) {
                $checked = ($this->isChecked($val)) ? "checked=\"checked\" " : null;
                $out .= "<label class=\"radio item small\">";
                $out .= "<input {$checked}value=\"{$val}\" {$this->attr}><span class=\"title\">{$item}</span>";
                $out .= "</label>";
            }
            return $out;
        });
    }

    /**
     * Input checkbox
     * @return string
     */
    public function checkbox(): string
    {
        return $this->container(function () {

            $out = "";
            $length = count($this->items);
            $name = ($length > 1) ? "{$this->name}[]" : $this->name;
            $this->attr(["type" => "checkbox", "name" => $name, "data-name" => $this->dataName]);

            foreach ($this->items as $val => $item) {
                $checked = ($this->isChecked($val)) ? "checked=\"checked\" " : null;
                $out .= "<label class=\"checkbox item small\">";
                $out .= "<input {$checked}value=\"{$val}\" {$this->attr}><span class=\"title\">{$item}</span>";
                $out .= "</label>";
            }
            return $out;
        });
    }
}
