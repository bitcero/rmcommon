<?php
// $Id: xoops_version.php 1056 2012-09-12 15:43:20Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!function_exists("__")){
    function __($text, $d){
        return $text;
    }
}

$modversion['name'] = 'Common Utilities';
$modversion['version'] = 2.1;
$modversion['releasedate'] = "";
$modversion['status'] = "Beta";
$modversion['description'] = 'Container a lot of classes and functions used by Red México Modules';
$modversion['author'] = "BitC3R0";
$modversion['authormail'] = "i.bitcero@gmail.com";
$modversion['authorweb'] = "Red México";
$modversion['authorurl'] = "http://redmexico.com.mx";
$modversion['updateurl'] = "http://www.xoopsmexico.net/modules/vcontrol/?action=check&id=1";
$modversion['credits'] = "Red México, BitC3R0";
$modversion['help'] = "http://www.redmexico.com.mx/docs/common-utilities/";
$modversion['license'] = "GPL 2";
$modversion['official'] = 0;
$modversion['image'] = "images/logo.png";
$modversion['dirname'] = "rmcommon";
$modversion['icon16'] = "images/rmc16.png";
$modversion['icon24'] = 'images/rmc24.png';
$modversion['icon48'] = 'images/icon48.png';
$modversion['rmnative'] = 1;
$modversion['rmversion'] = array('major'=>2,'minor'=>2,'revision'=>5,'stage'=>-2,'name'=>'Common Utilities');
$modversion['onUninstall'] = 'include/install.php';
$modversion['onInstall'] = 'include/install.php';
$modversion['onUpdate'] = 'include/install.php';

$modversion['social'][0] = array('title' => __('Twitter', 'rmcommon'),'type' => 'twitter','url' => 'http://www.twitter.com/bitcero/');
$modversion['social'][1] = array('title' => __('LinkedIn', 'rmcommon'),'type' => 'linkedin','url' => 'http://www.linkedin.com/bitcero/');
$modversion['social'][2] = array('title' => __('Red México Twitter', 'rmcommon'),'type' => 'twitter','url' => 'http://www.twitter.com/redmexico/');
$modversion['social'][3] = array('title' => __('Red México Facebook', 'rmcommon'),'type' => 'facebook','url' => 'http://www.facebook.com/redmexico/');

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "index.php";
$modversion['adminmenu'] = "menu.php";

$modversion['hasMain'] = 1;

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

$modversion['tables'][0] = 'mod_rmcommon_images_categories';
$modversion['tables'][1] = 'mod_rmcommon_comments';
$modversion['tables'][2] = 'mod_rmcommon_comments_assignations';
$modversion['tables'][3] = 'mod_rmcommon_images';
$modversion['tables'][4] = 'mod_rmcommon_plugins';
$modversion['tables'][5] = 'mod_rmcommon_settings';
$modversion['tables'][6] = 'mod_rmcommon_blocks';
$modversion['tables'][7] = 'mod_rmcommon_blocks_positions';
$modversion['tables'][8] = 'mod_rmcommon_blocks_assignations';

// Templates
$modversion['templates'][1]['file'] = 'rmc-comments-display.html';
$modversion['templates'][1]['description'] = 'Comments list';
$modversion['templates'][2]['file'] = 'rmc-comments-form.html';
$modversion['templates'][2]['description'] = 'Shows the comments form';

// URL Rewriting
$modversion['config'][] = array(
    'name'          => 'permalinks',
    'title'         => __( 'Activar redirección de URLs', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '0',
);

/**
* Language
*/

$files = XoopsLists::getFileListAsArray(XOOPS_ROOT_PATH.'/modules/rmcommon/lang', '');
$options = array();
$options['en_US'] = 'en';
foreach($files as $file => $v){
    
    if(substr($file, -3)!='.mo') continue;
    
    $options[substr($file, 0, -3)] = substr($file, 0, -3);
    
}
$modversion['config'][] = array(
    'name'          => 'lang',
    'title'         => '_MI_RMC_LANG',
    'description'   => '',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'default'       => 'en',
    'options'       => $options
);

// Update config options
$fct = isset($_GET['fct']) ? $_GET['fct'] : '';
$mid = isset($_GET['mod']) ? $_GET['mod'] : '';

$mh = xoops_gethandler('module');
$mod = $mh->getByDirname('rmcommon');

if($fct=='preferences' && $mid==$mod->mid()){
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = "SELECT conf_id FROM ".$db->prefix("config")." WHERE conf_modid=".$mod->mid()." AND conf_name='lang'";
    
    list($id) = $db->fetchRow($db->query($sql));
    if($id>0){
        $db->queryF("DELETE FROM ".$db->prefix("configoption")." WHERE conf_id=$id");
        $sql = "INSERT INTO ".$db->prefix("configoption")." (`confop_name`,`confop_value`,`conf_id`) VALUES ";
        foreach($options as $opt){
            $sql .= "('$opt','$opt','$id'),";
        }
        $db->queryF(rtrim($sql,','));
    }
}
unset($options, $files, $file, $v);

// Available themes
$dirs = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH.'/modules/rmcommon/themes', '');
$options = array();
foreach($dirs as $dir => $v){

    if(!file_exists(XOOPS_ROOT_PATH.'/modules/rmcommon/themes/'.$dir.'/admin_gui.php')) continue;

    $options[$dir] = $dir;

}

