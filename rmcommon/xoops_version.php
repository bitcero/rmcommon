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
    function __($text, $d = ''){
        return $text;
    }
}

$modversion['name'] = 'Common Utilities';
$modversion['version'] = 2.2;
$modversion['releasedate'] = "";
$modversion['status'] = "Beta";
$modversion['description'] = 'Contains a lot of classes and functions used by Red México Modules';
$modversion['credits'] = "Red México, BitC3R0";
$modversion['help'] = "docs/readme.html";
$modversion['license'] = "GPL 2";
$modversion['official'] = 0;
$modversion['image'] = "images/logo.png";
$modversion['dirname'] = "rmcommon";
$modversion['onUninstall'] = 'include/install.php';
$modversion['onInstall'] = 'include/install.php';
$modversion['onUpdate'] = 'include/install.php';

/**
 * Information for Common Utilities
 */
$modversion['rmnative'] = 1;
$modversion['rmversion'] = array('major'=>2,'minor'=>2,'revision'=>84,'stage'=>0,'name'=>'Common Utilities');
$modversion['rewrite'] = 1;
$modversion['author'] = "BitC3R0";
$modversion['authormail'] = "i.bitcero@gmail.com";
$modversion['authorweb'] = "Red México";
$modversion['authorurl'] = "http://redmexico.com.mx";
$modversion['updateurl'] = "http://www.xoopsmexico.net/modules/vcontrol/";
$modversion['icon16'] = "images/icon16.png";
$modversion['icon24'] = 'images/icon24.png';
$modversion['icon32'] = 'images/icon32.png';
$modversion['icon48'] = 'images/icon48.png';

// PERMISSIONS
$modversion['permissions'] = 'include/permissions.php';

$modversion['social'][0] = array('title' => __('Twitter', 'rmcommon'),'type' => 'twitter','url' => 'http://www.twitter.com/bitcero/');
$modversion['social'][1] = array('title' => __('Facebook', 'rmcommon'),'type' => 'facebook-square','url' => 'http://www.facebook.com/eduardo.cortes.hervis/');
$modversion['social'][2] = array('title' => __('Instagram', 'rmcommon'),'type' => 'instagram','url' => 'http://www.instagram.com/eduardocortesh/');
$modversion['social'][3] = array('title' => __('LinkedIn', 'rmcommon'),'type' => 'linkedin-square','url' => 'http://www.linkedin.com/in/bitcero/');
$modversion['social'][4] = array('title' => __('GitHub', 'rmcommon'),'type' => 'github','url' => 'http://www.github.com/bitcero/');
$modversion['social'][5] = array('title' => __('My Blog', 'rmcommon'),'type' => 'quote-left','url' => 'http://eduardocortes.mx');

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "index.php";
$modversion['adminmenu'] = "menu.php";

$modversion['hasMain'] = 1;

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

$modversion['tables'] = array(
    'mod_rmcommon_images_categories',
    'mod_rmcommon_comments',
    'mod_rmcommon_comments_assignations',
    'mod_rmcommon_images',
    'mod_rmcommon_plugins',
    'mod_rmcommon_permissions',
    'mod_rmcommon_settings',
    'mod_rmcommon_blocks',
    'mod_rmcommon_blocks_positions',
    'mod_rmcommon_blocks_assignations'
);

// Templates
$modversion['templates'][1]['file'] = 'rmc-comments-display.html';
$modversion['templates'][1]['description'] = 'Comments list';
$modversion['templates'][2]['file'] = 'rmc-comments-form.html';
$modversion['templates'][2]['description'] = 'Shows the comments form';

// Settings categories
$modversion['categories'] = array(
    'general' => __('General', 'rmcommon'),
    'appearance' => __('Appearance', 'rmcommon'),
    'comments' => __('Comentarios', 'rmcommon'),
    'email' => __('Email', 'rmcommon'),
);

