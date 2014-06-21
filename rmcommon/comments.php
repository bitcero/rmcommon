<?php
// $Id: comments.php 949 2012-04-14 04:18:10Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include_once '../../include/cp_header.php';
define('RMCLOCATION','comments');

function show_comments(){
    global $xoopsSecurity, $rmTpl;
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $keyw = rmc_server_var($_REQUEST, 'w', '');
    $filter = rmc_server_var($_REQUEST, 'filter', '');
    
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_comments");
    $sql .= $keyw!='' || $filter!='' ? ' WHERE ' : '';
    $sql .= $keyw!='' ? "(content LIKE '%$keyw%' OR ip LIKE '%$keyw%')" : '';
    $sql .= $filter!='' ? ($keyw!=''?' AND':'')." status='$filter'" : '';
    /**
     * Paginacion de Resultados
     */
    $page = rmc_server_var($_GET, 'page', 1);
    $limit = 15;
    list($num) = $db->fetchRow($db->query($sql));
    
    $tpages = ceil($num / $limit);
    $page = $page > $tpages ? $tpages : $page;

    $start = $num<=0 ? 0 : ($page - 1) * $limit;
    
    $nav = new RMPageNav($num, $limit, $page, 5);
    $nav->target_url('comments.php?page={PAGE_NUM}');
    
    $sql = str_replace("COUNT(*)",'*', $sql);
    $sql .= " ORDER BY posted DESC LIMIT $start,$limit";
    $result = $db->query($sql);
    $comments = array();
    
    $ucache = array();
    $ecache = array();
        
    while($row = $db->fetchArray($result)){
        $com = new RMComment();
        $com->assignVars($row);
        
        // Editor data
        if(!isset($ecache[$com->getVar('user')])){
            $ecache[$com->getVar('user')] = new RMCommentUser($com->getVar('user'));
        }
            
        $editor = $ecache[$com->getVar('user')];
            
        if($editor->getVar('xuid')>0){
            
            if(!isset($ucache[$editor->getVar('xuid')])){
                $ucache[$editor->getVar('xuid')] = new XoopsUser($editor->getVar('xuid'));
            }
                
            $user = $ucache[$editor->getVar('xuid')];
                
            $poster = array(
                'id' => $user->getVar('uid'),
                'name'  => $user->getVar('uname'),
                'email' => $user->getVar('email'),
                'posts' => $user->getVar('posts'),
                'avatar'=> $user->getVar('image')!='' && $user->getVar('image')!='blank.gif' ? XOOPS_UPLOAD_URL.'/'.$user->getVar('image') : RMCURL.'/images/avatar.gif',
                'rank'  => $user->rank(),
            );
            
        } else {
                
            $poster = array(
                'id'    => 0,
                'name'  => $editor->getVar('name'),
                'email' => $editor->getVar('email'),
                'posts' => 0,
                'avatar'=> RMCURL.'/images/avatar.gif',
                'rank'  => ''
            );
                
        }
        
        // Get item
        $cpath = XOOPS_ROOT_PATH.'/modules/'.$row['id_obj'].'/class/'.$row['id_obj'].'controller.php';
        
        if(is_file($cpath)){
			if(!class_exists(ucfirst($row['id_obj']).'Controller'))
				include_once $cpath;
			
			$class = ucfirst($row['id_obj']).'Controller';
			$controller = new $class();
			$item = $controller->get_item($row['params'], $com);
            if(method_exists($controller, 'get_item_url'))
			    $item_url = $controller->get_item_url($row['params'], $com);
			
        } else {
			
			$item = __('Unknow','rmcommon');
			$item_url = '';
			
        }
        
        $comments[] = array(
            'id'        => $row['id_com'],
            'text'      => TextCleaner::getInstance()->clean_disabled_tags(TextCleaner::getInstance()->popuplinks(TextCleaner::getInstance()->nofollow($com->getVar('content')))),
            'poster'    => $poster,
            'posted'    => sprintf(__('Posted on %s','rmcommon'), formatTimestamp($com->getVar('posted'), 'l')),
            'ip'        => $com->getVar('ip'),
            'item'		=> $item,
			'item_url'  => $item_url,
            'module'	=> $row['id_obj'],
            'status'	=> $com->getVar('status')
        );
    }
    
    $comments = RMEvents::get()->run_event('rmcommon.loading.admin.comments', $comments);

	RMBreadCrumb::get()->add_crumb(__('Comments Manager','rmcommon'));

	$rmTpl->assign('xoops_pagetitle', __('Comments Manager','rmcommon'));

    xoops_cp_header();
    //RMFunctions::create_toolbar();
    RMTemplate::get()->add_style('comms-admin.css', 'rmcommon');
    RMTemplate::get()->add_style('general.css', 'rmcommon');
    RMTemplate::get()->add_script('include/js/jquery.checkboxes.js');
    RMTemplate::get()->add_script('include/js/comments.js');
    $script = '<script type="text/javascript">delmes = "'.__('Do you really want to delete this comment?','rmcommon').'";</script>';
    RMTemplate::get()->add_head($script);
    include RMTemplate::get()->get_template('rmc-comments.php','module','rmcommon');
    xoops_cp_footer();
    
}

