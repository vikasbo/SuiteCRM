<?php

$target_fields = array(
    'name',
    'description',
    'lead_source',
    'amount',
    'next_step',
);


foreach($target_fields as $field){
    $dictionary['Opportunity']['fields'][$field]['audited'] = true;
}