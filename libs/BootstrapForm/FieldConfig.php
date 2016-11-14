<?php

namespace BootstrapForm;

use BootstrapForm\FieldAnnotation\Captcha;
use BootstrapForm\FieldAnnotation\Display;
use BootstrapForm\FieldAnnotation\Functionality;
use BootstrapForm\FieldAnnotation\Validation;

class FieldConfig {
    /** @var Captcha */
    public $captcha;
    /** @var Display */
    public $display;
    /** @var Functionality */
    public $functionality;
    /** @var Validation */
    public $validation;

    public function attach(\cms_mailer $mail, $files) {
        if ($this->display->input != 'file') return;
        if (empty($files)) return;
        if (!array_key_exists('name', $files)) return;
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $file = [];
            foreach(['name', 'type', 'tmp_name', 'error', 'size'] as $key) {
                $file[$key] = $files[$key][$i];
            }
            if ($file['error'] != UPLOAD_ERR_OK) continue;
            $mail->AddAttachment($file['tmp_name'], $file['name'], 'base64', $file['type']);
        }
    }

    public function convert(Form $form, $field) {
        if ($this->display->input == 'captcha') return null;
        $value = trim($form->$field);
        if (empty($value)) return null;
        if ($this->display->input == 'select') {
            $options = $form->GetSelectOptions($field);
            foreach($options as $option) {
                if ($option->Value() != $value) continue;
                $value = $option->Label();
                break;
            }
        }
        return sprintf("%s:\n  %s\n\n", $this->display->label, str_replace("\n", "\n  ", $value));
    }

    /**
     * @param FieldAnnotation $annotation
     * @return $this
     */
    public function set(FieldAnnotation $annotation = null) {
        if ($annotation instanceof Captcha) {
            $this->captcha = $annotation;
        } elseif ($annotation instanceof Display) {
            $this->display = $annotation;
        } elseif ($annotation instanceof Functionality) {
            $this->functionality = $annotation;
        } elseif ($annotation instanceof Validation) {
            $this->validation = $annotation;
        }
        return $this;
    }

    public function validate($value) {
        $value = trim($value);
        if ($this->captcha instanceof Captcha) {
            return $this->captcha->validate($this->display, $value);
        }
        if ($this->validation instanceof Validation) {
            return $this->validation->validate($this->display, $value);
        }
        return true;
    }
}