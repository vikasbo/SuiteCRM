<?php
//setup Google authentication for Drive Integration
$admin_option_defs = array();
$admin_option_defs['Administration']['google_account_settings']= array('Administration','LBL_GOOGLE_ACCOUNT_SETTINGS_TITLE','LBL_GOOGLE_ACCOUNT_SETTINGS_DESC','./index.php?module=Administration&action=googleAccountSettings');
$admin_group_header[] = array('LBL_GOOGLE_ACCOUNT_SETUP_HEADER', '', false, $admin_option_defs, '');
