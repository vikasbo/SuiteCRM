<?php

require_once("custom/include/Google/GoogleDriveHelper.php");

class GdriveHookClass
{

    /**
     * @param $bean
     * @param $event
     * @param $arguments
     */
    public function beforeSave($bean, $event, $arguments){
        if(empty($bean->fetched_row['id']) || $bean->fetched_row['id'] == NULL){
            $bean->isNew = true;
        }
    }

    /**
     * @param $bean
     * @param $event
     * @param $arguments
     */
    public function afterSave($bean, $event, $arguments)
    {
        if($bean->isNew){

            $user_folder_id = $this->getUserFolderID();
            //save document to google drive on upload
            $gdh = new GoogleDriveHelper();
            $fileObj = $gdh->addDocument($bean);
            $GLOBALS['log']->debug("FileOBj: ".print_r($fileObj,true));

            if(!empty($fileObj)) {
                $file_id = (isset($fileObj->id)) ? $fileObj->id : '';

                $file_download_url = $gdh->getDownloadUrl($fileObj);

                $query = "UPDATE {$bean->table_name} SET gdrive_download_url='".$file_download_url."', gdrive_id='".$file_id."' WHERE id='".$bean->id."'";
                $GLOBALS['db']->query($query);

                if(isset($bean->document_id) && $bean->document_id != "" && $bean->document_id != NULL){
                    $query = "UPDATE documents SET gdrive_download_url = '".$file_download_url."' WHERE id='".$bean->document_id."'";
                    $GLOBALS['db']->query($query);
                }

                //delete the local document
                unlink($GLOBALS['sugar_config']['upload_dir'].$bean->id);
            }
        }

    }

    /**
     * @return mixed
     */
    public function getUserFolderID()
    {
        global $current_user;
	$user = BeanFactory::getBean("Users", $current_user->id, array('use_cache' => false));
        //create user specific folder if doesn't exist
        if(empty($user->gd_folder_id) || $user->gd_folder_id == null) {
	    
            $gdh = new GoogleDriveHelper();
            $user_folder = $gdh->createUserFolder(array('title' => $current_user->user_name, 'description' => 'User folder.'));
            //update user
            $query = "UPDATE users SET gd_folder_id='".$user_folder->id."' WHERE id='".$current_user->id."' AND deleted=0";
            $GLOBALS['log']->fatal("Updating User google drive folder id :" . $query);
	    $GLOBALS['db']->query($query);
	    $current_user->gd_folder_id = $user_folder->id;

            return $user_folder->id;
        } else {
            $user->gd_folder_id;
        }
    }

    /**
     * @param $bean
     * @param $event
     * @param $arguments
     */
    public function afterDelete($bean, $event, $arguments)
    {
        $gdh = new GoogleDriveHelper();
        $fileObj = $gdh->trashDocument($bean);
        $GLOBALS['log']->debug("FileObj: ".print_r($fileObj,true));
    }
}

