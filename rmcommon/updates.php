<?php
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION','updates');
include_once '../../include/cp_header.php';

$updfile = XOOPS_CACHE_PATH.'/updates.chk';
$ftpConfig = new stdClass();
$runFiles = array();

function jsonReturn($message, $error=1, $data=array()){
    
    $ret = array(
        'message' => $message,
        'error' => $error,
        'data' => $data
    );
    echo json_encode($ret);
    die();
    
}

function show_available_updates(){
    global $rmTpl, $rmEvents, $updfile, $xoopsSecurity;
    
    $rmFunc = RMFunctions::get();
    $rmUtil = RMUtilities::get();
    $tf = new RMTimeFormatter('', '%T% %d%, %Y% at %h%:%i%');
    
    if(is_file($updfile))
        $updates = unserialize(base64_decode(file_get_contents($updfile)));
        
    $rmTpl->add_style('updates.css', 'rmcommon');
    $rmTpl->add_script('updates.js', 'rmcommon');
    $rmTpl->add_head_script('var xoToken = "'.$xoopsSecurity->createToken().'";');
    $rmTpl->add_head_script('var langUpdated = "'.__('Item updated!','rmcommon').'";');

    $rmTpl->add_help(__('Updates Help','rmcommon'), 'http://www.xoopsmexico.net/docs/common-utilities/actualizaciones-automaticas/standalone/1/');
    
    $ftpserver = parse_url(XOOPS_URL);
    $ftpserver = $ftpserver['host'];
    
    $pathinfo = parse_url(XOOPS_URL);
    $ftpdir = str_replace($pathinfo['scheme'].'://'.$pathinfo['host'], '', XOOPS_URL);
    unset($pathinfo);

	RMBreadCrumb::get()->add_crumb(__('Available Updates','rmcommon'));
	$rmTpl->assign('xoops_pagetitle', __('Available Updates','rmcommon'));
        
    xoops_cp_header();
    include $rmTpl->get_template('rmc-updates.php','module','rmcommon');
    xoops_cp_footer();
    
}

/**
* Load available updates via AJAX
*/
function ajax_load_updates(){
    global $rmTpl, $xoopsLogger, $updfile;
    
    $rmUtil = RMUtilities::get();
    
    $xoopsLogger->activated = false;
    $updates = array();
    if(is_file($updfile))
        $updates = unserialize(base64_decode(file_get_contents($updfile)));
    
    include $rmTpl->get_template('ajax/rmc-updates-list.php','module','rmcommon');
    die();
    
}

/**
* Load update details
*/
function ajax_update_details(){
    global $xoopsLogger, $rmTpl;
    
    $xoopsLogger->activated = false;
    
    $url = rmc_server_var($_GET, 'url', '');
    
    if($url=='')
        jsonReturn(__('Invalid parameters!','rmcommon'));
    
    $data = json_decode(file_get_contents($url.'&action=update-details'), true);
    
    if($data['error']==1)
        jsonReturn($data['message']);
    
    $rmUtil = RMUtilities::get();
    $files = unserialize(base64_decode($data['data']['files']));
    ob_start();
    include $rmTpl->get_template('ajax/rmc_files_list.php','module','rmcommon');
    
    $ret = array(
        'details' => $data['data']['details'],
        'files' => ob_get_clean()
    );
    
    jsonReturn(0,0,$ret);
    
    die();
}


