<?php

namespace BootstrapForm\FieldAnnotation;
use BootstrapForm\FieldAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Captcha extends FieldAnnotation {
    /** @var string API key pair site key */
    public $siteKey = '';
    /** @var string API key pair secret */
    public $secret = '';

    protected function captchaValidate() {
        if (fnmatch('192.*', $_SERVER['HTTP_HOST'])) return true;
        if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) return true;
        $secret = urlencode($this->secret);
        $response = urlencode($_POST['g-recaptcha-response']);
        $ip = urlencode($_SERVER['REMOTE_ADDR']);
        $url = sprintf('https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s&remoteip=%s', $secret, $response, $ip);
        error_log(">> $url");
        $result = file_get_contents($url);
        error_log("<< $result");
        $json = json_decode($result, true);
        if (!is_array($json)) return false;
        if (!array_key_exists('success', $json)) return false;
        if ($json['success'] !== true) return false;
    }

    public function validate(Display $display, $value) {
        if ($display->input != 'captcha') return true;
        if (!array_key_exists('g-recaptcha-response', $_POST)) $_POST['g-recaptcha-response'] = '';
        $value = $_POST['g-recaptcha-response'];
        if (empty($value)) return 'U dient aan te tonen dat u geen robot bent.';
        if (!$this->captchaValidate()) return 'We konden niet verifiëren dat u geen robot bent.';
        return true;
    }
}