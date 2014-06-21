<?php
// $Id: upload.php 967 2012-05-31 04:19:25Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../../mainfile.php';
XoopsLogger::getInstance()->activated = false;
XoopsLogger::getInstance()->renderingEnabled = false;

function error($message){
    $data['error'] = 1;
    $data['message'] = $message;
	echo json_encode($data);
	die();
}

/**
* Handle uploaded image files only.
*/
$security = TextCleaner::getInstance()->decrypt(rmc_server_var($_POST, 'rmsecurity', 0), true);
$category = rmc_server_var($_POST, 'category', 0);

$data = $security; //base64_decode($security);
$data = explode("|", $data); // [0] = referer, [1] = session_id(), [2] = user, [3] = token

$xoopsUser = new XoopsUser($data[0]);

if (!isset($data[1]) || strpos($data[1], RMCURL)===FALSE){
	error(__('You are not allowed to do this action','rmcommon'));
}

if (!$xoopsUser){
    error(__('You are not allowed to do this action','rmcommon'));
}

if ($category<=0){
	error(__('Sorry, category has not been specified!','rmcommon'));
}

$cat = new RMImageCategory($category);
if ($cat->isNew()){
    error(__('Sorry, the specified category has not been found!','rmcommon'));
}

if ($cat->getVar('status')!='open'){
	error(__('Sorry, the specified category is closed!','rmcommon'));
}

if (!$cat->user_allowed_toupload($xoopsUser)){
    error(__('Sorry, you can not upload images!','rmcommon'));
}

// Cargamos la imágen
$updir = XOOPS_UPLOAD_PATH.'/'.date('Y', time());
if (!file_exists($updir)){
    mkdir($updir);
    chmod($updir, octdec('0777'));
}
$updir .= '/'.date('m',time());
if (!file_exists($updir)){
    mkdir($updir);
    chmod($updir, octdec('0777'));
}

if (!file_exists($updir.'/sizes')){
    mkdir($updir.'/sizes');
    chmod($updir.'/sizes', octdec('0777'));
}

include RMCPATH.'/class/uploader.php';

$uploader = new RMFileUploader($updir, $cat->max_file_size(), array('gif', 'jpg', 'jpeg', 'png'));

$err = array();
if (!$uploader->fetchMedia('Filedata')){
    error($uploader->getErrors());
}

if (!$uploader->upload()){
    error($uploader->getErrors());
}

// Insertamos el archivo en la base de datos
$image = new RMImage();
$image->setVar('title', $uploader->savedFileName);
$image->setVar('date', time());
$image->setVar('file', $uploader->savedFileName);
$image->setVar('cat', $cat->id());
$image->setVar('uid', $xoopsUser->uid());

if (!$image->save()){
    unlink($uploader->savedDestination);
    error(__('File could not be inserted to database!','rmcommon'));
}

$ret['message'] = '1';
$ret['id'] = $image->id();
echo json_encode($ret);

die();