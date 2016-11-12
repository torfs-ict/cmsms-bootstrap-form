<?php

namespace BootstrapForm\FieldAnnotation;
use BootstrapForm\FieldAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Functionality extends FieldAnnotation {
    /** @var bool Whether or not the form should auto-submit upon change */
    public $autoSubmit = false;
    /** @var int Number of files allowed to upload */
    public $upload = null;
    /** @var bool Mark this field as a Captcha validator */
    public $captcha = false;
}