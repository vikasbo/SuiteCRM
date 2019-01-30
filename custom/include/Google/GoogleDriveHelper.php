<?php

require_once("Zend/Gdata/ClientLogin.php");
require_once('modules/Administration/Administration.php');
if(sugar_is_file('custom/include/Google/google-api-php-client/GoogleDrive.php'))
    require_once "custom/include/Google/google-api-php-client/GoogleDrive.php";
if (sugar_is_file('custom/include/Google/google-api-php-client/src/Google_Client.php')) {
    require_once 'custom/include/Google/google-api-php-client/src/Google_Client.php';
    require_once 'custom/include/Google/google-api-php-client/src/contrib/Google_DriveService.php';
    require_once 'custom/include/Google/lib/GoogleOauthHandler.php';
}

/** GD Helper Class **/
class GoogleDriveHelper
{
    public $google_client;
    public $auth_handler;
    public $jsonCredentials;
    public $drive_service;

    public $mimeTypeMapping = array(
        'application/vnd.google-apps.document' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.google-apps.spreadsheet' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.google-apps.presentation' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    );

    public function __construct()
    {

        $administration = new Administration();
        $credentials = $administration->retrieveSettings('google_auth');

        //instantiate google client and auth handler
        $this->google_client = new Google_Client();
        $this->auth_handler = new GoogleOauthHandler();
        //set
        $this->google_client->setClientId($credentials->settings['google_auth_CLIENT_ID']);
        $this->google_client->setClientSecret($credentials->settings['google_auth_CLIENT_SECRET']);
        $this->google_client->setRedirectUri($credentials->settings['google_auth_REDIRECT_URI']);
        $this->google_client->setScopes($credentials->settings['google_auth_SCOPES']);

        $this->jsonCredentials = $this->auth_handler->getStoredCredentials();
	$GLOBALS['log']->debug("Stored Google Auth Credentials " . print_r($this->jsonCredentials, true));
    }

    public function setupDrive()
    {
        if(!empty($this->jsonCredentials)) {
            //set oauth credentials
            $oauthCredentials = $this->auth_handler->getOauth2Credentials($this->jsonCredentials);
            $this->google_client->setAccessToken($oauthCredentials->toJson());
            if ($this->google_client->getAccessToken()) {
                $this->drive_service = new Google_DriveService($this->google_client);
                $this->google_client->setUseObjects(true);
                return true;
            } else {
                $GLOBALS['log']->fatal("Error occured while getting google access token");
                return false;
            }
        } else {
            $GLOBALS['log']->fatal("No google auth credentials saved.");
            return false;
        }
    }

    public function addDocument($bean)
    {
        if($this->setupDrive()) {
            //instantiate GoogleDrive class and call addFile
            $GD = new GoogleDrive();
            $doc_bean = BeanFactory::getBean("Documents", $bean->document_id);
            $fileObj = $GD->addFile($this->drive_service, $bean->filename, $doc_bean->description, $GLOBALS['current_user']->gd_folder_id, $bean->file_mime_type, $GLOBALS['sugar_config']['upload_dir'] . $bean->id);
            $GLOBALS['log']->debug("Uploaded File object " . print_r($fileObj, true));
	    return $fileObj;
        }
    }

    public function trashDocument($bean)
    {
        if($this->setupDrive()) {
            $GD = new GoogleDrive();
            $fileObj = $GD->trashFile($this->drive_service, $bean->gdrive_id);
            $GLOBALS['log']->debug("Trashed File object " . print_r($fileObj, true));
	    return $fileObj;
        }
    }

    public function deleteDocument($bean)
    {
        if($this->setupDrive()) {
            $GD = new GoogleDrive();
            $fileObj = $GD->deleteFile($this->drive_service, $bean->gdrive_id);
	    $GLOBALS['log']->debug("Deleted File object " . print_r($fileObj, true));
            return $fileObj;
        }
    }

    public function getDownloadUrl($fileObj)
    {
        if(isset($this->mimeTypeMapping[$fileObj->mimeType])) {
            return $fileObj->exportLinks[$this->mimeTypeMapping[$fileObj->mimeType]];
        } else {
            return $fileObj->webContentLink;
        }
    }

    public function downloadFile($fileMeta){
        if($this->setupDrive()) {
            $GD = new GoogleDrive();
            $fileObj = $GD->downloadFile($this->drive_service, $fileMeta);
            $GLOBALS['log']->debug("Downloaded File object " . print_r($fileObj, true));

        }
    }


    /**
     * add new folder
     * @param Array $folder_meta containing 'title' and 'description' of the folder
     */
    public function createFolder($folder_meta)
    {
        if($this->setupDrive()) {
            $folder = new Google_DriveFile();
            $folder->setTitle($folder_meta['title']);
            $folder->setDescription($folder_meta['description']);
            $folder->setMimeType('application/vnd.google-apps.folder');
            $createdFolder = $this->drive_service->files->insert($folder, array(
                'mimeType' => 'application/vnd.google-apps.folder',
            ));
            return $createdFolder;
        }
    }

    /**
     * add new child folder
     * @param Array $folder_meta containing 'title' and 'description' of the folder
     * @return Object $createdFolder
     */
    public function createUserFolder($folder_meta)
    {
        if($this->setupDrive()) {
            $administration = new Administration();
            $administration->retrieveSettings('google_auth');
            if(!empty($administration->settings['google_auth_parent_folder_id'])){
                $parentId = $administration->settings['google_auth_parent_folder_id'];
                $user_folder = new Google_DriveFile();
                $user_folder->setTitle($folder_meta['title']);
                $user_folder->setDescription($folder_meta['description']);
                $user_folder->setMimeType('application/vnd.google-apps.folder');
                $parent_ref = new Google_ParentReference();
                $parent_ref->setId($parentId);
                $user_folder->setParents(array($parent_ref));
                $createdFolder = $this->drive_service->files->insert($user_folder, array(
                    'mimeType' => 'application/vnd.google-apps.folder',
                ));
                return $createdFolder;
            } else {
                $GLOBALS['log']->fatal("No parent folder exists, file will be created without child folder");
                return false;
            }
        }
    }
}
