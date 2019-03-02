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

/*XoopsLogger::getInstance()->activated = false;
XoopsLogger::getInstance()->renderingEnabled = false;*/

function error($message)
{
    $data['error'] = 1;
    $data['message'] = $message;
    echo json_encode($data);
    die();
}

$common->ajax()->prepare();

if (!$common->security()->check(false, false, 'CUTOKEN')) {
    $common->ajax()->notifyError(
        __('Session token invalid!', 'rmcommon'),
        0
    );
}

/**
* Handle uploaded image files only.
*/
$category = $common->httpRequest()->post('category', 'integer', 0);

// Check user
if (!$xoopsUser) {
    $common->ajax()->notifyError(__('You are not allowed to do this action', 'rmcommon'));
}

// Check if category was specified
if ($category<=0) {
    $common->ajax()->notifyError(__('Sorry, category has not been specified!', 'rmcommon'));
}

$cat = new RMImageCategory($category);
if ($cat->isNew()) {
    $common->ajax()->notifyError(__('Sorry, the specified category could not been found!', 'rmcommon'));
}

if ($cat->getVar('status')!='open') {
    $common->ajax()->notifyError(__('Sorry, the specified category is closed!', 'rmcommon'));
}

// Check permissions to upload
if (!$cat->user_allowed_toupload($xoopsUser)) {
    $common->ajax()->notifyError(__('Sorry, you can not upload images!', 'rmcommon'));
}

// Cargamos la imágen
$updir = XOOPS_UPLOAD_PATH.'/'.date('Y', time());
if (!file_exists($updir)) {
    mkdir($updir);
    chmod($updir, octdec('0777'));
}
$updir .= '/'.date('m', time());
if (!file_exists($updir)) {
    mkdir($updir);
    chmod($updir, octdec('0777'));
}

if (!file_exists($updir.'/sizes')) {
    mkdir($updir.'/sizes');
    chmod($updir.'/sizes', octdec('0777'));
}

include RMCPATH.'/class/uploader.php';

$uploader = new RMFileUploader($updir, $cat->max_file_size(), array('gif', 'jpg', 'jpeg', 'png'));

$err = array();
if (!$uploader->fetchMedia('file')) {
    $common->ajax()->notifyError($uploader->getErrors());
}

if (!$uploader->upload()) {
    $common->ajax()->notifyError($uploader->getErrors());
}

// Insertamos el archivo en la base de datos
$image = new RMImage();
$image->setVar('title', $uploader->savedFileName);
$image->setVar('date', time());
$image->setVar('file', $uploader->savedFileName);
$image->setVar('cat', $cat->id());
$image->setVar('uid', $xoopsUser->uid());

if (!$image->save()) {
    unlink($uploader->savedDestination);
    $common->ajax()->notifyError(__('File could not be inserted to database!', 'rmcommon'));
}

$common->ajax()->response(
    sprintf(__('File <strong>%s</strong> has been uploaded successfully', 'rmcommon'), $uploader->savedFileName),
    0,
    1,
    [
        'id' => $image->id()
    ]
);

die();