/**
* Change comment status
* @param string status
*/
function set_comments_status($status){
	global $xoopsSecurity;
	
	if ($status!='waiting' && $status!='approved' && $status!='spam'){
		redirectMsg('comments.php', __('Invalid operation','rmcommon'), 1);
		die();
	}
	
	$coms = rmc_server_var($_POST, 'coms', array());
	$page = rmc_server_var($_POST, 'page', 1);
	$filter = rmc_server_var($_POST, 'filter', '');
	$w = rmc_server_var($_POST, 'w', '');
	
	$qs = "page=$page&filter=$filter&w=$w";
	
	if(!$xoopsSecurity->check()){
		redirectMsg('comments.php?'.$qs, __('Sorry, session token expired!','rmcommon'), 1);
		die();
	}
	
	if(!is_array($coms)){
		redirectMsg('comments.php?'.$qs, __('Unrecognized data!','rmcommon'), 1);
		die();
	}
	
	$db = XoopsDatabaseFactory::getDatabaseConnection();
	$sql = "UPDATE ".$db->prefix("mod_rmcommon_comments")." SET status='$status' WHERE id_com IN (".implode(",",$coms).")";
	
	if($db->queryF($sql)){
		
		RMEvents::get()->run_event('rmcommon.updated.comments',$coms, $status);
		
		redirectMsg('comments.php?'.$qs, __('Comments updated successfully!','rmcommon'), 0);
		die();
		
	} else {
		
		redirectMsg('comments.php?'.$qs, __('Errors occurrs while trying to update comments!','rmcommon'), 1);
		die();
		
	}
	
}

function delete_comments(){
	global $xoopsSecurity;
	
	$coms = rmc_server_var($_POST, 'coms', array());
	$page = rmc_server_var($_POST, 'page', 1);
	$filter = rmc_server_var($_POST, 'filter', '');
	$w = rmc_server_var($_POST, 'w', '');
	
	$qs = "page=$page&filter=$filter&w=$w";
	
	if(!$xoopsSecurity->check()){
		redirectMsg('comments.php?'.$qs, __('Sorry, session token expired!','rmcommon'), 1);
		die();
	}
	
	if(!is_array($coms)){
		redirectMsg('comments.php?'.$qs, __('Unrecognized data!','rmcommon'), 1);
		die();
	}
	
	// We need to delete each comment separated
	foreach ($coms as $id){
		$com = new RMComment($id);
		
		if($com->isNew()) continue;
		
		$cpath = XOOPS_ROOT_PATH.'/modules/'.$com->getVar('id_obj').'/class/'.$com->getVar('id_obj').'controller.php';
        
        if(!$com->delete()) return;
        
        if(is_file($cpath)){
			if(!class_exists(ucfirst($com->getVar('id_obj')).'Controller'))
				include_once $cpath;
			
			$class = ucfirst($com->getVar('id_obj')).'Controller';
			$controller = new $class();
			$item = $controller->reduce_comments_number($com);
			
        } else {
			
			$item = __('Unknow','rmcommon');
			
        }
		
	}
	
	redirectMsg('comments.php', __('Comments deleted successfully!','rmcommon'), 0);
	
}

