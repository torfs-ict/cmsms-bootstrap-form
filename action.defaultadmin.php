<?php

/** @var BootstrapForm $this */
/** @var array $params */
/** @var string $id */
if (!isset($gCms)) exit;

if (array_key_exists('cancel', $params)) {
    $this->Redirect($id, 'defaultadmin');
    return;
}

$values = [];
$properties = [
    'street' => 'Straat & huisnummer',
    'zipcode' => 'Postcode',
    'locality' => 'Woonplaats',
    'phone' => 'Telefoonnummer',
    'email' => 'E-mailadres',
    'vat' => 'BTW nummer'
];

foreach($properties as $property => $caption) {
    if (array_key_exists('submit', $params)) {
        $this->SetPreference($property, $params[$property]);
    } else {
        $values[$property] = $this->GetPreference($property);
    }
}
if (array_key_exists('submit', $params)) {
    $this->SetMessage('Uw contactgegevens werden succesvol opgeslagen.');
    $this->Redirect($id, 'defaultadmin');
    return;
}

$this->assign('properties', $properties);
$this->assign('values', $values);
echo $this->SmartyFetch('defaultadmin.tpl');