<?php

namespace BootstrapForm\FieldAnnotation;
use BootstrapForm\FieldAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Validation extends FieldAnnotation {
    public $maxChars = null;
    public $minChars = null;
    public $regex = null;
    public $required = false;
}