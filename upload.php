<?php

include "setup.php";
include "k2sUpload.class.php";
//include "Folder.class.php";
//include "Database.class.php";
//include "functions.php";

$upl = new k2sUpload(UPLOAD_FOLDER_K2S);

//============================================================================//
//									Upload files to server
//============================================================================//
$upl->createFolder();
$upl->upload('/blop/upl/vids/', 5);


//$upl->uploadFolder['id'] = '8976a0e53cc7d';

//============================================================================//
//									Get upladed files info
//============================================================================//

$files_multydim_array = $upl->getFilesList($upl->uploadFolder['id'], 1000, 0, ['name' => 1], 'any')['files'];

//============================================================================//
//									Create URLs links to uploaded files
//============================================================================//
$files = array();
foreach($files_multydim_array as $file){
	$files[$file['name']] = 'http://k2s.cc/file/' . $file['id'] . '/' . $file['name'];
}


//============================================================================//
//									Write uploaded files info to DB.video_urls
//============================================================================//
foreach($files as $filename => $url){

	$torr_id = $db->getTorrIDfromFilename($filename);
	$video_id = $db->getVideoID($filename);
	$db->appendTo('video_urls', array('torrent_id' => $torr_id, 
																		'video_id' 	 => $video_id, 
																		'video_url'  => $url));

}

