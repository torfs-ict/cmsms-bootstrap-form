<?php

namespace BootstrapForm;

class ContactData {
    public $street;
    public $zipcode;
    public $locality;
    public $phone;
    public $email;
    public $vat;

    public function __construct() {
        $module = \BootstrapForm::GetInstance();
        foreach(['street', 'zipcode', 'locality', 'phone', 'email', 'vat'] as $preference) {
            $this->$preference = $module->GetPreference($preference);
        }
    }
}