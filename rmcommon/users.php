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

/**
* This file allow to manage users registered.
* plugins can extend this file functionallity
*/

include '../../include/cp_header.php';
$common->location = 'users';

/**
 * Get the formated SQL to query the database
 */
function formatSQL(){
    global $op;

    $keyw = '';
    $email = '';
    $url = '';
    $srhmethod = '';
    $from = '';
    $login1 = ''; $login2 = ''; $register1 = ''; $register2 = '';
    $posts1 = ''; $posts2 = ''; $mailok = -1; $actives = -1;
    $show = '';

    $tpl = RMTemplate::get();
    $sql = '';
    $tcleaner = TextCleaner::getInstance();

    foreach ($_REQUEST as $k => $v) {
        $$k = $tcleaner->addslashes($v);
    }

    $tpl->assign('srhkeyw', $keyw);
    $tpl->assign('srhemail', $email);
    $tpl->assign('srhurl', $url);
    $tpl->assign('srhsrhmethod', $srhmethod);
    $tpl->assign('srhfrom', $from);

    if ($show=='inactives') {
        $sql = "level<=0 AND ";
    } elseif ($show=='actives') {
        $sql = "level>0 AND ";
    }

    if ($keyw == '' && $email == '' && $url == '' && $from == ''
        && $login1 == '' && $login2 == '' && $register1 == '' && $register2 == '' && $posts1 == ''
        && $posts2 == '' && $mailok == -1 && $actives == -1) {

        if ($show=='inactives') {
            $sql = " level<=0";
        } elseif ($show=='actives') {
            $sql = " level>0";
        }

        $tpl->assign('display_adv', 'display: none;');
        // Extend SQL with plugins
        // API:
        $sql = RMEvents::get()->run_event('rmcommon.users.getsql', $sql);

        return $sql!='' ? "WHERE $sql": '';

    }

    $or = false;
    $ao = $srhmethod;
    $show = false;

    if ($keyw!='') {
        $sql .= "uname LIKE '%$keyw%' $ao name LIKE '%$keyw%'";
        $or = true;
    }

    if ($email!='') {
        $sql .= ($or ? " $ao " : '')."email LIKE '%$email%'";
        $or = true;
        $show = true;
    }

    if ($url!='') {
        $sql .= ($or ? " $ao " : '')."url LIKE '%$url%'";
        $or = true;
        $show = true;
    }

    if ($from!='') {
        $sql .= ($or ? " $ao " : '')."user_from LIKE '%$from%'";
        $or = true;
        $show = true;
    }

    if ($login1!='') {
        $sql .= ($or ? " $ao " : '').($login2!='' ? '(' : '')."last_login>='$login1'";
        $or = true;
        $show = true;
    }

    if ($login2!='') {
        $sql .= ($or ? ($login1!='' ? ' AND ' : " $ao ") : '')."last_login<='$login2'".($login1!='' ? ')' : '');
        $or = true;
        $show = true;
    }

    if ($registered1!='') {
        $sql .= ($or ? " $ao " : '').($registered2!='' ? '(' : '')."last_login>='$registered1'";
        $or = true;
        $show = true;
    }

    if ($registered2!='') {
        $sql .= ($or ? ($registered1!='' ? ' AND ' : " $ao ") : '')."last_login<='$registered2'".($registered1!='' ? ')' : '');
        $or = true;
        $show = true;
    }

    if ($posts1>0) {
        $sql .= ($or ? " $ao " : '').($posts2!='' ? '(' : '')."posts>='$posts1'";
        $or = true;
        $show = true;
    }

    if ($posts2>0) {
        $sql .= ($or ? ($posts1!='' ? ' AND ' : " $ao ") : '')."posts<='$posts2'".($posts1!='' ? ')' : '');
        $or = true;
        $show = true;
    }

    if ($mailok>-1) {
        $sql .= ($or ? " $ao " : '')."user_mailok='$mailok'";
        $or = true;
    }

    if ($actives>-1) {
        $sql .= ($or ? " $ao " : '')."level".($actives>0 ? ">'0'" : "<='0'");
        $or = true;
    }

    if ($show) { $tpl->assign('display_adv', ''); } else { $tpl->assign('display_adv', 'display: none;'); }

    $rtsql = $sql!='' ? "WHERE $sql" : '';
    // ** API **
    // Event to modify, if it is neccesary, the sql string to query de database
    $rtsql = RMEvents::get()->run_event('rmcommon.users.getsql', $rtsql);

    return $rtsql;

}

