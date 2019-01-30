<?php

$target_fields = array(
    'description',
    'salutation',
    'first_name',
    'last_name',
    'title',
    'department',
    'phone_home',
    'phone_mobile',
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
    'converted',
    'refered_by',
    'lead_source_description',
    'status_description',
    'account_name',
    'account_description',
    'opportunity_name',
    'opportunity_amount',
    'birthdate',
    'portal_name',
    'portal_app',
    'website',
);


foreach($target_fields as $field){
    $dictionary['Lead']['fields'][$field]['audited'] = true;
}