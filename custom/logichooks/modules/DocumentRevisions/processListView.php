<?php

/**
 * Class DocumentRevisionsProcessRecord
 */
class DocumentRevisionsProcessRecord {


    /**
     * @param DocumentRevision $bean
     * @param $event
     * @param $arguments
     */
    public function processList(DocumentRevision $bean, $event, $arguments){

        $url = 'index.php?entryPoint=downloadDriveFiles&id='.$bean->id;
        $bean->gdrive_download_url = '<a href="'.$url.'">'.$bean->filename.'</a>';
    }
}