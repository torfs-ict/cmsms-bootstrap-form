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

    protected function captcha() {
        if (fnmatch('192.*', $_SERVER['HTTP_HOST'])) return true;
        if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) return true;
        $secret = urlencode($this->captcha);
        $response = urlencode($_POST['g-recaptcha-response']);
        $ip = urlencode($_SERVER['REMOTE_ADDR']);
        $url = sprintf('https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s', $secret, $response, $ip);
        $result = json_decode(file_get_contents($url), true);
        if (!is_array($result)) return false;
        if (!array_key_exists('success', $result)) return false;
        if ($result['success'] !== true) return false;
    }

    public function attach(\cms_mailer $mail, $files) {
        if ($this->input != 'file') return;
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
        if ($this->input == 'captcha') return null;
        $value = trim($form->$field);
        if (empty($value)) return null;
        if ($this->input == 'select') {
            $options = $form->GetSelectOptions($field);
            foreach($options as $option) {
                if ($option->Value() != $value) continue;
                $value = $option->Label();
                break;
            }
        }
        return sprintf("%s:\n  %s\n\n", $this->label, str_replace("\n", "\n  ", $value));
    }

    /**
     * @param FieldAnnotation $annotation
     * @return $this
     */
    public function set(FieldAnnotation $annotation) {
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
        if ($this->input != 'captcha' && $this->required === true && empty($value)) return 'Dit veld is vereist.';
        if (!is_null($this->minChars) && strlen($value) < $this->minChars) return sprintf('Dit veld vereist minstens %d karakters.', $this->minChars);
        if ($this->input == 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) return 'Dit veld vereist een geldig e-mailadres.';
        if ($this->input == 'captcha') {
            if (!array_key_exists('g-recaptcha-response', $_POST)) $_POST['g-recaptcha-response'] = '';
            if (!$this->captcha()) return 'We konden niet verifiëren dat u geen robot bent.';
        }
        return true;
    }
}