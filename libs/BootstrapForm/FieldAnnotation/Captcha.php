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
}