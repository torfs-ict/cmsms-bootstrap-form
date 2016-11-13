<?php

namespace BootstrapForm;

use BootstrapForm\FieldAnnotation\Captcha;
use BootstrapForm\FieldAnnotation\Display;
use BootstrapForm\FieldAnnotation\Functionality;
use BootstrapForm\FieldAnnotation\Validation;

abstract class Form {
    /**
     * @var FieldConfig[]
     */
    protected $fields = [];
    /** @var \BootstrapForm */
    protected $mod;
    abstract function GetFrom();
    abstract function GetFromName();
    abstract function GetTo();
    abstract function GetCC();
    abstract function GetBCC();
    abstract function GetSubject();

    /**
     * @param $field
     * @return SelectOption[]
     */
    abstract function GetSelectOptions($field);

    private function ParseFields() {
        foreach($this as $field => $value) {
            if (in_array($field, ['fields', 'mod'])) continue;
            $config = new FieldConfig();
            $config
                ->set(Captcha::GetFor($this, $field))
                ->set(Display::GetFor($this, $field))
                ->set(Functionality::GetFor($this, $field))
                ->set(Validation::GetFor($this, $field));
            $this->fields[$field] = $config;
        }
    }

    private function ParsePost() {
        $id = $this->mod->ActionId();
        if (!array_key_exists($id . 'form', $_POST)) return;
        if ($_POST[$id . 'form'] != get_called_class()) return;
        foreach($this as $field => $value) {
            if (!array_key_exists($id . $field, $_POST)) continue;
            $this->$field = $_POST[$id . $field];
        }
    }

    public function __construct() {
        $this->mod = \BootstrapForm::GetInstance();
        $this->ParsePost();
        $this->ParseFields();
        if ($this->IsSubmit()) {
            $this->Send();
        }
    }

    public function GetName() {
        return get_called_class();
    }

    public function GetValue($field) {
        return $this->$field;
    }

    public function GetFields() {
        return $this->fields;
    }

    public function IsInvalid($field = null) {
        if (!$this->IsSubmit()) return false;
        if (array_key_exists($field, $this->fields)) {
            // Check a single field
            $value = $this->$field;
            $validate = $this->fields[$field]->validate($value);
            if ($validate === true) return false;
            else return $validate;
        } else {
            // Check all fields
            foreach($this->fields as $name => $field) {
                $value = $this->$name;
                if ($field->validate($value) !== true) return 'Één of meerdere velden zijn ongeldig.';
            }
            return false;
        }
    }

    public function IsSubmit() {
        $id = $this->mod->ActionId();
        return (array_key_exists($id . 'form', $_POST) && array_key_exists($id . 'submit', $_POST));
    }

    public function IsSuccess() {
        return ($this->IsSubmit() === true && $this->IsInvalid() === false);
    }

    public function FilterName($input) {
        $rules = array("\r" => '', "\n" => '', "\t" => '', '"'  => "'", '<'  => '[', '>'  => ']');
        $name = trim(strtr($input, $rules));
        return $name;
    }
    public function FilterAddress($input) {
        $rules = array("\r" => '', "\n" => '', "\t" => '', '"'  => '', ','  => '', '<'  => '', '>'  => '');
        $email = strtr($input, $rules);
        return $email;
    }

    public function Send()
    {
        if ($this->IsSubmit() !== true || $this->IsInvalid() !== false) return false;
        $mail = new \cms_mailer();
        $tmp = \cms_siteprefs::get('mailprefs');
        if ($tmp) {
            $mailprefs = unserialize($tmp);
            $from = $mailprefs['from'];
            $fromname = $mailprefs['fromuser'];
        } else {
            $from = 'noreply@torfs.org';
            $fromname = 'Torfs ICT';
        }
        // Set the subject
        $mail->SetSubject($this->GetSubject());
        $mail->IsHTML(false);
        // Setup our sender and from headers
        $mail->SetFrom($this->GetFrom());
        $mail->SetFromName($this->GetFromName());
        if (method_exists($mail, 'SetSMTPOptions')) {
            $mail->SetSMTPOptions(array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            ));
        }
        $sender = array(array($this->FilterAddress($from), $this->FilterName($fromname)));
        $header = $mail->AddrAppend('Sender', $sender);
        $mail->AddCustomHeader($header);
        // Set the recipient(s)
        try { foreach ($this->GetTo() as $address) $mail->AddAddress($address); } catch (\Exception $e) {}
        try { foreach ($this->GetCC() as $address) $mail->AddCC($address); } catch (\Exception $e) {}
        try { foreach ($this->GetBCC() as $address) $mail->AddBCC($address); } catch (\Exception $e) {}
        $body = '';
        // Generate the body
        foreach ($this->fields as $name => $field) {
            if ($name == 'submit') continue;
            $fileKey = $this->mod->ActionId() . $name;
            if (array_key_exists($fileKey, $_FILES)) $field->attach($mail, $_FILES[$fileKey]);
            $line = $field->convert($this, $name);
            $check = trim($line);
            if (!empty($check)) $body .= $line;
        }
        $mail->SetBody($body);
        return $mail->Send();
    }
}