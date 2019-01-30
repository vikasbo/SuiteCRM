<?php


require_once("custom/include/Google/GoogleDriveHelper.php");

$gdh = new GoogleDriveHelper();

if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
    $revision = BeanFactory::getBean('DocumentRevisions', $_REQUEST['id']);

    $fileMeata = array(
        'gdrive_id'   => $revision->gdrive_id,
        'downloadPath' => 'upload/'.$revision->id,
        'exportLink' => $revision->gdrive_download_url,
    );

    //download file
    $gdh->downloadFile($fileMeata);
    set_time_limit(0);

    downloadFile($fileMeata['downloadPath'], $revision->filename, $revision->file_ext);

}

/**
 * @param $file
 * @param $name
 * @param string $mime_type
 *
 * Method to download the file to browser
 */
function downloadFile($file, $name, $file_ext)

{
    if(!is_readable($file)) die('File not found or inaccessible!');
    $size = filesize($file);
    $name = rawurldecode($name);
    $known_mime_types=array(
        "htm" => "text/html",
        "exe" => "application/octet-stream",
        "zip" => "application/zip",
        "doc" => "application/msword",
        "docx" => "application/msword",
        "jpg" => "image/jpg",
        "php" => "text/plain",
        "xls" => "application/vnd.ms-excel",
        "ppt" => "application/vnd.ms-powerpoint",
        "gif" => "image/gif",
        "pdf" => "application/pdf",
        "txt" => "text/plain",
        "html"=> "text/html",
        "png" => "image/png",
        "jpeg"=> "image/jpg"
    );



    //get file mime type

    $file_extension = ($file_ext == '' || $file_ext == null) ? strtolower(substr(strrchr($file,"."),1)) : $file_ext;

    if(array_key_exists($file_extension, $known_mime_types)){
        $mime_type=$known_mime_types[$file_extension];
    } else {
        $mime_type="application/force-download";
    };

    @ob_end_clean();

    if(ini_get('zlib.output_compression')) {
        ini_set('zlib.output_compression', 'Off');
    }
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header("Content-Transfer-Encoding: binary");
    header('Accept-Ranges: bytes');
    header("Content-Length: ".$size);

    ob_clean();
    flush();
    readfile($file);
    unlink($file);
}

