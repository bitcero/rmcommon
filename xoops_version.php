<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
 * -------------------------------------------------------------
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

if (!function_exists("__")){
    function __($text, $d = ''){
        return $text;
    }
}
global $xoopsUser, $common;

$modversion['name'] = 'Common Utilities';
$modversion['version'] = 2.3;
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
$modversion['rmversion'] = array('major'=>2,'minor'=>3,'revision'=>67,'stage'=>0,'name'=>'Common Utilities');
$modversion['rewrite'] = 1;
$modversion['url'] = "http://rmcommon.com";
$modversion['author'] = "Eduardo Cortés";
$modversion['authormail'] = "i.bitcero@gmail.com";
$modversion['authorweb'] = "Eduardo Cortés";
$modversion['authorurl'] = "http://eduardocortes.mx";
//$modversion['updateurl'] = "https://www.xoopsmexico.net/modules/vcontrol/";
$modversion['icon'] = 'svg-rmcommon-rmcommon rmcommon-icon';

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
    'mod_rmcommon_notifications',
    'mod_rmcommon_blocks_positions',
    'mod_rmcommon_blocks_assignations'
);

// Templates
$modversion['templates'][1]['file'] = 'rmc-comments-display.html';
$modversion['templates'][1]['description'] = 'Comments list';
$modversion['templates'][2]['file'] = 'rmc-comments-form.html';
$modversion['templates'][2]['description'] = 'Shows the comments form';

// Settings categories
$cu_settings = array();
$cu_settings['categories'] = array(
    'general' => __('General', 'rmcommon'),
    'appearance' => __('Appearance', 'rmcommon'),
    'comments' => __('Comentarios', 'rmcommon'),
    'email' => __('Email', 'rmcommon'),
    'components' => __('Components', 'rmcommon')
);

