<?php

if(!defined('sugarEntry')) die('not a valid entry point');

//Redirect back to admin authentication screen with auth code
SugarApplication::Redirect("index.php?module=Administration&action=googleAccountSettings&athenticate=1&code=".$_GET['code']);
