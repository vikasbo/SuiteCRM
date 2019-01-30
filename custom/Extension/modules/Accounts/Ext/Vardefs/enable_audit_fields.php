<?php

$target_fields = array(
    'name',
    'account_type',
    'industry',
    'annual_revenue',
    'phone_fax',
    'billing_address_street',
    'billing_address_city',
    'billing_address_state',
    'billing_address_postalcode',
    'billing_address_country',
    'rating',
    'phone_office',
    'phone_alternate',
    'website',
    'ownership',
    'employees',
    'ticker_symbol',
    'shipping_address_street',
    'shipping_address_city',
    'shipping_address_state',
    'shipping_address_postalcode',
    'shipping_address_country',
    'sic_code',
);


foreach($target_fields as $field){
    $dictionary['Account']['fields'][$field]['audited'] = true;
}