<?php

namespace BootstrapForm;

class SelectOption {
    private $data = [];
    private $label;
    private $value;
    public static function Factory() {
        return new SelectOption();
    }
    public function Data($name = null, $value = null) {
        if (!is_null($name)) {
            $this->data[] = sprintf('data-%s="%s"', $name, htmlentities($value));
            return $this;
        } else {
            return implode(' ', $this->data);
        }
    }
    public function Label($label = null) {
        if (!is_null($label)) {
            $this->label = $label;
            return $this;
        } else {
            return $this->label;
        }
    }
    public function Value($value = null) {
        if (!is_null($value)) {
            $this->value = $value;
            return $this;
        } else {
            return $this->value;
        }
    }
}