function download_file(){
    global $xoopsLogger, $rmTpl, $runFiles;
    
    $xoopsLogger->activated = false;
    
    $url = rmc_server_var($_POST, 'url', '');
    $cred = rmc_server_var($_POST, 'credentials', '');
    $type = rmc_server_var($_POST, 'type', '');
    $dir = rmc_server_var($_POST, 'dir', '');
    $ftpdata = rmc_server_var($_POST, 'ftp', '');
    
    if($url=='')
        jsonReturn(__('Invalid parameters!','rmcommon'));
    
    // Request access
    $response = json_decode(file_get_contents($url.'&action=login'.($cred!='' ? '&l='.$cred : '')), true);
    if($response['error']==1)
        jsonReturn($response['message']);
    
    //jsonReturn($response['data']['url']);
    
    if(!is_dir(XOOPS_CACHE_PATH.'/updates/'))
        mkdir(XOOPS_CACHE_PATH.'/updates/', 511);
    
    if(!file_put_contents(XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip', file_get_contents($response['data']['url'])))
        jsonReturn(__('Unable to download update file!','rmcommon'));
        
    // Extract files
    $zip = new ZipArchive();
    $res = $zip->open(XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip');
    if($res!==TRUE)
        jsonReturn(__('ERROR: unable to open downloaded zip file!','rmcommon'));
    
    $rmUtil = RMUtilities::get();
    $source = XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir;
    if(is_dir($source))
        $rmUtil->delete_directory($source);
    
    $zip->extractTo($source);
    $zip->close();
    // Delete downloaded zip
    unlink(XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip');
    
    // Get files list
    $details = json_decode(file_get_contents($url.'&action=update-details'), true);
    if($details['error']==1)
        jsonReturn($details['message']);
    
    $files = unserialize(base64_decode($details['data']['files']));
    
    // Prepare to copy files
    
    $target = XOOPS_ROOT_PATH.'/modules/';
    if($type=='plugin')
        $target .= 'rmcommon/plugins/'.$dir;
    else
        $target .= $dir;
    
    if(!is_dir($target))
        jsonReturn(sprintf(__('Target path "%s" does not exists!','rmcommon'), $target));
    
    // TODO 1: Verificar si existen permisos de escritura
    if(is_writable($target)){

        foreach($files as $item){
            
            $fpath = $source . $item['path'] . ($item['path']!='/' ? '/' : '') . $item['name'];

            if($item['type']=='directory'){
                
                if($item['action']=='delete')
                    is_dir($target.$item['path'].'/'.$item['name']) ? $rmUtil->delete_directory(str_replace($source, $target, $fpath)) : null;
                else
                    !is_dir($target.$item['path'].'/'.$item['name']) ? mkdir(str_replace($source, $target, $fpath)) : null;
                    
                
            } else {
                
                if($item['action']=='delete')
                    is_file($target.$item['path'].'/'.$item['name']) ? unlink(str_replace($source, $target, $fpath)) : null;
                else
                    copy($fpath, str_replace($source, $target, $fpath));
                
                if($item['action']=='run')
                    $runFiles[] = str_replace(XOOPS_ROOT_PATH, XOOPS_URL, str_replace($source, $target, $fpath));
                
            }
            
        }
        
    } else {
        
        if($ftpdata=='')
            jsonReturn(__('FTP configuration not specified!','rmcommon'));
        
        parse_str($ftpdata);
        if($ftp_server=='' || $ftp_user=='' || $ftp_pass=='')
            jsonReturn(__('FTP configuration not valid!','rmcommon'));
            
        global $ftpConfig;
        $ftpConfig->server = $ftp_server;
        $ftpConfig->user = $ftp_user;
        $ftpConfig->pass = $ftp_pass;
        $ftpConfig->dir = $ftp_dir;
        $ftpConfig->port = $ftp_port>0 ? $ftp_port : 21;
        
        $ftp = new RMFtpClient($ftp_server, $ftp_port>0 ? $ftp_port : 21, $ftp_user, $ftp_pass);
        
        if(!$ftp->connect())
            jsonReturn(sprintf(__('Unable to connect FTP server %s','rmcommon'), '<strong>'.$ftp_server.'</strong>'));
        
        $ftpConfig->base =  $ftpConfig->dir .'/modules/'.($type=='plugin' ? 'rmcommon/plugins/' : '').$dir;
        $ftpConfig->source = $source;
        $ftpConfig->target = $target;
        
        foreach($files as $item){
            processFile($item, $ftp);
        }
                
    }

    // Update uploads file
    $updates = unserialize(base64_decode(file_get_contents(XOOPS_CACHE_PATH.'/updates.chk')));
    $new = array();
    foreach($updates['updates'] as $upd){
        
        if($upd['data']['type'] == $type && $upd['data']['dir']==$dir) continue;
        $new[] = $upd;
        
    }
    
    file_put_contents(XOOPS_CACHE_PATH.'/updates.chk', base64_encode(serialize(array('date'=>$updates['date'],'total'=>intval($updates['total'])-1,'updates'=>$new))));
    
    // Delete source folder
    $rmUtil->delete_directory($source);
    
    if(!empty($runFiles))
        jsonReturn(__('Executing files...','rmcommon'), 0, array('run'=>json_encode($runFiles)));
    else
        jsonReturn(sprintf(__('%s has been updated','rmcommon'), '<strong>'.$dir.'</strong>'), 0);
    
}

function processFile($file, $ftp){
    global $ftpConfig, $runFiles;
    
    switch ($file['action']){
        case 'update':
        case 'run':
            
            if($file['type']=='directory' && $file['name']!=''){
                $dirs = explode("/", $file['path'].'/'.$file['name']);
            } else {
                $dirs = explode("/", $file['path']);
                $dirs = array_slice($dirs, 0, count($dirs)-1);
            }
            
            if (count($dirs)>0) createDirs($dirs, $ftp);
            
            if($file['type']=='file')
                putContents($ftpConfig->base.$file['path'].($file['path']!='/' ? '/' : '').$file['name'], $ftpConfig->source.$file['path'].($file['path']!='/' ? '/' : '').$file['name'], $ftp);
            
            chmodFile($ftpConfig->base.$file['path'].($file['path']!='/' ? '/' : '').$file['name'], $file['mode'], $ftp);
            
            // Almacenamos el archivo si se debe ejecutar
            if ($file['action']=='run' && $file['type']=='file')
                $runFiles[] = $ftpConfig->target.$file['path'].($file['path']!='/' ? '/' : '').$file['name'];
            
            break;
            
        case 'delete':
            
            if($file['type']=='directory')
                deleteFTPDir($ftpConfig->base.$file['path'].($file['path']!='/' ? '/' : '').$file['name'], $ftp);
            else
                $ftp->delete($ftpConfig->base.$file['path'].($file['path']!='/' ? '/' : '').$file['name']);

            break;
            
    }
}

// Create FTP firectories
function createDirs($dirs, RMFtpClient $ftp){
    global $ftpConfig;
    
    $path = '';
    $ftp->chdir($ftpConfig->base);
    foreach ($dirs as $dir){
        $path .= '/'.$dir;
            
        if (!$ftp->isDir($ftpConfig->base.$path))
            $ftp->mkdir($ftpConfig->base.$path);
        
    }
}

function chmodFile($file, $mode, $ftp){
   
    return $ftp->chmod($mode, $file);
    
}

function putContents($file, $source, $ftp){
    global $updConfig;
        
    $res = $ftp->put($file, $source, FTP_BINARY);
    return $res;
    
}

function deleteFTPDir($dir, $ftp){
    global $ftpConfig;
    
    $list = $ftp->nlist($dir);
    foreach($list as $item){
        if ($item=='.' || $item=='..') continue;
        if ($ftp->isDir($dir.$item)){
            deleteFTPDir($dir.$item);
        } else {
            $ftp->delete($dir.$item);
        }
        
    }
    
    $ftp->rmdir($dir);
    
}


function download_for_later(){
    global $xoopsLogger;

    $xoopsLogger->activated = false;

    $url = rmc_server_var($_POST, 'url', '');
    $cred = rmc_server_var($_POST, 'credentials', '');
    $type = rmc_server_var($_POST, 'type', '');
    $dir = rmc_server_var($_POST, 'dir', '');

    if($url=='')
        jsonReturn(__('Invalid parameters!','rmcommon'));

    // Request access
    $response = json_decode(file_get_contents($url.'&action=login'.($cred!='' ? '&l='.$cred : '')), true);
    if($response['error']==1)
        jsonReturn($response['message']);

    if(!is_dir(XOOPS_CACHE_PATH.'/updates/'))
        mkdir(XOOPS_CACHE_PATH.'/updates/', 511);

    if(!file_put_contents(XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip', file_get_contents($response['data']['url'])))
        jsonReturn(__('Unable to download update file!','rmcommon'));

    jsonReturn(__('Downloaded!', 'rmcommon'), 0, array(
        'file' => $type.'-'.$dir.'.zip'
    ));
}


/**
 * Send downloaded file to user
 */
function get_file_now(){

    global $xoopsSecurity;
    $tfile = rmc_server_var($_GET, 'file', '');

    if($tfile=='')
        redirectMsg('updates.php', __('File not found!','rmcommon'), RMMSG_ERROR);

    $tfile = str_replace(array("/","\\"), '', $tfile);

    $file = XOOPS_CACHE_PATH.'/updates/'.$tfile;
    if(!is_file($file))
        redirectMsg("updates.php", __('File not found!','rmcommon')." $tfile = $file", RMMSG_ERROR);

    header('Content-type: application/zip');
    header('Cache-control: no-store');
    header('Expires: 0');
    header('Content-disposition: attachment; filename='.urlencode($tfile));
    header('Content-Transfer-Encoding: binary');
    header('Content-Lenght: '.filesize($file));
    header('Last-Modified: '.gmdate("D, d M Y H:i:s",$file).'GMT');
    ob_clean();
    flush();
    readfile($file);
    unlink($file);
    exit();

}


$action = RMHttpRequest::request( 'action', 'string','' );

switch($action){
    case 'ajax-updates':
        ajax_load_updates();
        break;
    case 'update-details':
        ajax_update_details();
        break;
    case 'first-step':
        download_file();
        break;
    case 'later':
        download_for_later();
        break;
    case 'getfile':
        get_file_now();
        break;
    default:
        show_available_updates();
        break;
    
}

