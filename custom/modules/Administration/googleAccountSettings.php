<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $current_user;
global $mod_strings;
global $app_list_strings;
global $app_strings;
global $theme;

if (!is_admin($current_user)) sugar_die("Unauthorized access to administration.");

require_once('modules/Administration/Administration.php');
require_once 'custom/include/Google/GoogleDriveHelper.php';
require_once 'custom/include/Google/lib/GoogleOauthHandler.php';

echo getClassicModuleTitle(
    "Administration",
    array(
        "<a href='index.php?module=Administration&action=index'>".translate('LBL_MODULE_NAME','Administration')."</a>",
        $mod_strings['LBL_GOOGLE_ACCOUNT_SETUP_HEADER'],
    ),
    false
);

$sugar_smarty	= new Sugar_Smarty();
$administration = new Administration();

if(isset($_REQUEST['athenticate']) && $_REQUEST['athenticate'] == true) {
    if(isset($_REQUEST['save']) && $_REQUEST['save'] == true) {
        //save the values in config table
        $administration->saveSetting('google_auth', 'USER_EMAIL', $_POST['USER_EMAIL']);
        $administration->saveSetting('google_auth', 'PARENT_FOLDER_NAME', $_POST['PARENT_DIR']);
        $administration->saveSetting('google_auth', 'CLIENT_ID', $_POST['CLIENT_ID']);
        $administration->saveSetting('google_auth', 'CLIENT_SECRET', $_POST['CLIENT_SECRET']);
        $administration->saveSetting('google_auth', 'REDIRECT_URI', $_POST['REDIRECT_URI']);
        $administration->saveSetting('google_auth', 'SCOPES', $_POST['SCOPES']);
    }
	//Implementing Google Authentication with Saved credentials
    global $current_user;

    //instantiate an auth handler
    $auth_handler=new GoogleOauthHandler();
    $client = new Google_Client();

    //setup client with values from config table
    $credentials = $administration->retrieveSettings('google_auth');
    $client->setClientId($credentials->settings['google_auth_CLIENT_ID']);
    $client->setClientSecret($credentials->settings['google_auth_CLIENT_SECRET']);
    $client->setRedirectUri($credentials->settings['google_auth_REDIRECT_URI']);
    $client->setScopes($credentials->settings['google_auth_SCOPES']);
    

    //create auth URL
    $authUrl = $client->createAuthUrl();
    $authUrl.="&user_id=".$_POST['USER_EMAIL'];//creating auth url according to current setting

    if(isset($_GET['code'])){ //Executed when google calls redirect uri with the acces code
        $jsonCredentials = json_decode($client->authenticate());
        if(!empty($jsonCredentials) && $auth_handler->saveOauth2Credentials($jsonCredentials, $_GET['code'])){

            //once authentication is successfull, create default folder within the Google Drive
            $credentials = $administration->retrieveSettings('google_auth');
  
            $gdh = new GoogleDriveHelper();
            $folderObj = $gdh->createFolder(array('title' => $credentials->settings['google_auth_PARENT_FOLDER_NAME'], 'description' => 'CRM documents reside inside this directory'));
            if(!empty($folderObj)) {
                $administration->saveSetting('google_auth', 'parent_folder_id', $folderObj->id);
            }
            
	    //empty user folder ids
	    $GLOBALS['db']->query("update users set gd_folder_id = null where deleted=0");

            //show message authentication done and redirect user where you want
            echo "<p style=\"color: #179817;font-weight: bold;margin-top: 10px;\">User account successfully authenticated ! <br><a href='/index.php?module=Administration&action=index'>Click here to go to admin page</a></p>";
            $sugar_smarty->assign('AUTH_SUCCESSFULL', true);
        }else{
            echo "Error occurred please <a href='$authUrl'>try again</a>";
        }
    }else{
        if(isset($_GET['error'])){
            echo "Error occurred please <a href='$authUrl'>try again</a>";
        }else{
            //Request authorization
            header("Location: ".$authUrl);
        }
    }

} else {
    $credentials = $administration->retrieveSettings('google_auth');
    $sugar_smarty->assign('USER_EMAIL', $credentials->settings['google_auth_USER_EMAIL']);
    $sugar_smarty->assign('PARENT_DIR', $credentials->settings['google_auth_PARENT_FOLDER_NAME']);
    $sugar_smarty->assign('CLIENT_ID', $credentials->settings['google_auth_CLIENT_ID']);
    $sugar_smarty->assign('CLIENT_SECRET', $credentials->settings['google_auth_CLIENT_SECRET']);
    $sugar_smarty->assign('SCOPES', $credentials->settings['google_auth_SCOPES']);
    $sugar_smarty->assign('STATE', $credentials->settings['google_auth_STATE']);
    $sugar_smarty->assign('REDIRECT_URI', $credentials->settings['google_auth_REDIRECT_URI']);
}

$sugar_smarty->assign('MOD', $mod_strings);
$sugar_smarty->assign('APP', $app_strings);
$sugar_smarty->assign('APP_LIST', $app_list_strings);
$sugar_smarty->assign('LANGUAGES', get_languages());
$sugar_smarty->assign("JAVASCRIPT",get_set_focus_js());
$sugar_smarty->assign('error', $errors);

$buttons =  <<<EOQ
    <input title="{$app_strings['LBL_SAVE_BUTTON_TITLE']}"
                       accessKey="{$app_strings['LBL_SAVE_BUTTON_KEY']}"
                       class="button primary"
                       type="submit"
                       name="save"
                       onclick="return check_form('ConfigureGoogleSettings');"
                       value="  {$mod_strings['LBL_AUTHENTICATE_GA_BUTTON_LABEL']}  " >
                &nbsp;<input title="{$mod_strings['LBL_CANCEL_BUTTON_TITLE']}"  onclick="document.location.href='index.php?module=Administration&action=index'" class="button"  type="button" name="cancel" value="  {$app_strings['LBL_CANCEL_BUTTON_LABEL']}  " >
EOQ;

$sugar_smarty->assign("BUTTONS",$buttons);
$sugar_smarty->display('custom/modules/Administration/googleAccountSettings.tpl');

$javascript = new javascript();
$javascript->setFormName('ConfigureGoogleSettings');
echo $javascript->getScript();