$modversion['config'][] = array(
    'name'          => 'theme',
    'title'         => '_MI_RMC_ADMTHEME',
    'description'   => '',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'default'       => 'twop6',
    'options'       => $options
);

// Update config options
$fct = isset($_GET['fct']) ? $_GET['fct'] : '';
$mid = isset($_GET['mod']) ? $_GET['mod'] : '';

$mh = xoops_gethandler('module');
$mod = $mh->getByDirname('rmcommon');

if($fct=='preferences' && $mid==$mod->mid()){
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = "SELECT conf_id FROM ".$db->prefix("config")." WHERE conf_modid=".$mod->mid()." AND conf_name='theme'";

    list($id) = $db->fetchRow($db->query($sql));
    if($id>0){
        $db->queryF("DELETE FROM ".$db->prefix("configoption")." WHERE conf_id=$id");
        $sql = "INSERT INTO ".$db->prefix("configoption")." (`confop_name`,`confop_value`,`conf_id`) VALUES ";
        foreach($options as $opt){
            $sql .= "('$opt','$opt','$id'),";
        }
        $db->queryF(rtrim($sql,','));
    }
}
unset($options, $files, $file, $v);

$modversion['config'][] = array(
    'name'          => 'gui_disable',
    'title'         => '_MI_RMC_GUIENABLE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

// Editor
$modversion['config'][] = array(
    'name'          => 'editor_type',
    'title'         => '_MI_RMC_EDITOR',
    'description'   => '',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'default'       => 'tiny',
    'options'       => array('_MI_RMC_EDITOR_VISUAL'=>'tiny','_MI_RMC_EDITOR_HTML'=>'html','_MI_RMC_EDITOR_XOOPS'=>'xoops','_MI_RMC_EDITOR_SIMPLE'=>'simple')
);

// JQuery inclusion
$modversion['config'][] = array(
    'name'          => 'jquery',
    'title'         => '_MI_RMC_ADDJQUERY',
    'description'   => '_MI_RMC_ADDJQUERYD',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '1'
);

// Images store type
$modversion['config'][] = array(
    'name'          => 'imagestore',
    'title'         => '_MI_RMC_IMAGESTORE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

// Images Categories list limit number
$modversion['config'][] = array(
    'name'          => 'catsnumber',
    'title'         => '_MI_RMC_IMGCATSNUMBER',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 10
);

$modversion['config'][] = array(
    'name'          => 'imgsnumber',
    'title'         => '_MI_RMC_IMGSNUMBER',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 20
);

// Secure Key
if (!isset($xoopsSecurity)) $xoopsSecurity = new XoopsSecurity();
$modversion['config'][] = array(
    'name'          => 'secretkey',
    'title'         => '_MI_RMC_SECREY',
    'description'   => '_MI_RMC_SECREYD',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => $xoopsSecurity->createToken()
);

// Formato HTML5
$modversion['config'][] = array(
    'name'          => 'dohtml',
    'title'         => '_MI_RMC_DOHTML',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'dosmileys',
    'title'         => '_MI_RMC_DOSMILE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'doxcode',
    'title'         => '_MI_RMC_DOXCODE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'doimage',
    'title'         => '_MI_RMC_DOIMAGE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0
);

$modversion['config'][] = array(
    'name'          => 'dobr',
    'title'         => '_MI_RMC_DOBR',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0
);

// Comments
$modversion['config'][] = array(
    'name'          => 'enable_comments',
    'title'         => '_MI_RMC_ENABLECOMS',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'anonymous_comments',
    'title'         => '_MI_RMC_ANONCOMS',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'approve_reg_coms',
    'title'         => '_MI_RMC_APPROVEREG',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'approve_anon_coms',
    'title'         => '_MI_RMC_APPROVEANON',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0
);

$modversion['config'][] = array(
    'name'          => 'allow_edit',
    'title'         => '_MI_RMC_ALLOWEDIT',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0
);

$modversion['config'][] = array(
    'name'          => 'edit_limit',
    'title'         => '_MI_RMC_EDITLIMIT',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'mods_number',
    'title'         => '_MI_RMC_MODSNUMBER',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 6
);


$modversion['config'][] = array(
    'name'          => 'rssimage',
    'title'         => '_MI_RMC_RSSIMAGE',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => XOOPS_URL.'/modules/rmcommon/images/rssimage.png'
);

/** Mailer Configurations **/
$modversion['config'][] = array(
    'name'          => 'transport',
    'title'         => '_MI_RMC_MAILERMETH',
    'description'   => '_MI_RMC_MAILERMETHD',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'options'       => array('_MI_RMC_PHPMAIL'=>'mail','_MI_RMC_SMTP'=>'smtp', '_MI_RMC_SENDMAIL'=>'sendmail'),
    'default'       => XOOPS_URL.'/modules/rmcommon/images/rssimage.png'
);

$modversion['config'][] = array(
    'name'          => 'smtp_server',
    'title'         => '_MI_RMC_SMTPSERVER',
    'description'   => '_MI_RMC_SMTPSERVERD',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => ''
);

$modversion['config'][] = array(
    'name'          => 'smtp_crypt',
    'title'         => '_MI_RMC_ENCRYPT',
    'description'   => '_MI_RMC_ENCRYPTD',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'options'       => array('_MI_RMC_CRYPTNONE'=>'none', '_MI_RMC_CRYPTSSL'=>'ssl', '_MI_RMC_CRYPTTLS'=>'tls'),
    'default'       => 'none'
);

$modversion['config'][] = array(
    'name'          => 'smtp_port',
    'title'         => '_MI_RMC_SMTPPORT',
    'description'   => '_MI_RMC_SMTPPORTD',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => 25
);

$modversion['config'][] = array(
    'name'          => 'smtp_user',
    'title'         => '_MI_RMC_SMTPUSER',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => ''
);

$modversion['config'][] = array(
    'name'          => 'smtp_pass',
    'title'         => '_MI_RMC_SMTPPASS',
    'description'   => '',
    'formtype'      => 'password',
    'valuetype'     => 'text',
    'default'       => ''
);

$modversion['config'][] = array(
    'name'          => 'sendmail_path',
    'title'         => '_MI_RMC_SENDMAILPATH',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '/usr/sbin/sendmail -bs'
);

$modversion['config'][] = array(
    'name'          => 'rss_enable',
    'title'         => '_MI_RMC_RSSENABLE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'blocks_enable',
    'title'         => '_MI_RMC_BLOCKSENABLE',
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0
);

$modversion['config'][] = array(
    'name'          => 'updates',
    'title'         => '_MI_RMC_UPDATES',
    'description'   => '_MI_RMC_UPDATESD',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1
);

$modversion['config'][] = array(
    'name'          => 'updatesinterval',
    'title'         => '_MI_RMC_UPDATESINTERVAL',
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 1
);

// BLOCKS

$modversion['blocks'][] = array(
    'file' => "comments.php",
    'name' => __('Comments','rmcommon'),
    'description' => __('Show comments from internal comments system','rmcommon'),
    'show_func' => "rmc_bkcomments_show",
    'edit_func' => "rmc_bkcomments_edit",
    'template' => 'rmc_bk_comments.html',
    'options' => "5|1|1|1|1"
);

$modversion['blocks'][] = array(
    'file' => "custom.php",
    'name' => __('Custom Block','rmcommon'),
    'description' => __('Allows to create a block with custom content.','rmcommon'),
    'show_func' => "",
    'type' => 'custom'
);

$amod = xoops_getActiveModules();
if(in_array("rmcommon",$amod)){
    $plugins = RMFunctions::installed_plugins();
    foreach($plugins as $plugin){
        $p = RMFunctions::load_plugin($plugin);
        if(!method_exists($p, 'blocks')) continue;
        foreach($p->blocks() as $block){
            $block['plugin'] = 1;
            $modversion['blocks'][] = $block;
        }
        
    }
    
}