// URL Rewriting
$modversion['config'][] = array(
    'name'          => 'permalinks',
    'title'         => __( 'Enable URL rewriting', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '0',
    'category'      => 'general'
);

// Modules path rewriting
$modversion['config'][] = array(
    'name'          => 'modules_path',
    'title'         => __( 'New rewrite paths for supported modules', 'rmcommon' ),
    'description'   => __( 'Indicate the new paths for supported modules. This path must be used to form new rewrited URLs for modules.', 'rmcommon' ),
    'formtype'      => 'modules-rewrite',
    'valuetype'    => 'array',
    'default'       => '',
    'category'      => 'general'
);

/**
* Language
*/
$modversion['config'][] = array(
    'name'          => 'lang',
    'title'         => __('Language to use','rmcommon'),
    'description'   => '',
    'formtype'      => 'cu-language',
    'valuetype'     => 'text',
    'default'       => 'en',
    'category'      => 'general'
);

$modversion['config'][] = array(
    'name'          => 'theme',
    'title'         => __('Admin theme','rmcommon'),
    'description'   => '',
    'formtype'      => 'cu-theme',
    'valuetype'     => 'text',
    'default'       => 'twop6',
    'category'      => 'appearance'
);

$modversion['config'][] = array(
    'name'          => 'gui_disable',
    'title'         => __('Disable new GUI when working on non native modules?','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

// Editor
$modversion['config'][] = array(
    'name'          => 'editor_type',
    'title'         => __('Select the default editor','rmcommon'),
    'description'   => '',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'default'       => 'tiny',
    'options'       => array(
        __('Visual Editor','rmcommon')=>'tiny',
        __('HTML Editor','rmcommon')=>'html',
        __('XoopsCode Editor','rmcommon')=>'xoops',
        __('Simple Editor','rmcommon')=>'simple'
    ),
    'category'      => 'general'
);

// JQuery inclusion
$modversion['config'][] = array(
    'name'          => 'jquery',
    'title'         => __('Enable JQuery for front end','rmcommon'),
    'description'   => __('When this option is enabled, Common Utilities will include JQuery automatically. Please, disable this option only when your theme include jquery by default.','rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '1',
    'category'      => 'general'
);

// Images store type
$modversion['config'][] = array(
    'name'          => 'imagestore',
    'title'         => __('Arrange images by date','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

// Images Categories list limit number
$modversion['config'][] = array(
    'name'          => 'catsnumber',
    'title'         => __('Limit for image categories list.','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 10,
    'category'      => 'general'
);

$modversion['config'][] = array(
    'name'          => 'imgsnumber',
    'title'         => __('Image manager: number of images per page','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 20,
    'category'      => 'general'
);

// Secure Key
if (!isset($xoopsSecurity)) $xoopsSecurity = new XoopsSecurity();
$modversion['config'][] = array(
    'name'          => 'secretkey',
    'title'         => __('Secret Key','rmcommon'),
    'description'   => __('Provide a secret key used to encrypt information.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => $xoopsSecurity->createToken(),
    'category'      => 'general'
);

// Formato HTML5
$modversion['config'][] = array(
    'name'          => 'dohtml',
    'title'         => __('Allow HTMl in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

$modversion['config'][] = array(
    'name'          => 'dosmileys',
    'title'         => __('Allow smilies in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

$modversion['config'][] = array(
    'name'          => 'doxcode',
    'title'         => __('Allow XoopsCode','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

$modversion['config'][] = array(
    'name'          => 'doimage',
    'title'         => __('Allow images in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'appearance'
);

$modversion['config'][] = array(
    'name'          => 'dobr',
    'title'         => __('Auto add line breaks in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'appearance'
);

// Comments
$modversion['config'][] = array(
    'name'          => 'enable_comments',
    'title'         => __('Enable comments','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$modversion['config'][] = array(
    'name'          => 'anonymous_comments',
    'title'         => __('Allow anonymous users to post comments','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$modversion['config'][] = array(
    'name'          => 'approve_reg_coms',
    'title'         => __('Automatically approve comments by registered users','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$modversion['config'][] = array(
    'name'          => 'approve_anon_coms',
    'title'         => __('Automatically approve comments by anonymous users','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'comments'
);

$modversion['config'][] = array(
    'name'          => 'allow_edit',
    'title'         => __('Allow users to edit their comments','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'comments'
);

$modversion['config'][] = array(
    'name'          => 'edit_limit',
    'title'         => __('Time limit to edit a comment (in hours).','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$modversion['config'][] = array(
    'name'          => 'mods_number',
    'title'         => __('Modules number on dashboard','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 6,
    'category'      => 'appearance'
);


$modversion['config'][] = array(
    'name'          => 'rssimage',
    'title'         => __('Image for RSS feeds','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => XOOPS_URL.'/modules/rmcommon/images/rssimage.png',
    'category'      => 'appearance'
);

/** Mailer Configurations **/
$modversion['config'][] = array(
    'name'          => 'transport',
    'title'         => __('Mailer method','rmcommon'),
    'description'   => __('Common Utilities will use this method to send emails.', 'rmcommon'),
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'options'       => array(
        __('PHP Mail()','rmcommon') => 'mail',
        __('SMTP','rmcommon') => 'smtp',
        __('Sendmail','rmcommon') => 'sendmail'
    ),
    'default'       => XOOPS_URL.'/modules/rmcommon/images/rssimage.png',
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'smtp_server',
    'title'         => __('SMTP server to use','rmcommon'),
    'description'   => __('Specify the server through with the emails will be sent.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '',
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'smtp_crypt',
    'title'         => __('SMTP encryption','rmcommon'),
    'description'   => __('For SSL or TLS encryption to work, your PHP installation must have appropriate OpenSSL transports wrappers.','rmcommon'),
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'options'       => array(
        __('None','rmcommon') => 'none',
        __('SSL','rmcommon') => 'ssl',
        __('TLS','rmcommon') => 'tls'
    ),
    'default'       => 'none',
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'smtp_port',
    'title'         => __('SMTP server port','rmcommon'),
    'description'   => __('Note that you must to write the appropriate port based on your encryption type selection.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => 25,
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'smtp_user',
    'title'         => __('SMTP username','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '',
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'smtp_pass',
    'title'         => __('SMTP password','rmcommon'),
    'description'   => '',
    'formtype'      => 'password',
    'valuetype'     => 'text',
    'default'       => '',
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'sendmail_path',
    'title'         => __('Sendmail path','rmcommon'),
    'description'   => __('Input the command for sendmail, including the correct command line flags. The default to use is "/usr/sbin/sendmail -bs" if this is not specified.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '/usr/sbin/sendmail -bs',
    'category'      => 'email'
);

$modversion['config'][] = array(
    'name'          => 'rss_enable',
    'title'         => __('Enable RSS Center','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

$modversion['config'][] = array(
    'name'          => 'blocks_enable',
    'title'         => __('Enable internal blocks manager','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'general'
);

$modversion['config'][] = array(
    'name'          => 'updates',
    'title'         => __('Activate updates','rmcommon'),
    'description'   => __('When this option is enabled, Common Utilities will search automatically updates for modules and other components.','rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

$modversion['config'][] = array(
    'name'          => 'updatesinterval',
    'title'         => __('Days between updates search','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
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
            $block['type'] = 'plugin';
            $modversion['blocks'][] = $block;
        }
        
    }
    
}

$modversion['subpages'] = array(
    'error404'=> __('Error 404', 'rmcommon' )
);