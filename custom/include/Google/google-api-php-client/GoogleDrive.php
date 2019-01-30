<?php
require_once 'custom/include/Google/google-api-php-client/src/contrib/Google_DriveService.php';

	class GoogleDrive{
		var $q='';
		var $maxResults=100000;//by default
		
		/**
		 * setter function for $q class var
		*/
		function setQ($q=array()){
			if(!empty($q)){
				$q=implode(' and ',$q);
				$this->q=$q;
			}else{
				$this->q='';
			}
		}
		/*
		 * setter function for $maxResults class var
		*/
		function setMaxResults($r){
			if(!empty($r) && $r > 0)
			$this->maxResults=$r;
		}
		/*
		 * getter function for $q class var
		*/
		function getQ(){
			return $this->q;
		}
		/**
		 * getter function for $maxResults class var
		*/
		function getMaxResults(){
			return $this->maxResults;
		}
		
		function getAdditionalParams(){
			return array("maxResults"=>$this->maxResults,"q"=>$this->q);
		}
		/**
		 * add new file.
		 *
		 * @param Google_DriveService $service Drive API service instance.
		 * @param string $title Title of the file to insert, including the extension.
		 * @param string $description Description of the file to insert.
		 * @param string $parentId Parent folder's ID.
		 * @param string $mimeType MIME type of the file to insert.
		 * @param string $filename Filename of the file to insert.
		 * @return Google_DriveFile The file that was inserted. false is returned if an API error occurred.
		 */
		function addFile($service, $title, $description, $parentId, $mimeType, $filename) {
			$file = new Google_DriveFile();
			$file->setTitle($title);
			$file->setDescription($description);
			$file->setMimeType($mimeType);

			// Set the parent folder.
			if ($parentId != null) {
                $parent = new Google_ParentReference();
				$parent->setId($parentId);
				$file->setParents(array($parent));
			}

			try {
				$data = sugar_file_get_contents($filename);
				$convert=true;
				if(strpos($mimeType, 'application/pdf') !== false || strpos($mimeType, 'image')!==false){
					$convert=false;
				}
				$createdFile = $service->files->insert($file, array(
				'data' => $data,
				'mimeType' => $mimeType,
				'convert' => $convert,
				));
				
				return $createdFile;
			} catch (Exception $e) {
				$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage()); 
				return false;
			}
		}
		/**
		 * Update an existing file's metadata and content.
		 *
		 * @param Google_DriveService $service Drive API service instance.
		 * @param string $fileId ID of the file to update.
		 * @param string $newTitle New title for the file.
		 * @param string $newDescription New description for the file.
		 * @param string $newMimeType New MIME type for the file.
		 * @param string $newFilename Filename of the new content to upload.
		 * @param bool $newRevision Whether or not to create a new revision for this file.
		 * @return Google_DriveFile The updated file. false is returned if an API error occurred.
		 */
		function updateFile($service, $fileId, $newTitle, $newDescription, $newMimeType, $newFileName, $newRevision,$modified_date) {
			try {
				// First retrieve the file from the API.
				$file = $service->files->get($fileId);

				// File's new metadata.
				$file->setTitle($newTitle);
				$file->setDescription($newDescription);
				$file->setMimeType($newMimeType);

				// File's new content.
				$data = sugar_file_get_contents($newFileName);

				$additionalParams = array(
				'newRevision' => $newRevision,
				'data' => $data,
				'mimeType' => $newMimeType,
				'setModifiedDate'=>true,
				);

				// Send the request to the API.
				$updatedFile = $service->files->update($fileId, $file, $additionalParams);
				return $updatedFile;
			} catch (Exception $e) {
				if($e->getCode() == 404){
					//$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage());
					return '404';// return 404 means not found, may be dont have permissions, permissions have been changed or file has been deleted
				}else{
					$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage());
					return false;
				}
			}
		}
		/*
		*get updated/modified list of files from drive
		*
		*@param Google_DriveService $service Drive API service instance.
		*@return Array containing metadata of updated files
		*/	
		function getUpdatedFiles($service){
			$files_updated=array();
			try{

				$files = $service->files->listFiles($this->getAdditionalParams());
				
				if(is_object($files)){// useObjects is true for google client
					$items=$files->getItems();
					foreach($items as $item){
						$files_updated[]=$this->objToArray($item);
					}
				}else {// means useObjects is false by default and google will return arrays
					$files_updated=$files['items'];
				}
				return $files_updated;
			}
			catch (Exception $e){
				$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage());
				return false;
			}
			
		}
		/**
		* Download a file's content.
		*
		* @param Google_DriveService $service Drive API service instance.
		* @param Array $file_meta array containing meta data for file to be downloaded
		* 
		*/
		function downloadFile($service,$file_meta) {

			try{
				$file = $service->files->get($file_meta['gdrive_id']);
				$downloadUrl = $file->getDownloadUrl();

				if ($downloadUrl) {
					$request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
					$httpRequest = Google_Client::$io->authenticatedRequest($request);
					if ($httpRequest->getResponseHttpCode() == 200) {
						$data=$httpRequest->getResponseBody();
						//sugar_put_contents
						if(!empty($data))
						sugar_file_put_contents($file_meta['downloadPath'], $data);
						return true;
					} else {
						// An error occurred.
						$GLOBALS['log']->fatal("Unable to download file: gdrive_id=".$file_meta['gdrive_id']);
						return false;
					}
				} else {

					if(!empty($file_meta['exportLink'])){
						$request = new Google_HttpRequest($file_meta['exportLink'], 'GET', null, null);
						$httpRequest = Google_Client::$io->authenticatedRequest($request);
						if ($httpRequest->getResponseHttpCode() == 200) {
							$data=$httpRequest->getResponseBody();
							if(!empty($data))
							sugar_file_put_contents($file_meta['downloadPath'], $data);
							return true;
						} else {
							// An error occurred.
							$GLOBALS['log']->fatal("Unable to download file: gdrive_id=".$file_meta['gdrive_id']);
							return false;
						}
					}else{
						// The file doesn't have any content stored on Drive.
						$GLOBALS['log']->fatal("The file is not downloadable/exportable or doesn't have any content stored on Drive ,gdrive_id=".$file_meta['gdrive_id']);
						return false;
					}
				}
			} catch (Exception $e) {
				$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage());
				return false;
			}
		}
		
		/**
		 * Move a file to the trash.
		 *
		 * @param Google_DriveService $service Drive API service instance.
		 * @param String $fileId ID of the file to trash.
		 * @return Google_DriveFile The updated file. false is returned if an API error occurred.
		 */
		function trashFile($service, $fileId) {
			try {
				return $service->files->trash($fileId);
			} catch (Exception $e) {
				$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage()); 
			}
			return false;
		}
		
		/**
		 * Permanently delete a file, skipping the trash.
		 *
		 * @param Google_DriveService $service Drive API service instance.
		 * @param String $fileId ID of the file to delete.
		 */
		function deleteFile($service, $fileId) {
			try {
				$service->files->delete($fileId);
			} catch (Exception $e) {
				$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage()); 
			}
		}
		/**
		 * get trashed files
		 *
		 * @param Google_DriveService $service Drive API service instance.
		 * @return Array containg metadata of trashed files
		 */	
		function getTrashedFiles($service){
			$files_trashed=array();
			try{
				
				$files = $service->files->listFiles(array("q"=>"trashed=true"));
				
				if(is_object($files)){// useObjects is true for google client
					$items=$files->getItems();
					foreach($items as $item){
						$files_trashed[]=$this->objToArray($item);
					}
				}else {// means useObjects is false by default and google will return arrays
					$files_trashed=$files['items'];
				}
				return $files_trashed;
			}
			catch (Exception $e){
				$GLOBALS['log']->fatal("Exception Occurred: ". $e->getMessage());
				return false;
			}
		}
		
		/*
		*convert a complete object to an associative array
		*@param Object $obj object of file
		*@return Array $array 
		*/
		function objToArray($obj){

			$json  = json_encode($obj);
			$array = json_decode($json, true);
			return $array;
	
		}
	}
	
	/*1011*/
?>