// Jquery cdn
$cu_settings['config'][] = array(
    'name'          => 'cdn_jquery',
    'title'         => __( 'Use CDN for jQuery', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '0',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_jquery_url',
    'title'         => __( 'jQuery CDN URI', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => 'https://code.jquery.com/jquery-2.1.3.min.js',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_jqueryui_url',
    'title'         => __( 'jQuery UI CDN URI', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => 'https://code.jquery.com/ui/1.11.2/jquery-ui.min.js',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_bootstrap',
    'title'         => __( 'Use CDN for Bootstrap', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '0',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_bootstrap_url',
    'title'         => __( 'Bootstrap CDN URI', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_jsbootstrap_url',
    'title'         => __( 'Bootstrap Javascript CDN URI', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_fa',
    'title'         => __( 'Use CDN for FontAwesome', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '0',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'cdn_fa_url',
    'title'         => __( 'FontAwesome CDN URI', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
    'category'      => 'components'
);

$cu_settings['config'][] = array(
    'name'          => 'development',
    'title'         => __( 'Enable development stage', 'rmcommon' ),
    'description'   => __('Enable features for development stage, such as disable CSS and JS files caching and more.', 'rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'components'
);

// URL Rewriting
$cu_settings['config'][] = array(
    'name'          => 'permalinks',
    'title'         => __( 'Enable URL rewriting', 'rmcommon' ),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '0',
    'category'      => 'general'
);

// Modules path rewriting
$cu_settings['config'][] = array(
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
$cu_settings['config'][] = array(
    'name'          => 'lang',
    'title'         => __('Language to use','rmcommon'),
    'description'   => '',
    'formtype'      => 'cu-language',
    'valuetype'     => 'text',
    'default'       => 'en',
    'category'      => 'general'
);

$cu_settings['config'][] = array(
    'name'          => 'theme',
    'title'         => __('Admin theme','rmcommon'),
    'description'   => '',
    'formtype'      => 'cu-theme',
    'valuetype'     => 'text',
    'default'       => 'helium',
    'category'      => 'appearance'
);

$cu_settings['config'][] = array(
    'name'          => 'gui_disable',
    'title'         => __('Disable new GUI when working on non native modules?','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

// Editor
$cu_settings['config'][] = array(
    'name'          => 'editor_type',
    'title'         => __('Select the default editor','rmcommon'),
    'description'   => '',
    'formtype'      => 'select',
    'valuetype'     => 'text',
    'default'       => 'tiny',
    'options'       => array(
        //__('Visual Editor Quill','rmcommon') =>'quill',
        __('Visual Editor TinyMCE','rmcommon') =>'tiny',
        __('HTML Editor','rmcommon') => 'html',
        __('Simple Editor','rmcommon') => 'simple',
        __('Markdown Editor','rmcommon') => 'markdown'
    ),
    'category'      => 'general'
);

$cu_settings['config'][] = array(
    'name'          => 'markdown',
    'title'         => __('Parse MarkDown','rmcommon'),
    'description'   => __('This option enables the parsing for Markdown code for text. Must be enabled when "Markdown Editor" is selected.', 'rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

// JQuery inclusion
$cu_settings['config'][] = array(
    'name'          => 'jquery',
    'title'         => __('Enable JQuery for front end','rmcommon'),
    'description'   => __('When this option is enabled, Common Utilities will include JQuery automatically. Please, disable this option only when your theme include jquery by default.','rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => '1',
    'category'      => 'general'
);

// Images store type
$cu_settings['config'][] = array(
    'name'          => 'imagestore',
    'title'         => __('Arrange images by date','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

// Images Categories list limit number
$cu_settings['config'][] = array(
    'name'          => 'catsnumber',
    'title'         => __('Limit for image categories list.','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 10,
    'category'      => 'general'
);

$cu_settings['config'][] = array(
    'name'          => 'imgsnumber',
    'title'         => __('Image manager: number of images per page','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 20,
    'category'      => 'general'
);

// Secure Key
global $common;
if (!isset($xoopsSecurity)) $xoopsSecurity = new XoopsSecurity();
$cu_settings['config'][] = array(
    'name'          => 'secretkey',
    'title'         => __('Secret Key','rmcommon'),
    'description'   => __('Provide a secret key used to encrypt information.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => $common->settings->secretkey != '' ? '' : password_hash( XOOPS_URL . $_SERVER['SERVER_SOFTWARE'] . $_SERVER['GATEWAY_INTERFACE'] . $_SERVER['SERVER_ADDR'] . $_SERVER['SERVER_ADMIN'] . $_SERVER['SERVER_SIGNATURE'] . $GLOBALS['xoopsUser']->uname(), PASSWORD_DEFAULT),
    'category'      => 'general'
);

// Formato HTML5
$cu_settings['config'][] = array(
    'name'          => 'dohtml',
    'title'         => __('Allow HTMl in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

$cu_settings['config'][] = array(
    'name'          => 'dosmileys',
    'title'         => __('Allow smilies in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

$cu_settings['config'][] = array(
    'name'          => 'doxcode',
    'title'         => __('Allow XoopsCode','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'appearance'
);

$cu_settings['config'][] = array(
    'name'          => 'doimage',
    'title'         => __('Allow images in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'appearance'
);

$cu_settings['config'][] = array(
    'name'          => 'dobr',
    'title'         => __('Auto add line breaks in text','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'appearance'
);

$cu_settings['config'][] = array(
    'name'          => 'mods_number',
    'title'         => __('Modules number on dashboard','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 6,
    'category'      => 'appearance'
);


$cu_settings['config'][] = array(
    'name'          => 'rssimage',
    'title'         => __('Image for RSS feeds','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => XOOPS_URL.'/modules/rmcommon/images/rssimage.png',
    'category'      => 'appearance'
);

// Comments
$cu_settings['config'][] = array(
    'name'          => 'enable_comments',
    'title'         => __('Enable comments','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$cu_settings['config'][] = array(
    'name'          => 'anonymous_comments',
    'title'         => __('Allow anonymous users to post comments','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$cu_settings['config'][] = array(
    'name'          => 'approve_reg_coms',
    'title'         => __('Automatically approve comments by registered users','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$cu_settings['config'][] = array(
    'name'          => 'approve_anon_coms',
    'title'         => __('Automatically approve comments by anonymous users','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'comments'
);

$cu_settings['config'][] = array(
    'name'          => 'allow_edit',
    'title'         => __('Allow users to edit their comments','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'comments'
);

$cu_settings['config'][] = array(
    'name'          => 'edit_limit',
    'title'         => __('Time limit to edit a comment (in hours).','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

$cu_settings['config'][] = array(
    'name'          => 'comments_notify',
    'title'         => __('Notify administrators about new comments','rmcommon'),
    'description'   => __('By enabling this option, Common Utilities will send a notification to webmasters every time that a new comment is posted.', 'rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'comments'
);

/** Mailer Configurations **/
$cu_settings['config'][] = array(
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
    'default'       => 'mail',
    'category'      => 'email'
);

$cu_settings['config'][] = array(
    'name'          => 'smtp_server',
    'title'         => __('SMTP server to use','rmcommon'),
    'description'   => __('Specify the server through with the emails will be sent.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '',
    'category'      => 'email'
);

$cu_settings['config'][] = array(
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

$cu_settings['config'][] = array(
    'name'          => 'smtp_port',
    'title'         => __('SMTP server port','rmcommon'),
    'description'   => __('Note that you must to write the appropriate port based on your encryption type selection.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => 25,
    'category'      => 'email'
);

$cu_settings['config'][] = array(
    'name'          => 'smtp_user',
    'title'         => __('SMTP username','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '',
    'category'      => 'email'
);

$cu_settings['config'][] = array(
    'name'          => 'smtp_pass',
    'title'         => __('SMTP password','rmcommon'),
    'description'   => '',
    'formtype'      => 'password',
    'valuetype'     => 'text',
    'default'       => '',
    'category'      => 'email'
);

$cu_settings['config'][] = array(
    'name'          => 'sendmail_path',
    'title'         => __('Sendmail path','rmcommon'),
    'description'   => __('Input the command for sendmail, including the correct command line flags. The default to use is "/usr/sbin/sendmail -bs" if this is not specified.','rmcommon'),
    'formtype'      => 'textbox',
    'valuetype'     => 'text',
    'default'       => '/usr/sbin/sendmail -bs',
    'category'      => 'email'
);

$cu_settings['config'][] = array(
    'name'          => 'rss_enable',
    'title'         => __('Enable RSS Center','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

$cu_settings['config'][] = array(
    'name'          => 'blocks_enable',
    'title'         => __('Enable internal blocks manager','rmcommon'),
    'description'   => '',
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 0,
    'category'      => 'general'
);

$cu_settings['config'][] = array(
    'name'          => 'updates',
    'title'         => __('Activate updates','rmcommon'),
    'description'   => __('When this option is enabled, Common Utilities will search automatically updates for modules and other components.','rmcommon'),
    'formtype'      => 'yesno',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

$cu_settings['config'][] = array(
    'name'          => 'updatesinterval',
    'title'         => __('Days between updates search','rmcommon'),
    'description'   => '',
    'formtype'      => 'textbox',
    'valuetype'     => 'int',
    'default'       => 1,
    'category'      => 'general'
);

// Additional configurations
if( class_exists('RMEvents') ){
    $cu_settings = RMEvents::get()->run_event('rmcommon.additional.options', $cu_settings);
}

$modversion['categories']   = $cu_settings['categories'];
$modversion['config']       = $cu_settings['config'];
unset($cu_settings);

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
    $plugins = Common\Core\Helpers\Plugins::allInstalled();
    foreach($plugins as $plugin){
        $p = Common\Core\Helpers\Plugins::getInstance()->load($plugin);
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
