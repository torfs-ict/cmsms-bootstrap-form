<?php

namespace BootstrapForm\FieldAnnotation;
use BootstrapForm\FieldAnnotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Validation extends FieldAnnotation {
    public $email = false;
    public $maxChars = null;
    public $minChars = null;
    public $regex = null;
    public $required = false;

    public function validate(Display $display, $value) {
        // required
        if ($display->input != 'captcha' && $this->required === true && empty($value)) return 'Dit veld is vereist.';
        // email
        if ($this->email === true && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) return 'Dit veld vereist een geldig e-mailadres.';
        // maxChars
        if (!is_null($this->maxChars) && strlen($value) > $this->maxChars) return sprintf('Dit veld kan maximum %d karakters bevatten.', $this->maxChars);
        // minChars
        if (!is_null($this->minChars) && strlen($value) < $this->minChars) return sprintf('Dit veld vereist minstens %d karakters.', $this->minChars);
        // regex
        if (!is_null($this->regex) && preg_match($this->regex, $value) !== 1) return 'De waarde van dit veld is niet in het juiste formaat.';

        return true;
    }


}