<?php

$target_fields = array(
    'description',
    'salutation',
    'first_name',
    'last_name',
    'title',
    'department',
    'do_not_call',
    'phone_home',
    'phone_mobile',
    'phone_work',
    'phone_other',
    'phone_fax',
    'primary_address_street',
    'primary_address_city',
    'primary_address_state',
    'primary_address_postalcode',
    'primary_address_country',
    'alt_address_street',
    'alt_address_city',
    'alt_address_state',
    'alt_address_postalcode',
    'alt_address_country',
    'assistant',
    'assistant_phone',
    'lead_source',
    'birthdate',
    'joomla_account_id',
    'portal_account_disabled',
    'portal_user_type',
);


foreach($target_fields as $field){
    $dictionary['Contact']['fields'][$field]['audited'] = true;
}