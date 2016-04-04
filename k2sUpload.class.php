<?php 

include "Keep2ShareAPI.php";

class k2sUpload extends Keep2ShareAPI{
	
	private $baseID;
	private $folder;
	private $srcFolder;
	public  $uploadFolder;
	public $loggedIn;




	function __construct($baseID){
		parent::__construct();	
		$this->username ="biz78ex@gmail.com";
		$this->password ="qxwv35azsc";
		$this->baseID = $baseID;
		$this->folder = date('d-m-y_h');
		$this->loggedIn = $this->login();
		$this->srcFolder = 'vids';
	}



	function createFolder($tail = '', $folder = false, $baseID = false){

		$this->baseID = empty($baseID) ? $this->baseID : $baseID;
		$this->folder = empty($folder) ? $this->folder : $folder;
		
		if($this->folderExists()){
			$this->uploadFolder = $this->folderExists();	
			return;
		}

		$this->uploadFolder = parent::createFolder( $this->folder . '_' . $tail, $this->baseID, Keep2ShareAPI::FILE_ACCESS_PUBLIC, true);
	}



	function folderExists($name = NULL){

		$list = $this->GetFilesList($this->baseID, 1000, 0, [], 'folder')['files'];
		if($list === [])return false;
		
	  $name	= empty($name) ? $this->folder : $name;

		foreach($list as $item){
			if($item['name']==$name){
				
				return $item;
			}
		}

		return false;
	}



	function getUploadFolderID(){
		return isset($this->uploadFolder) ? $this->uploadFolder['id'] : false;
	}

//Upload files in a folder one by one
	function uploadDir($dir){
		$this->srcFolder = empty($dir) ? $this->srcFolder : $dir;
		$files = scandir($this->srcFolder);	
		for($i = 2; $i <= count($files)-1; $i++){
			$this->uploadFile( $this->srcFolder . '/' . $files[$i], $this->uploadFolder['id']);
		}
	}

	

	function upload($dir = NULL, $threads = 2){
	
		$this->srcFolder = empty($dir) ? $this->srcFolder : $dir;
		$files = scandir($this->srcFolder);	
		array_splice($files, 0, (count($files)-2)*-1);

		$a = [];
		$i = $j = 0;

		while(isset($files[$i])){
			if( $i!=0 && $i % $threads==0 ) $j++;
			$a[$j][] = $this->srcFolder . $files[$i];
			$i++;
		}
/*
		foreach($a as $files){
			//var_dump($files);
			$this->multyUpload($files[0], $files[1], $files[2], $files[3], $files[4]);	
		}
*/

		var_dump($a);

	}


	function multyUpload($file1, $file2, $file3, $file4 , $file5){

    $data = $this->getUploadFormData();


    $ch1 = curl_init();


    $postFields = $data['form_data'];

		var_dump($postFields);die();

    $postFields['parent_id'] = $this->uploadFolder['id'];
    //$postFields['parent_name'] = $parent_name;

		if(isset($file1)){
    $postFields[$data['file_field']] = '@'.$file1;

    curl_setopt_array($ch1, array(
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $data['form_action'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
    ));
	}
//======================================================================

    $ch2 = curl_init();

    $postFields[$data['file_field']] = '@'.$file2;

    curl_setopt_array($ch2, array(
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $data['form_action'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
    ));

//======================================================================

    $ch3 = curl_init();

    $postFields[$data['file_field']] = '@'.$file3;

    curl_setopt_array($ch3, array(
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $data['form_action'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
    ));


//======================================================================

    $ch4 = curl_init();

    $postFields[$data['file_field']] = '@'.$file4;

    curl_setopt_array($ch4, array(
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $data['form_action'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
    ));


//======================================================================

    $ch5 = curl_init();

    $postFields[$data['file_field']] = '@'.$file5;

    curl_setopt_array($ch5, array(
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $data['form_action'],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
    ));



		//create the multiple cURL handle
		$mh = curl_multi_init();
		
		//add the two handles
		curl_multi_add_handle($mh,$ch1);
		curl_multi_add_handle($mh,$ch2);
		curl_multi_add_handle($mh,$ch3);
		curl_multi_add_handle($mh,$ch4);
		curl_multi_add_handle($mh,$ch5);

		$active = null;
		//execute the handles

		do {
		    $mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active && $mrc == CURLM_OK) {
			progress('Uploading files', '.');
		    if (curl_multi_select($mh) != -1) {
		        do {
		            $mrc = curl_multi_exec($mh, $active);
		        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
		    }
		}
			
	}

}

