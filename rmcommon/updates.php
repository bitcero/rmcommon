<?php
/**
 * Common Utilities
 * A framework for new XOOPS modules
 *
 * Copyright © 2015 Eduardo Cortés
 * -----------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * -----------------------------------------------------------------
 * @package      rmcommon
 * @author       Eduardo Cortés
 * @copyright    Eduardo Cortés
 * @license      GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link         http://eduardocortes.mx
 * @link         http://rmcommon.com
 */

define('RMCLOCATION','updates');
include_once '../../include/cp_header.php';

$updfile = XOOPS_CACHE_PATH.'/updates.chk';
$ftpConfig = new stdClass();
$runFiles = array();

function jsonReturn($message, $error=1, $data=array(), $token = 1){
    global $xoopsSecurity;

    $ret = array(
        'message' => $message,
        'error' => $error,
        'data' => $data,
        'token' => $token ? $xoopsSecurity->createToken() : ''
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

    //$rmTpl->add_style('updates.css', 'rmcommon');
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
    global $rmTpl, $xoopsLogger, $updfile, $cuIcons;

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
    global $xoopsLogger, $rmTpl, $runFiles, $xoopsSecurity;

    $xoopsLogger->activated = false;

    $url        = RMHttpRequest::post( 'url', 'string', '' );
    $cred       = RMHttpRequest::post( 'credentials', 'string', '' );
    $type       = RMHttpRequest::post( 'type', 'string', '' );
    $dir        = RMHttpRequest::post( 'dir', 'string', '' );
    $ftpdata    = RMHttpRequest::post( 'ftp', 'string', '' );

    if($url=='')
        jsonReturn(__('Invalid parameters!','rmcommon'));

    // Request access
    $query = explode( "?", $url );
    $query[1] = ($query[1] != '' ? $query[1] . '&' : '') . 'action=identity'.($cred!='' ? '&l='.$cred : '');

    $response = json_decode( RMHttpRequest::load_url( $query[0], $query[1], true ), true );

    //$response = json_decode(file_get_contents($url.'&), true);
    if($response['error']==1)
        jsonReturn($response['message']);

    //jsonReturn($response['data']['url']);

    if(!is_dir(XOOPS_CACHE_PATH.'/updates/'))
        mkdir(XOOPS_CACHE_PATH.'/updates/', 511);

    if(!file_put_contents( XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip', file_get_contents( $response['data']['url'] ) ) )
        jsonReturn(__('Unable to download update file!','rmcommon'));

    // Get files list
    $details = json_decode( RMHttpRequest::load_url($url.'&action=update-details', '', true), true );
    if($details['error']==1)
        jsonReturn($details['message']);

    $hash = $details['data']['hash'];
    $file_hash = md5_file( XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip' );

    if ( $hash != $file_hash ){
        @unlink( XOOPS_CACHE_PATH.'/updates/'.$type.'-'.$dir.'.zip' );
        jsonReturn( __('The package file could be corrupted. Aborting!', 'rmcommon'));
    }

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

    // Prepare to copy files

    $target = XOOPS_ROOT_PATH.'/modules/';
    if($type=='plugin')
        $target .= 'rmcommon/plugins/'.$dir;
    else
        $target .= $dir;

    if(!is_dir($target))
        jsonReturn(sprintf(__('Target path "%s" does not exists!','rmcommon'), $target));

    /**
     * When rmcommon is the module to be updated then we need
     * to make a backup before to delete files
     */
    $excluded = array();
    if($dir == 'rmcommon'){
        $excluded = array($target . '/plugins');
    }

    if (is_writable($target) && !empty( $target ) ) {

        $target = str_replace( '\\', '/', $target );

        // Deletes dir content to replace with new files
        RMUtilities::delete_directory( $target, false, $excluded );

        // Copy new files
        $source = rtrim( str_replace('\\', '/', $source), '/' );

        $odir = opendir($source);
        while (($file = readdir($odir)) !== false){
            if ($file == '.' || $file=='..') continue;
            @rename( $source . '/' . $file, $target . '/' . $file );
        }
        closedir($odir);

        RMUtilities::delete_directory( $source );

    } else {

        if($ftpdata=='')
            jsonReturn(__('FTP configuration not specified!','rmcommon'));

        parse_str($ftpdata);
        if($ftp_server=='' || $ftp_user=='' || $ftp_pass=='')
            jsonReturn(__('FTP configuration not valid!','rmcommon'));

        $target = str_replace( '\\', '/', $target );

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

        // Clean current element directory
        deleteFTPDir( $ftpConfig->base, $ftp, false );

        // Copy new files

    }

    // Update uploads file
    $updates = unserialize(base64_decode(file_get_contents(XOOPS_CACHE_PATH.'/updates.chk')));
    $new = array();
    foreach ($updates['updates'] as $upd) {

        if($upd['data']['type'] == $type && $upd['data']['dir']==$dir) continue;
        $new[] = $upd;

    }

    file_put_contents(XOOPS_CACHE_PATH.'/updates.chk', base64_encode(serialize(array('date'=>$updates['date'],'total'=>intval($updates['total'])-1,'updates'=>$new))));

    if(!empty($runFiles))
        jsonReturn(__('Executing files...','rmcommon'), 0, array('run'=>json_encode($runFiles)));
    else
        jsonReturn(sprintf(__('%s has been updated','rmcommon'), '<strong>'.$dir.'</strong>'), 0);

}

function processFile($file, $ftp){
    global $ftpConfig, $runFiles;

    switch ($file['action']) {
        case 'update':
        case 'run':

            if ($file['type']=='directory' && $file['name']!='') {
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
    foreach ($dirs as $dir) {
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

function deleteFTPDir( $dir, $ftp, $root = true ){
    global $ftpConfig;

    $list = $ftp->nlist($dir);
    foreach ($list as $item) {
        if ($item=='.' || $item=='..') continue;
        if ($ftp->isDir($dir.$item)) {
            deleteFTPDir($ftp, $dir.$item);
        } else {
            $ftp->delete($dir.$item);
        }

    }

    if ( $root )
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
    $response = json_decode(file_get_contents($url.'&action=identity'.($cred!='' ? '&l='.$cred : '')), true);
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

function update_locally(){
    global $xoopsSecurity, $xoopsLogger, $xoopsConfig;

    $xoopsLogger->activated = false;

    if ( !$xoopsSecurity->check() )
        jsonReturn( __('Wrong action!', 'rmcommon'), 1, array(), 0 );

    $dir = RMHttpRequest::post( 'module', 'string', '' );
    $type = RMHttpRequest::post( 'type', 'string', '' );

    if ( '' == $dir || '' == $type )
        jsonReturn( __('Data not valid!', 'rmcommon') );

    if ( 'module' == $type ){

        if ( !is_dir( XOOPS_ROOT_PATH . '/modules/' . $dir ) )
            jsonReturn( __('Module does not exists!', 'rmcommon') );

        xoops_loadLanguage( 'admin', 'system' );

        $file = XOOPS_ROOT_PATH.'/modules/system/language/'.$xoopsConfig['language'].'/admin/modulesadmin.php';
        if (file_exists($file)){
            include_once $file;
        } else {
            include_once str_replace($xoopsConfig['language'], 'english', $file);
        }

        include_once XOOPS_ROOT_PATH.'/modules/system/admin/modulesadmin/modulesadmin.php';
        $log = module_update( $dir );

        jsonReturn( __('Module updated locally', 'rmcommon'), 0, array('log' => $log) );

    } elseif ( 'plugin' == $type ){

        if ( !is_dir( XOOPS_ROOT_PATH . '/modules/rmcommon/plugins/' . $dir ) )
            jsonReturn( __('Plugin does not exists!', 'rmcommon') );

        $plugin = new RMPlugin( $dir );
        if ( $plugin->isNew() ){
            jsonReturn( __('Plugin does not exists!', 'rmcommon') );
        }

        if ( !$plugin->on_update() )
            jsonReturn( sprintf( __('Plugins manager could not update the plugin: %s', 'rmcommon' ), $plugin->errors() ) );

        jsonReturn( __('Plugin updated locally', 'rmcommon'), 0 );

    }

}

function module_update($dirname){
    global $xoopsConfig, $xoopsDB;

    $dirname = trim($dirname);
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname($dirname);
    // Save current version for use in the update function
    $prev_version = $module->getVar('version');
    include_once XOOPS_ROOT_PATH.'/class/template.php';
    $xoopsTpl = new XoopsTpl();
    $xoopsTpl->clearCache($dirname);
    //xoops_template_clear_module_cache($module->getVar('mid'));
    // we dont want to change the module name set by admin
    $temp_name = $module->getVar('name');
    $module->loadInfoAsVar($dirname);
    $module->setVar('name', $temp_name);

    $log = '';
    if (!$module_handler->insert($module)) {
        $log .=  sprintf(__('Could not update %s','rmcommon'), $module->getVar('name'));
    } else {
        $newmid = $module->getVar('mid');
        $msgs = array();
        $msgs[] = sprintf(__('Updating module %s','rmcommon'), $module->getVar('name'));
        $tplfile_handler =& xoops_gethandler('tplfile');
        $deltpl = $tplfile_handler->find('default', 'module', $module->getVar('mid'));
        $delng = array();
        if (is_array($deltpl)) {
            // delete template file entry in db
            $dcount = count($deltpl);
            for ($i = 0; $i < $dcount; $i++) {
                if (!$tplfile_handler->delete($deltpl[$i])) {
                    $delng[] = $deltpl[$i]->getVar('tpl_file');
                }
            }
        }
        $templates = $module->getInfo('templates');
        if ($templates != false) {
            $msgs[] = __('Updating templates...','rmcommon');
            foreach ($templates as $tpl) {
                $tpl['file'] = trim($tpl['file']);
                if (!in_array($tpl['file'], $delng)) {
                    $tpldata =& xoops_module_gettemplate($dirname, $tpl['file']);
                    $tplfile =& $tplfile_handler->create();
                    $tplfile->setVar('tpl_refid', $newmid);
                    $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_lastmodified', time());
                    if (preg_match("/\.css$/i", $tpl['file'])) {
                        $tplfile->setVar('tpl_type', 'css');
                    } else {
                        $tplfile->setVar('tpl_type', 'module');
                    }
                    $tplfile->setVar('tpl_source', $tpldata, true);
                    $tplfile->setVar('tpl_module', $dirname);
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $tpl['file'], true);
                    $tplfile->setVar('tpl_desc', $tpl['description'], true);
                    if (!$tplfile_handler->insert($tplfile)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('Template %s could not be inserted!','rmcommon'), "<strong>".$tpl['file']."</strong>").'</span>';
                    } else {
                        $newid = $tplfile->getVar('tpl_id');
                        $msgs[] = '&nbsp;&nbsp;'.sprintf(__('Template %s inserted to the database.','rmcommon'), "<strong>".$tpl['file']."</strong>");
                        if ($xoopsConfig['template_set'] == 'default') {
                            if (!xoops_template_touch($newid)) {
                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Could not recompile template %s','rmcommon'), "<strong>".$tpl['file']."</strong>").'</span>';
                            } else {
                                $msgs[] = '&nbsp;&nbsp;<span>'.sprintf(__('Template %s recompiled','rmcommon'), "<strong>".$tpl['file']."</strong>").'</span>';
                            }
                        }
                    }
                    unset($tpldata);
                } else {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Could not delete old template %s. Aborting update of this file.','rmcommon'), "<strong>".$tpl['file']."</strong>").'</span>';
                }
            }
        }
        $blocks = $module->getInfo('blocks');
        $msgs[] = __('Rebuilding blocks...','rmcommon');
        if ($blocks != false) {
            $showfuncs = array();
            $funcfiles = array();
            foreach ($blocks as $i => $block) {
                if (isset($block['show_func']) && $block['show_func'] != '' && isset($block['file']) && $block['file'] != '') {
                    $editfunc = isset($block['edit_func']) ? $block['edit_func'] : '';
                    $showfuncs[] = $block['show_func'];
                    $funcfiles[] = $block['file'];
                    $template = '';
                    if ((isset($block['template']) && trim($block['template']) != '')) {
                        $content = xoops_module_gettemplate($dirname, $block['template'], 'blocks');
                    }

                    if (!$content) {
                        $content = '';
                    } else {
                        $template = $block['template'];
                    }
                    $options = '';
                    if (!empty($block['options'])) {
                        $options = $block['options'];
                    }
                    $sql = "SELECT bid, name FROM ".$xoopsDB->prefix('newblocks')." WHERE mid=".$module->getVar('mid')." AND func_num=".$i." AND show_func='".addslashes($block['show_func'])."' AND func_file='".addslashes($block['file'])."'";
                    $fresult = $xoopsDB->query($sql);
                    $fcount = 0;
                    while ($fblock = $xoopsDB->fetchArray($fresult)) {
                        $fcount++;
                        $sql = "UPDATE ".$xoopsDB->prefix("newblocks")." SET name='".addslashes($block['name'])."', edit_func='".addslashes($editfunc)."', content='', template='".$template."', last_modified=".time()." WHERE bid=".$fblock['bid'];
                        $result = $xoopsDB->query($sql);
                        if (!$result) {
                            $msgs[] = "&nbsp;&nbsp;".sprintf(__('ERROR: Could not update %s'), $fblock['name']);
                        } else {
                            $msgs[] = "&nbsp;&nbsp;".sprintf(__('Block %s updated.','rmcommon'), $fblock['name']).sprintf(__('Block ID: %s','rmcommon'), "<strong>".$fblock['bid']."</strong>");
                            if ($template != '') {
                                $tplfile = $tplfile_handler->find('default', 'block', $fblock['bid']);
                                if (count($tplfile) == 0) {
                                    $tplfile_new =& $tplfile_handler->create();
                                    $tplfile_new->setVar('tpl_module', $dirname);
                                    $tplfile_new->setVar('tpl_refid', $fblock['bid']);
                                    $tplfile_new->setVar('tpl_tplset', 'default');
                                    $tplfile_new->setVar('tpl_file', $block['template'], true);
                                    $tplfile_new->setVar('tpl_type', 'block');
                                } else {
                                    $tplfile_new = $tplfile[0];
                                }
                                $tplfile_new->setVar('tpl_source', $content, true);
                                $tplfile_new->setVar('tpl_desc', $block['description'], true);
                                $tplfile_new->setVar('tpl_lastmodified', time());
                                $tplfile_new->setVar('tpl_lastimported', 0);
                                if (!$tplfile_handler->insert($tplfile_new)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Could not update %s template.','rmcommon'), "<strong>".$block['template']."</strong>").'</span>';
                                } else {
                                    $msgs[] = "&nbsp;&nbsp;".sprintf(__('Template %s updated.','rmcommon'), "<strong>".$block['template']."</strong>");
                                    if ($xoopsConfig['template_set'] == 'default') {
                                        if (!xoops_template_touch($tplfile_new->getVar('tpl_id'))) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Could not recompile template %s','rmcommon'), "<strong>".$block['template']."</strong>").'</span>';
                                        } else {
                                            $msgs[] = "&nbsp;&nbsp;".sprintf(__('Template %s recompiled','rmcommon'), "<strong>".$block['template']."</strong>");
                                        }
                                    }

                                }
                            }
                        }
                    }
                    if ($fcount == 0) {
                        $newbid = $xoopsDB->genId($xoopsDB->prefix('newblocks').'_bid_seq');
                        $block_name = addslashes($block['name']);
                        $block_type = ($module->getVar('dirname') == 'system') ? 'S' : 'M';
                        $sql = "INSERT INTO ".$xoopsDB->prefix("newblocks")." (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, isactive, dirname, func_file, show_func, edit_func, template, last_modified) VALUES (".$newbid.", ".$module->getVar('mid').", ".$i.",'".addslashes($options)."','".$block_name."', '".$block_name."', '', 0, 0, 0, '{$block_type}', 1, '".addslashes($dirname)."', '".addslashes($block['file'])."', '".addslashes($block['show_func'])."', '".addslashes($editfunc)."', '".$template."', ".time().")";
                        $result = $xoopsDB->query($sql);
                        if (!$result) {
                            $msgs[] = '&nbsp;&nbsp;'.sprintf(_('ERROR: Could not create %s','rmcommon'), $block['name']);$log .=  $sql;
                        } else {
                            if (empty($newbid)) {
                                $newbid = $xoopsDB->getInsertId();
                            }
                            if ($module->getInfo('hasMain')) {
                                $groups = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
                            } else {
                                $groups = array(XOOPS_GROUP_ADMIN);
                            }
                            $gperm_handler =& xoops_gethandler('groupperm');
                            foreach ($groups as $mygroup) {
                                $bperm =& $gperm_handler->create();
                                $bperm->setVar('gperm_groupid', $mygroup);
                                $bperm->setVar('gperm_itemid', $newbid);
                                $bperm->setVar('gperm_name', 'block_read');
                                $bperm->setVar('gperm_modid', 1);
                                if (!$gperm_handler->insert($bperm)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.__('ERROR: Could not add block access right','rmcommon') .' '.sprintf(__("Block ID: %s",'rmcommon'), "<strong>".$newbid."</strong>"). ' '.sprintf(__('Group ID: %s','rmcommon'), "<strong>".$mygroup."</strong>").'</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;'.__('Added block access right','rmcommon'). ' ' . sprintf(__("Block ID: %s",'rmcommon'), "<strong>".$newbid."</strong>") . ' ' . sprintf(__('Group ID: %s','rmcommon'), "<strong>".$mygroup."</strong>");
                                }
                            }

                            if ($template != '') {
                                $tplfile =& $tplfile_handler->create();
                                $tplfile->setVar('tpl_module', $dirname);
                                $tplfile->setVar('tpl_refid', $newbid);
                                $tplfile->setVar('tpl_source', $content, true);
                                $tplfile->setVar('tpl_tplset', 'default');
                                $tplfile->setVar('tpl_file', $block['template'], true);
                                $tplfile->setVar('tpl_type', 'block');
                                $tplfile->setVar('tpl_lastimported', 0);
                                $tplfile->setVar('tpl_lastmodified', time());
                                $tplfile->setVar('tpl_desc', $block['description'], true);
                                if (!$tplfile_handler->insert($tplfile)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Could not insert template %s to the database.','rmcommon'), "<strong>".$block['template']."</strong>").'</span>';
                                } else {
                                    $newid = $tplfile->getVar('tpl_id');
                                    $msgs[] = '&nbsp;&nbsp;'.sprintf(__('Template %s added to the database','rmcommon'), "<strong>".$block['template']."</strong>");
                                    if ($xoopsConfig['template_set'] == 'default') {
                                        if (!xoops_template_touch($newid)) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Template %s recompile failed','rmcommon'), "<strong>".$block['template']."</strong>").'</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;'.sprintf(__('Template %s recompiled','rmcommon'), "<strong>".$block['template']."</strong>");
                                        }
                                    }
                                }
                            }
                            $msgs[] = '&nbsp;&nbsp;'.sprintf(__('Block %s created','rmcommon'), "<strong>".$block['name']."</strong>").sprintf(__("Block ID: %s",'rmcommon'), "<strong>".$newbid."</strong>");
                            $sql = 'INSERT INTO '.$xoopsDB->prefix('block_module_link').' (block_id, module_id) VALUES ('.$newbid.', -1)';
                            $xoopsDB->query($sql);
                        }
                    }
                }
            }
            $block_arr = XoopsBlock::getByModule($module->getVar('mid'));
            foreach ($block_arr as $block) {
                if (!in_array($block->getVar('show_func'), $showfuncs) || !in_array($block->getVar('func_file'), $funcfiles)) {
                    $sql = sprintf("DELETE FROM %s WHERE bid = %u", $xoopsDB->prefix('newblocks'), $block->getVar('bid'));
                    if(!$xoopsDB->query($sql)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.sprintf(__('ERROR: Could not delete block %s','rmcommon'), "<strong>".$block->getVar('name')."</strong>").sprintf(__("Block ID: %s",'rmcommon'), "<strong>".$block->getVar('bid')."</strong>").'</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;Block <strong>'.$block->getVar('name').' deleted. Block ID: <strong>'.$block->getVar('bid').'</strong>';
                        if ($block->getVar('template') != '') {
                            $tplfiles = $tplfile_handler->find(null, 'block', $block->getVar('bid'));
                            if (is_array($tplfiles)) {
                                $btcount = count($tplfiles);
                                for ($k = 0; $k < $btcount; $k++) {
                                    if (!$tplfile_handler->delete($tplfiles[$k])) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.__('ERROR: Could not remove deprecated block template.','rmcommon'). '(ID: <strong>'.$tplfiles[$k]->getVar('tpl_id').'</strong>)</span>';
                                    } else {
                                        $msgs[] = '&nbsp;&nbsp;'.sprintf(__('Block template %s deprecated','rmcommon'), "<strong>".$tplfiles[$k]->getVar('tpl_file')."</strong>");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // reset compile_id
        $xoopsTpl->setCompileId();

        // first delete all config entries
        $config_handler =& xoops_gethandler('config');
        $configs = $config_handler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
        $confcount = count($configs);
        $config_delng = array();
        if ($confcount > 0) {
            $msgs[] = __('Deleting module config options...','rmcommon');
            for ($i = 0; $i < $confcount; $i++) {
                if (!$config_handler->deleteConfig($configs[$i])) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">'.__('ERROR: Could not delete config data from the database','rmcommon'). sprintf(__('Config ID: %s','rmcommon'), "<strong>".$configs[$i]->getvar('conf_id')."</strong>").'</span>';
                    // save the name of config failed to delete for later use
                    $config_delng[] = $configs[$i]->getvar('conf_name');
                } else {
                    $config_old[$configs[$i]->getvar('conf_name')]['value'] = $configs[$i]->getvar('conf_value', 'N');
                    $config_old[$configs[$i]->getvar('conf_name')]['formtype'] = $configs[$i]->getvar('conf_formtype');
                    $config_old[$configs[$i]->getvar('conf_name')]['valuetype'] = $configs[$i]->getvar('conf_valuetype');
                    $msgs[] = "&nbsp;&nbsp;".__('Config data deleted from the database.','rmcommon'). ' ' . sprintf(__('Config ID: %s','rmcommon'), "<strong>".$configs[$i]->getVar('conf_id')."</strong>");
                }
            }
        }

        // now reinsert them with the new settings
        $configs = $module->getInfo('config');
        // Include
        if ($configs != false) {
            if ($module->getVar('hascomments') != 0) {
                include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
                array_push($configs, array('name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)));
                array_push($configs, array('name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0));
            }
        } else {
            if ($module->getVar('hascomments') != 0) {
                $configs = array();
                include_once(XOOPS_ROOT_PATH.'/include/comment_constants.php');
                $configs[] = array('name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN));
                $configs[] = array('name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0);
            }
        }
        // RMV-NOTIFY
        if ($module->getVar('hasnotification') != 0) {
            if (empty($configs)) {
                $configs = array();
            }
            // Main notification options
            include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
            include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
            $options = array();
            $options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
            $options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
            $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
            $options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;

            //$configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLED', 'description' => '_NOT_CONFIG_ENABLEDDSC', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
            $configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLE', 'description' => '_NOT_CONFIG_ENABLEDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => XOOPS_NOTIFICATION_ENABLEBOTH, 'options'=>$options);
            // Event specific notification options
            // FIXME: for some reason the default doesn't come up properly
            //  initially is ok, but not when 'update' module..
            $options = array();
            $categories =& notificationCategoryInfo('',$module->getVar('mid'));
            foreach ($categories as $category) {
                $events =& notificationEvents ($category['name'], false, $module->getVar('mid'));
                foreach ($events as $event) {
                    if (!empty($event['invisible'])) {
                        continue;
                    }
                    $option_name = $category['title'] . ' : ' . $event['title'];
                    $option_value = $category['name'] . '-' . $event['name'];
                    $options[$option_name] = $option_value;
                    //$configs[] = array ('name' => notificationGenerateConfig($category,$event,'name'), 'title' => notificationGenerateConfig($category,$event,'title_constant'), 'description' => notificationGenerateConfig($category,$event,'description_constant'), 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
                }
            }
            $configs[] = array ('name' => 'notification_events', 'title' => '_NOT_CONFIG_EVENTS', 'description' => '_NOT_CONFIG_EVENTSDSC', 'formtype' => 'select_multi', 'valuetype' => 'array', 'default' => array_values($options), 'options' => $options);
        }

        if ($configs != false) {
            $msgs[] = 'Adding module config data...';
            $config_handler =& xoops_gethandler('config');
            $order = 0;
            foreach ($configs as $config) {
                // only insert ones that have been deleted previously with success
                if (!in_array($config['name'], $config_delng)) {
                    $confobj =& $config_handler->createConfig();
                    $confobj->setVar('conf_modid', $newmid);
                    $confobj->setVar('conf_catid', 0);
                    $confobj->setVar('conf_name', $config['name']);
                    $confobj->setVar('conf_title', $config['title'], true);
                    $confobj->setVar('conf_desc', $config['description'], true);
                    $confobj->setVar('conf_formtype', $config['formtype']);
                    $confobj->setVar('conf_valuetype', $config['valuetype']);
                    if (isset($config_old[$config['name']]['value']) && $config_old[$config['name']]['formtype'] == $config['formtype'] && $config_old[$config['name']]['valuetype'] == $config['valuetype']) {
                        // preserver the old value if any
                        // form type and value type must be the same
                        $confobj->setVar('conf_value', $config_old[$config['name']]['value'], true);
                    } else {
                        $confobj->setConfValueForInput($config['default'], true);

                        //$confobj->setVar('conf_value', $config['default'], true);
                    }
                    $confobj->setVar('conf_order', $order);
                    $confop_msgs = '';
                    if (isset($config['options']) && is_array($config['options'])) {
                        foreach ($config['options'] as $key => $value) {
                            $confop =& $config_handler->createConfigOption();
                            $confop->setVar('confop_name', $key, true);
                            $confop->setVar('confop_value', $value, true);
                            $confobj->setConfOptions($confop);
                            $confop_msgs .= '<br />&nbsp;&nbsp;&nbsp;&nbsp; ' . __('Config option added','rmcommon') . ' ' . __('Name:','rmcommon') . ' <strong>' . ( defined($key) ? constant($key) : $key ) . '</strong> ' . __('Value:','rmcommon') . ' <strong>' . $value . '</strong> ';
                            unset($confop);
                        }
                    }
                    $order++;
                    if (false != $config_handler->insertConfig($confobj)) {
                        //$msgs[] = '&nbsp;&nbsp;Config <strong>'.$config['name'].'</strong> added to the database.'.$confop_msgs;
                        $msgs[] = "&nbsp;&nbsp;".sprintf(__('Config %s added to the database','rmcommon'), "<strong>" . $config['name'] . "</strong>") . $confop_msgs;
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not insert config %s to the database.','rmcommon'), "<strong>" . $config['name'] . "</strong>") . '</span>';
                    }
                    unset($confobj);
                }
            }
            unset($configs);
        }

        // execute module specific update script if any
        $update_script = $module->getInfo('onUpdate');
        if (false != $update_script && trim($update_script) != '') {
            include_once XOOPS_ROOT_PATH.'/modules/'.$dirname.'/'.trim($update_script);
            if (function_exists('xoops_module_update_'.$dirname)) {
                $func = 'xoops_module_update_'.$dirname;
                if (!$func($module, $prev_version)) {
                    $msgs[] = "<p>".sprintf(__('Failed to execute %s','rmcommon'), $func)."</p>";
                } else {
                    $msgs[] = "<p>".sprintf(__('%s executed successfully.','rmcommon'), "<strong>".$func."</strong>")."</p>";
                }
            }
        }

        foreach ($msgs as $msg) {
            $log .=  $msg.'<br />';
        }
        $log .=  "<p>".sprintf(__('Module %s updated successfully!','rmcommon'), "<strong>".$module->getVar('name')."</strong>")."</p>";
    }

    // Flush cache files for cpanel GUIs
    xoops_load("cpanel", "system");
    XoopsSystemCpanel::flush();

    return $log;

}

$action = RMHttpRequest::request( 'action', 'string','' );

switch ($action) {
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
    case 'local-update':
        update_locally();
        break;
    default:
        show_available_updates();
        break;

}
