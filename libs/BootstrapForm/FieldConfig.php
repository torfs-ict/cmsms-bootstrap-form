<?php

namespace BootstrapForm;

class FieldConfig {
    protected $autoSubmit = false;
    protected $captcha = null;
    protected $classes = [];
    protected $classesOnly = [];
    protected $input = 'text';
    protected $label = 'Unknown field';
    protected $minChars = null;
    protected $required = false;
    protected $row = false;
    protected $upload = null;
    protected $visible = true;
    // Dummy fields
    protected $col = null;
    protected $class = null;
    public function __get($property) {
        if (!property_exists($this, $property)) {
            throw new \InvalidArgumentException(sprintf('Property "%s" is unknown.', $property));
        }
        return $this->$property;
    }
    public function __set($property, $value) {
        if (!property_exists($this, $property)) {
            throw new \InvalidArgumentException(sprintf('Property "%s" is unknown.', $property));
        }
        switch($property) {
            case 'autoSubmit':
            case 'required':
            case 'row':
            case 'visible':
                $this->$property = cms_to_bool($value);
                break;
            case 'class':
                $this->classes[] = $value;
                $this->classesOnly[] = $value;
                break;
            case 'col':
                $this->classes[] = 'col-' . $value;
                break;
            case 'minChars':
            case 'upload':
                if (is_null($value)) $this->$property = null;
                else $this->$property = (int)$value;
                break;
            default:
                $this->$property = (string)$value;
                break;
        }
    }
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