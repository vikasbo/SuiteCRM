<?php

/**
 * Class DocumentProcessRecord
 */
class DocumentProcessRecord {


    /**
     * @param Document $bean
     * @param $event
     * @param $arguments
     */
    public function processList(Document $bean, $event, $arguments){
        $url = 'index.php?entryPoint=downloadDriveFiles&id='.$bean->document_revision_id;
        $bean->gdrive_download_url = '<a href="'.$url.'">'.$bean->filename.'</a>';
    }
}