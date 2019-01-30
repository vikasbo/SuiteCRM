<?php
require_once 'custom/include/Google/lib/oauth_credentials.php';
require_once 'modules/Users/User.php';

class GoogleOauthHandler{

	function getOauth2Credentials($credentials){
		global $sugar_config;
		$oauthCredentials = new OauthCredentials(
			$credentials['access_token'],
			isset($credentials['refresh_token'])?($credentials['refresh_token']):null,
			$credentials['created'],
			$credentials['expires_in'],
			$sugar_config['GOOGLE']['CLIENT_ID'],
			$sugar_config['GOOGLE']['CLIENT_SECRET']
		);

		return $oauthCredentials;
	}
	function saveOauth2Credentials($jsonCredentials,$authCode){
		//save google credentials in db config table
		$administration = new Administration();
		$administration->saveSetting('google_auth', 'auth_code', $authCode);
		$administration->saveSetting('google_auth', 'access_token', $jsonCredentials->access_token);
		$administration->saveSetting('google_auth', 'refresh_token', isset($jsonCredentials->refresh_token)?($jsonCredentials->refresh_token):'');
		$administration->saveSetting('google_auth', 'auth_created', $jsonCredentials->created);
		$administration->saveSetting('google_auth', 'auth_expires_in', $jsonCredentials->expires_in);
		return true;
	}
	
	function getStoredCredentials(){
		//fetch saved credentials and return in array format
		$credentials=array();
		$administration = new Administration();
		$credentials = $administration->retrieveSettings('google_auth');
		if($credentials->settings['google_auth'])
		{
			$credentials=array(
				'access_token'	=> $credentials->settings['google_auth_access_token'],
				'refresh_token'	=> $credentials->settings['google_auth_refresh_token'],
				'created'		=> $credentials->settings['google_auth_auth_created'],
				'expires_in'	=> $credentials->settings['google_auth_auth_expires_in'],
			);
		}
		return $credentials;
	}

}
?>