/**
* Shows all registered users in a list with filter and manage options
*/
function show_users(){
    global $xoopsSecurity, $rmTpl, $cuIcons;

    define('RMCSUBLOCATION','allusers');
    RMTemplate::get()->add_style('users.css','rmcommon');
    RMTemplate::get()->add_style('js-widgets.css');

    //Scripts
    RMTemplate::get()->add_script('users.js','rmcommon', array('directory' => 'include') );
    RMTemplate::get()->add_script('jquery.checkboxes.js','rmcommon', array('directory' => 'include') );

    RMTemplate::get()->add_head('<script type="text/javascript">var rmcu_select_message = "'.__('You have not selected any user!','rmcommon').'";
        var rmcu_message = "'.__('Dou you really wish to delete selected users?','rmcommon').'";</script>');

    $form = new RMForm('', '', '');
    // Date Field
    $login1 = new RMFormDate('','login1', '');
    $login1->addClass('form-control');
    $login2 = new RMFormDate('','login2', '') ;
    $login2->addClass('form-control');

    // Registered Field
    $register1 = new RMFormDate('','registered1', '');
    $register1->addClass('form-control');
    $register2 = new RMFormDate('','registered2', '');
    $register2->addClass('form-control');

    RMBreadCrumb::get()->add_crumb(__('Users Management','rmcommon'));
    $rmTpl->assign('xoops_pagetitle', __('Users Management','rmcommon'));

   RMFunctions::create_toolbar();

    // Show the theme
    xoops_cp_header();

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = "SELECT COUNT(*) FROM ".$db->prefix("users")." ".formatSQL();

    $page = rmc_server_var($_REQUEST, 'pag', 1);
    $limit = rmc_server_var($_REQUEST, 'limit', 15);
    $order = rmc_server_var($_GET,'order','uid');
    list($num) = $db->fetchRow($db->query($sql));

    $tpages = ceil($num / $limit);
    $page = $page > $tpages ? $tpages : $page;

    $start = $num<=0 ? 0 : ($page - 1) * $limit;

    $nav = new RMPageNav($num, $limit, $page, 5);
    $nav->target_url('users.php?limit='.$limit.'&order='.$order.'&pag={PAGE_NUM}');

    $sql = str_replace("COUNT(*)",'*', $sql);
    $sql .= "ORDER BY $order LIMIT $start, $limit";
    $result = $db->query($sql);

    $users = array();
    $t = array(); // Temporary
    while ($row=$db->fetchArray($result)) {
        $user = new RMUser();
        $user->assignVars($row);
        $t = $user->getValues();
        $t['groups'] =& $user->getGroups();
        $t = RMEvents::get()->trigger('rmcommon.loading.users.list', $t);
        $users[] = $t;
        $t = array();
    }

    $xgh = new XoopsGroupHandler($db);
    $users = RMEvents::get()->trigger('rmcommon.users.list.loaded', $users);

    // Users template
    include RMTemplate::get()->path('rmc-users.php','module','rmcommon');

    xoops_cp_footer();
}

/*
* Show the form to create or edit a user
*/
function user_form($edit = false){
    global $rmTpl;

    define('RMCSUBLOCATION','newuser');
    $query = rmc_server_var($_GET, 'query', '');
    $query = $query=='' ? '' : base64_decode($query);

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    if ($edit) {
        $uid = rmc_server_var($_GET, 'uid', 0);
        if ($uid<=0)
            redirectMsg('users.php?'.$query, __('The specified user is not valid!','rmcommon'), 1);

        $uh = new XoopsUserHandler($db);
        $user = $uh->get($uid);
        if ($user->isNew())
            redirectMsg('users.php?'.$query, __('The specified user does not exists!','rmcommon'), 1);
    } else {
        $user = new XoopsUser();
    }

    RMFunctions::create_toolbar();

    RMBreadCrumb::get()->add_crumb(__('Users Management','rmcommon'), 'users.php');
    RMBreadCrumb::get()->add_crumb($edit ? __('Edit User','rmcommon') : __('Add User','rmcommon'));

    $rmTpl->assign('xoops_pagetitle', $edit ? __('Edit User','rmcommon') : __('Add User','rmcommon'));

    xoops_cp_header();

    $form = new RMForm(__($edit ? 'Editing User' : 'Add new user','rmcommon'), 'user_form', 'users.php');

    // Uname
    $form->addElement(new RMFormText(__('Username','rmcommon'), 'uname', 50, 50, $edit ? $user->uname() : ''), true);
    $form->element('uname')->setDescription(__("This field also will be the user login name.",'rmcommon'));

    // Full Name
    $form->addElement(new RMFormText(__('Full name','rmcommon'), 'name', 50, 150, $edit ? $user->name() : ''));
    $form->element('name')->setDescription(__("This field must contain firstname and lastname.",'rmcommon'));

    // Email
    $form->addElement(new RMFormText(__('Email address','rmcommon'), 'email', 50, 150, $edit ? $user->email() : ''), true, 'email');

    // Password
    $form->addElement(new RMFormText(__($edit ? 'New password' : 'Password','rmcommon'), 'password', 50, 50, '', true), $edit ? false : true);
    $form->element('password')->setDescription(__('The password should be at least eight characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &','rmcommon'));
    $form->addElement(new RMFormText(__('Repeat Password','rmcommon'), 'passwordc', 50, 50, '', true), $edit ? false : true);
    $form->element('passwordc')->setDescription(__('Type password again.','rmcommon'));

    // Web
    $form->addElement(new RMFormText(__('URL (Blog or personal website)','rmcommon'), 'url', 50, 250, $edit ? $user->getVar('url') : ''));

    // Timezone
    $form->addElement(new RMFormTimeZoneField(__('Time zone','rmcommon'), 'timezone', 0, 0, $edit ? $user->getVar('timezone_offset') : ''));

    // Groups
    $form->addElement(new RMFormGroups([
        'caption' => __('Assign to groups','rmcommon'),
        'name' => 'groups',
        'multiple' => null,
        'type' => 'checkbox',
        'selected' => $user->groups()
    ]));

    // Other options by API
    $form = RMEvents::get()->run_event('rmcommon.user.form', $form, $edit, isset($user) ? $user : null);

    // Action
    $form->addElement(new RMFormHidden('action',$edit ? 'saveedit' : 'save'));
    if ($edit)
        $form->addElement(new RMFormHidden('uid',$user->uid()));

    // Submit and cancel buttons
    $ele = new RMFormButtonGroup('');
    $ele->addButton(new RMFormButton([
        'caption' => $edit ? __('Save Changes', 'rmcommon') : __('Save User', 'rmcommon'),
        'type' => 'submit',
        'class' => 'btn btn-primary btn-lg'
    ]));
    $ele->addButton(new RMFormButton([
        'caption' => __('Cancel', 'rmcommon'),
        'type' => 'button',
        'class' => 'btn btn-default btn-lg',
        'onclick' => 'history.go(-1);'
    ]));

    $form->addElement($ele);

    $form->display();

    xoops_cp_footer();
}

/**
* Save user data
*
* @param bool Indicates when is a edit
*/
function save_data($edit = false){
    global $xoopsSecurity, $xoopsDB;

    $q = ''; // Query String
    foreach ($_POST as $k => $v) {
        $$k = $v;
    if ($k=='XOOPS_TOKEN_REQUEST' || $k=='sbt' || $k=='action' || $k=='password' || $k=='passwordc') continue;
    $q .= $q=='' ? "$k=".urlencode($v) : "&$k=".urlencode($v);
    }

    if (!$xoopsSecurity->check()) {
    redirectMsg('users.php?action='.($edit ? 'edit' : 'new').'&'.$q, __('Sorry, you don\'t have permission to add users.','rmcommon'), 1);
    die();
    }

    if ($edit) {
    if ($uid<=0) {
            redirectMsg('users.php', __('The specified user is not valid!','rmcommon'), 1);
            die();
    }

        $user = new RMUser($uid);
    if ($user->isNew()) {
            redirectMsg('users.php', __('The specified user does not exists!','rmcommon'), 1);
            die();
    }
    } else {
    $user = new RMUser();
    }

    // Check uname, password and passwordc
    if ($uname=='' || $email=='' || (!$edit && ($password=='' || $passwordc==''))) {
        redirectMsg('users.php?action='.($edit ? 'edit' : 'new').'&'.$q, __('Please fill all required fields and try again!','rmcommon'), 1);
    die();
    }

    // Check passwords
    if ($password!=$passwordc) {
    redirectMsg('users.php?action='.($edit ? 'edit' : 'new').'&'.$q, __('Passwords doesn\'t match. Please chek them.','rmcommon'), 1);
    die();
    }

    // Check if user exists
    $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix( "users" ) . " WHERE (uname = '$uname' OR email = '$email')" . ( $edit ? " AND uid != " . $user->uid : '' );
    list( $exists ) = $xoopsDB->fetchRow( $xoopsDB->query( $sql ) );

    if ( $exists > 0 )
        RMUris::redirect_with_message(
            __('Another user with same username or email already exists!', 'rmcommon'), 'users.php?action='.($edit ? 'edit' : 'new').'&'.$q, RMMSG_ERROR
        );

    // Save user data
    $user->setVar('name', $name);
    $user->setVar('uname', $uname);
    $user->setVar('display_name', $display_name);
    $user->setVar('email', $email);
    if (!$edit) $user->assignVar('user_regdate', time());
    if ($password!='') $user->assignVar('pass', md5($password));
    $user->setVar('level', 1);
    $user->setVar('timezone_offset', $timezone);
    $user->setVar('url', $url);

    /**
     * If "All" has been selected then we need to get all
     * groups ID's
     */
    if( in_array( 0, $groups )){
        $groups = array();
        $result = $xoopsDB->query("SELECT groupid FROm " . $xoopsDB->prefix("groups"));
        while($row = $xoopsDB->fetchArray($result)){
            $groups[] = $row['groupid'];
        }
        unset($result);
    }

    /**
     * If no group has been selected, then we add user to
     * Anonymous group
     */
    if(empty($groups)){
        $groups = array(XOOPS_GROUP_ANONYMOUS);
    }

    $user->setGroups($groups);

    // Plugins and modules can save metadata.
    // Metadata are generated by other dynamical fields
    $user = RMEvents::get()->run_event('rmcommon.add.usermeta.4save', $user);

    if ($user->save()) {
    $user = RMEvents::get()->run_event($edit ? 'rmcommon.user.edited' : 'rmcommon.user.created', $user);
    redirectMsg('users.php', __('Database updated successfully!','rmcommon'), 0);
    } else {
    redirectMsg('users.php?action='.($edit ? 'edit' : 'new').'&'.$q, __('The users could not be saved. Please try again!','rmcommon').'<br />'.$user->errors(), 1);
    }

}

/**
* This function shows a form to send email to single or multiple users
*/
function show_mailer(){
    global $xoopsConfig, $rmc_config, $rmTpl;

    $uid = rmc_server_var($_GET, 'uid', array());
    $query = rmc_server_var($_GET, 'query', '');

    if (!is_array($uid) && $uid<=0 || empty($uid)) {
        // In admin control panel (side) add_message always must to be called before
        // ExmGUI::show_header()
        RMTemplate::get()->add_message(__('You must select one user at least. Please click on "Add Users" and select as many users as you wish.'), 0);
    }

    $uid = !is_array($uid) ? array($uid) : $uid;

    RMBreadCrumb::get()->add_crumb(__('Users Management','rmcommon'), 'users.php');
    RMBreadCrumb::get()->add_crumb(__('Send E-Mail','rmcommon'));
    $rmTpl->assign('xoops_pagetitle', __('Sending email to users','rmcommon'));

    xoops_cp_header();

    $form = new RMForm(__('Send Email to Users','rmcommon'), 'frm_mailer', 'users.php');

    $form->addElement(new RMFormUser(__('Users','global'), 'mailer_users', 1, $uid, 30, 600, 400));
    $form->element('mailer_users')->setDescription(__('Please note that the maximun users number that you can select depends of the limit of emails that you can send accourding to your email server policies (or hosting account policies).','rmcommon'));

    $form->addElement(new RMFormText(__('Message subject','rmcommon'), 'subject', 50, 255), true);
    $form->element('subject')->setDescription(__('Subject must be descriptive.','rmcommon'));
    $form->addElement(new RMFormRadio(__('Message type','rmcommon'), 'type', ' ', 1, 2));
    $form->element('type')->addOption(__('HTML','global'), 'html', 1, $rmc_config['editor_type']=='tiny' ? 'onclick="switchEditors.go(\'message\', \'tinymce\');"' : '');
    $form->element('type')->addOption(__('Plain Text','global'), 'text', 0, $rmc_config['editor_type']=='tiny' ? 'onclick="switchEditors.go(\'message\', \'html\');"': '');
    $form->addElement(new RMFormEditor(__('Message content','rmcommon'), 'message', '', '300px', ''), true);

    $ele = new RMFormButtonGroup();
    $ele->addButton('sbt', __('Send E-Mail','rmcommon'), 'submit');
    $ele->addButton('cancel', __('Cancel','rmcommon'), 'button', 'onclick="history.go(-1);"');
    $form->addElement($ele);

    $form->addElement(new RMFormHidden('action','sendmail'));
    $form->addElement(new RMFormHidden('query',$query));

    $form->display();

    xoops_cp_footer();
}

/**
* Send mail to selected users using Swift
*/
function send_mail(){
    global $rmc_config, $xoopsConfig;

    extract($_POST);
    // Creating a message
    $mailer = new RMMailer($type=='html' ? 'text/html' : 'text/plain');

    $mailer->add_xoops_users($mailer_users);
    $mailer->set_subject($subject);

    $message = $type=='html' ? TextCleaner::getInstance()->to_display($message) : $message;

    $mailer->set_body($message);

    if (!$mailer->batchSend()) {
        xoops_cp_header();
        echo "<h3>".__('There was errors while sending this emails','rmcommon')."</h3>";
        foreach ($mailer->errors() as $error) {
            echo "<div class='even'>".$error."</div>";
        }
        xoops_cp_footer();
    }

    redirectMsg('users.php?'.base64_decode($query), __('Message sent successfully!','rmcommon'), 0);

}

/**
* Deactivate selected users
*/
function activate_users($activate){
    global $xoopsSecurity;

    foreach ($_GET as $k => $v) {
        if ($k=='XOOPS_TOKEN_REQUEST' || $k=='action') continue;
        $q .= $q=='' ? "$k=".urlencode($v) : "&$k=".urlencode($v);
    }

    $uid = rmc_server_var($_POST, 'ids', array());

    if (empty($uid))
        redirectMsg('users.php?'.$q, __('No users has been selected','rmcommon'), 1);

    $in = '';
    foreach ($uid as $id) {
        $in .= $in=='' ? $id : ','.$id;
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "UPDATE ".$db->prefix("users")." SET level='$activate' WHERE uid IN($in)";

    if ($db->queryF($sql)) {
        redirectMsg('users.php?'.$q, __('Users '.($activate ? 'activated' : 'deactivated').' successfully!','rmcommon'), 0);
    } else {
        redirectMsg('users.php?'.$q, __('Users could not be '.($activate ? 'activated' : 'deactivated').'!','rmcommon'), 1);
    }

}

function delete_users(){
    global $xoopsSecurity;

    if (!$xoopsSecurity->check()) {
        redirectMsg("users.php", implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()), 1);
        die();
    }

    foreach ($_GET as $k => $v) {
        if ($k=='XOOPS_TOKEN_REQUEST' || $k=='action') continue;
        $q .= $q=='' ? "$k=".urlencode($v) : "&$k=".urlencode($v);
    }

    $uid = rmc_server_var($_POST, 'ids', array());
    $member_handler = xoops_getHandler('member', 'system');

    foreach ($uid as $id) {

        $user = $member_handler->getUser($id);
        $groups = $user->getGroups();

        if (in_array(XOOPS_GROUP_ADMIN, $groups)) {
            xoops_error( sprintf( __('Admin user cannot be deleted: %s','rmcommon'), $user->getVar("uname").'<br />') );
        } elseif (!$member_handler->deleteUser($user)) {
            xoops_error( sprintf( __('User cannot be deleted: %s','rmcommon'), $user->getVar("uname").'<br />') );
        } else {
            $online_handler = xoops_getHandler('online');
            $online_handler->destroy($uid);
            // RMV-NOTIFY
            xoops_notification_deletebyuser($uid);
        }

    }

    redirectMsg("users.php?".$q,__('Users deleted successfully!','rmcommon'),0);

}

// get the action
$action = RMHttpRequest::request( 'action', 'string', '' );

switch ($action) {
    case 'new':
        user_form();
        break;
    case 'edit':
        user_form(true);
        break;
    case 'save':
        save_data();
        break;
    case 'saveedit':
        save_data(true);
        break;
    case 'mailer':
        show_mailer();
        break;
    case 'sendmail':
        send_mail();
        break;
    case 'deactivate':
        activate_users(0);
        break;
    case 'activate':
        activate_users(1);
        break;
    case 'delete':
        delete_users();
        break;
    default:
        show_users();
        break;
}