function edit_comment(){
	global $rmTpl;

	$id = rmc_server_var($_GET,'id',0);
	$page = rmc_server_var($_GET, 'page', 1);
	$filter = rmc_server_var($_GET, 'filter', '');
	$w = rmc_server_var($_GET, 'w', '1');
	
	$qs = "w=$w&page=$page&filter=$filter";
	
	if($id<=0){
		redirectMsg('comments.php?'.$qs, __('Sorry, comment id is not valid','rmcommon'), 1);
		die();
	}
	
	$comment = new RMComment($id);
	if($comment->isNew()){
		redirectMsg('comments.php?'.$qs, __('Sorry, comment does not found','rmcommon'), 1);
		die();
	}
	
	$cpath = XOOPS_ROOT_PATH.'/modules/'.$comment->getVar('id_obj').'/class/'.$comment->getVar('id_obj').'controller.php';
	
	if(is_file($cpath)){
		include $cpath;
		$class = ucfirst($comment->getVar('id_obj')).'Controller';
		$controller = new $class();
	}
	
	$form = new RMForm(__('Edit Comment', 'rmcommon'), 'editComment', 'comments.php');
	$form->addElement(new RMFormLabel(__('In reply to', 'rmcommon'), $controller ? $controller->get_item($comment->getVar('params'), $comment):''));
	$form->addElement(new RMFormLabel(__('Posted date','rmcommon'), formatTimestamp($comment->getVar('posted'), 'mysql')));
	$form->addElement(new RMFormLabel(__('Module','rmcommon'), $comment->getVar('id_obj')));
	$form->addElement(new RMFormLabel(__('IP','rmcommon'), $comment->getVar('ip')));
	
	$user = new RMCommentUser($comment->getVar('user'));
	$ele = new RMFormUser(__('Poster','rmcommon'), 'user', false, $user->getVar('xuid')>0 ? $user->getVar('xuid') : 0);
	$form->addElement($ele);
	
	$ele = new RMFormRadio(__('Status','rmcommon'), 'status', 1, 0, 2);
	$ele->addOption(__('Approved', 'rmcommon'), 'approved', $comment->getVar('status')=='approved'?1:0);
	$ele->addOption(__('Unapproved', 'rmcommon'), 'waiting', $comment->getVar('status')=='waiting'?1:0);
	$form->addElement($ele);
	
	$form->addElement(new RMFormTextArea(__('Content','rmcommon'), 'content', null, null, $comment->getVar('content','e'),'100%','150px'), true);
	$form->addElement(new RMFormHidden('page', $page));
	$form->addElement(new RMFormHidden('filter', $filter));
	$form->addElement(new RMFormHidden('w', $w));
	$form->addElement(new RMFormHidden('id', $id));
	$form->addElement(new RMFormHidden('action', 'save'));
	$ele = new RMFormButtonGroup();
	$ele->addButton('sbt', __('Update Comment','rmcommon'), 'submit');
	$ele->addButton('cancel', __('Cancel','rmcommon'), 'button', 'onclick="history.go(-1);"');
	$form->addElement($ele);
	
    //RMFunctions::create_toolbar();

	RMBreadCrumb::get()->add_crumb(__('Comments Manager','rmcommon'),'comments.php');
	RMBreadCrumb::get()->add_crumb(__('Edit Comment','rmcommon'));

	$rmTpl->assign('xoops_pagetitle', __('Edit Comment','rmcommon'));

	xoops_cp_header();
	$form->display();
	xoops_cp_footer();
	
}

function save_comment(){
	global $xoopsSecurity;
	
	$id = rmc_server_var($_POST,'id',0);
	$page = rmc_server_var($_POST, 'page', 1);
	$filter = rmc_server_var($_POST, 'filter', '');
	$w = rmc_server_var($_POST, 'w', '1');
	
	$qs = "id=$id&w=$w&page=$page&filter=$filter";
	
	if(!$xoopsSecurity->check()){
		redirectMsg('comments.php?action=edit&'.$qs, __('Sorry, session token expired!','rmcommon'), 1);
		die();
	}
    
    if ($id<=0){
        redirectMsg('comments.php', __('Comment ID not specified!', 'rmcommon'), 1);
        die();
    }
    
    $comment = new RMComment($id);
    if($comment->isNew()){
        redirectMsg('comments.php?'.$qs, __('Specified comment does not exist!', 'rmcommon'), 1);
        die();
    }
	
	$status = rmc_server_var($_POST, 'status', 'unapproved');
	$status = $status=='approved'?$status:'unapproved';
	
	$user = rmc_server_var($_POST, 'user', 0);
	$content = rmc_server_var($_POST, 'content', '');
    
    // save basic info in comment object
    $comment->setVar('content', $content);
    $comment->setVar('status', $status);
    // Modify, if neccessary, the user
    $cuser = new RMCommentUser($comment->getVar('user'));
    if ($cuser->getVar('xuid')!=$user){
        
        if ($user==0){
            $cuser->setVar('xuid', 0);
            $cuser->save();
        } else {
            $xuser = new XoopsUser($user);
            $cuser = new RMCommentUser($xuser->getVar('email'));
            $cuser->setVar('name', $xuser->getVar('uname'));
            $cuser->setVar('email', $xuser->getVar('email'));
            $cuser->setVar('xuid', $user);
            $cuser->setVar('url', $xuser->getVar('url'));
            $cuser->save();
        }
        
        $comment->setVar('user', $cuser->id());
        
    }

    if ($comment->save()){
        redirectMsg('comments.php?'.$qs, __('Comment updated successfully!','rmcommon'), 0);
    } else {
        redirectMsg('comments.php?action=edit&'.$qs, __('Errros ocurrs while trying to update comment!', 1).'<br />'.$comment->errors(), 1);
    }
	
}



$action = rmc_server_var($_REQUEST, 'action', '');

switch($action){
    case 'approve':
    	set_comments_status('approved');
    	break;
    case 'unapprove':
    	set_comments_status('waiting');
    	break;
    case 'spam':
    	set_comments_status('spam');
    	break;
    case 'delete':
    	delete_comments();
    	break;
    case 'edit':
    	edit_comment();
    	break;
    case 'save':
    	save_comment();
    	break;
    default:
        show_comments();
        break;
}
