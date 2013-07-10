<?php
// $Id: modinfo.php 842 2011-12-14 05:16:26Z mambax7@gmail.com $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!function_exists("__"))
    include_once XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php';

// Configurations
define('_MI_RMC_LANG', __('Language to use','rmcommon'));
define('_MI_RMC_IMAGESTORE', __('Arrange images by date','rmcommon'));
define('_MI_RMC_EDITOR',__('Select the default editor','rmcommon'));
define('_MI_RMC_EDITOR_VISUAL',__('Visual Editor','rmcommon'));
define('_MI_RMC_EDITOR_HTML',__('HTML Editor','rmcommon'));
define('_MI_RMC_EDITOR_XOOPS',__('XoopsCode Editor','rmcommon'));
define('_MI_RMC_EDITOR_SIMPLE',__('Simple Editor','rmcommon'));
define('_MI_RMC_IMGCATSNUMBER',__('Limit for image categories list.','rmcommon'));
define('_MI_RMC_IMGSNUMBER',__('Image manager: number of images per page','rmcommon'));
define('_MI_RMC_SECREY',__('Secret Key','rmcommon'));
define('_MI_RMC_SECREYD',__('Provide a secret key used to encrypt information.','rmcommon'));
define('_MI_RMC_ADMTHEME', __('Admin theme','rmcommon'));
define('_MI_RMC_ADDJQUERY', __('Enable JQuery for front end','rmcommon'));
define('_MI_RMC_ADDJQUERYD', __('When this option is enabled, Common Utilities will include JQuery automatically. Please, disable this option only when your theme include jquery by default.','rmcommon'));

define('_MI_RMC_DOHTML',__('Allow HTMl in text','rmcommon'));
define('_MI_RMC_DOSMILE',__('Allow smilies in text','rmcommon'));
define('_MI_RMC_DOXCODE',__('Allow XoopsCode','rmcommon'));
define('_MI_RMC_DOIMAGE',__('Allow images in text','rmcommon'));
define('_MI_RMC_DOBR',__('Auto add line breaks in text','rmcommon'));

define('_MI_RMC_MODSNUMBER',__('Modules number on dashboard','rmcommon'));

// Comments
define('_MI_RMC_ENABLECOMS',__('Enable comments','rmcommon'));
define('_MI_RMC_ANONCOMS',__('Allow anonymous users to post comments','rmcommon'));
define('_MI_RMC_APPROVEREG',__('Automatically approve comments by registered users','rmcommon'));
define('_MI_RMC_APPROVEANON',__('Automatically approve comments by anonymous users','rmcommon'));
define('_MI_RMC_ALLOWEDIT',__('Allow users to edit their comments','rmcommon'));
define('_MI_RMC_EDITLIMIT',__('Time limit to edit a comment (in hours).','rmcommon'));

// RSS
define('_MI_RMC_RSSIMAGE', __('Image for RSS feeds','rmcommon'));

// Mailer Settings
define('_MI_RMC_MAILERMETH', __('Mailer method','rmcommon'));
define('_MI_RMC_MAILERMETHD', __('Common Utilities will use this method to send emails.'));
define('_MI_RMC_PHPMAIL', __('PHP Mail()','rmcommon'));
define('_MI_RMC_SMTP', __('SMTP','rmcommon'));
define('_MI_RMC_SENDMAIL', __('Sendmail','rmcommon'));
define('_MI_RMC_SMTPSERVER',__('SMTP server to use','rmcommon'));
define('_MI_RMC_SMTPSERVERD', __('Specify the server through with the emails will be sent.','rmcommon'));
define('_MI_RMC_ENCRYPT', __('SMTP encryption','rmcommon'));
define('_MI_RMC_ENCRYPTD', __('For SSL or TLS encryption to work, your PHP installation must have appropriate OpenSSL transports wrappers.','rmcommon'));
define('_MI_RMC_CRYPTNONE', __('None','rmcommon'));
define('_MI_RMC_CRYPTSSL', __('SSL','rmcommon'));
define('_MI_RMC_CRYPTTLS', __('TLS','rmcommon'));
define('_MI_RMC_SMTPPORT', __('SMTP server port','rmcommon'));
define('_MI_RMC_SMTPPORTD', __('Note that you must to write the appropriate port based on your encryption type selection.','rmcommon'));
define('_MI_RMC_SMTPUSER', __('SMTP username','rmcommon'));
define('_MI_RMC_SMTPPASS', __('SMTP password','rmcommon'));
define('_MI_RMC_SENDMAILPATH', __('Sendmail path','rmcommon'));
define('_MI_RMC_SENDMAILPATHD', __('Input the command for sendmail, including the correct command line flags. The default to use is "/usr/sbin/sendmail -bs" if this is not specified.','rmcommon'));

// RSS
define('_MI_RMC_RSSENABLE', __('Enable RSS Center','rmcommon'));

// Settings
define('_MI_RMC_BLOCKSENABLE', __('Enable internal blocks manager','rmcommon'));
define('_MI_RMC_GUIENABLE', __('Disable new GUI when working on non native modules?','rmcommon'));
define('_MI_RMC_UPDATES', __('Activate updates','rmcommon'));
define('_MI_RMC_UPDATESD', __('When this option is enabled, Common Utilities will search automatically updates for modules and other components.','rmcommon'));
define('_MI_RMC_UPDATESINTERVAL', __('Days between updates search','rmcommon'));
