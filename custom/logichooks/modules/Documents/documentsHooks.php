<?php


require_once("custom/include/Google/GoogleDriveHelper.php");

/**
 * Class documentsHooks
 */
class documentsHooks
{

    /**
     * @param $bean
     * @param $event
     * @param $arguments
     */
    public function afterSave($bean, $event, $arguments){


        $query = "select gdrive_download_url download_url from document_revisions where document_id = '".$bean->id."' order by date_entered desc limit 1;
 ";
        $results = $GLOBALS['db']->query($query);
        $revision = $GLOBALS['db']->fetchByAssoc($results);

        if(isset($revision['download_url']) && $revision['download_url'] != "" && $revision['download_url'] != NULL){
            $query = "UPDATE documents SET gdrive_download_url = '".$revision['download_url']."' WHERE id='".$bean->id."'";
            $GLOBALS['db']->query($query);
        }
    }


    /**
     * @param $bean
     * @param $event
     * @param $arguments
     */

    public function afterRelationshipDelete($bean, $event, $arguments){

        if($arguments['relationship'] == "document_revisions"){
            $revision = BeanFactory::getBean("DocumentRevisions", $arguments['related_id']);
            $gdh = new GoogleDriveHelper();
            $fileObj = $gdh->trashDocument($revision);
            $GLOBALS['log']->debug("FileObj: ".print_r($fileObj,true));
        }

    }
}