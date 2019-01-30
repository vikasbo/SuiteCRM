<?php

$hook_array['before_save'][] = array(
    '5',
    'This hook will link account to opp and related contact',
    'custom/logichooks/modules/Accounts/AccountsBeforeSave.php',
    'AccountsBeforeSave',
    'callBeforeSave'
);

$hook_array['before_save'][] = array(
    '20',
    'This hook will call simple billing api to create a new customer',
    'custom/logichooks/modules/Accounts/AccountsBeforeSave.php',
    'AccountsBeforeSave',
    'callBillingApi'
);
