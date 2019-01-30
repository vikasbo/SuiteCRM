<?php

define('sugarEntry', true);
define('ENTRY_POINT_TYPE', 'api');

require_once 'include/entryPoint.php';
global $sugar_config;

$sapi_type = php_sapi_name();
if (substr($sapi_type, 0, 3) != 'cli') {
    sugar_die("this script is CLI only.");
}

$configurator = new Configurator();
$configurator->config['http_referer']['actions'][] = 'googleAccountSettings';
$configurator->handleOverride();