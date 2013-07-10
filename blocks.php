<?php
// $Id: blocks.php 952 2012-05-06 23:23:46Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION','blocks');
include '../../include/cp_header.php';

function createSQL()
{
    $mid = isset($_GET['mid']) ? intval($_GET['mid']) : 0;
    $subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';
    $group = isset($_GET['group']) ? intval($_GET['group']) : 0;
    $visible = isset($_GET['visible']) ? intval($_GET['visible']) : -1;
    $pos = isset($_GET['pos']) ? intval($_GET['pos']) : -1;
    
    $wid_globals = array(
        'mid'=>$mid,
        'subpage'=>$subpage,
        'group'=>$group,
        'visible'=>$visible
    );
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    // Obtenemos los widgets
    $tblw = $db->prefix("mod_rmcommon_blocks");
    $tbll = $db->prefix("mod_rmcommon_blocks_assignations");
    $tblp = $db->prefix("group_permission");
    
    $sql = "SELECT $tblw.* FROM $tblw ".($subpage!='' || $mid>0 ? ", $tbll" : '').($group > 0 ? ", $tblp" : '');
    
    $and = false;
    
    if ($mid>0){
        $sql .= " WHERE ($tbll.mid='$mid' AND $tblw.bid=$tbll.bid ".($subpage!='' ? " AND $tbll.subpage='$subpage'" : '').") ";
        $and = true;
    }
    
    if ($group>0){
        $sql .= $and ? " AND " : " WHERE ";
        $sql .= " ($tblp.gperm_itemid=$tblw.bid AND $tblp.gperm_name='rmblock_read' AND $tblp.gperm_groupid='$group')";
    }
    
    if ($pos>0){
        $sql .= $and ? " AND " : ' WHERE ';
        $sql .= " $tblw.canvas='$pos'";
        $and = true;
    }
    
    if($visible>-1){
        $sql .= $and ? " AND " : " WHERE ";
        $sql .= " $tblw.visible=$visible";
        $and = true;
    }
    
    $sql .= " ORDER BY weight";
    
    return $sql;

}

function show_rm_blocks()
{
    global $xoopsModule, $xoopsConfig, $wid_globals, $xoopsSecurity, $rmc_config, $rmTpl;
    define('RMCSUBLOCATION','blocks');
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $modules = RMFunctions::get_modules_list(1);

    $from = rmc_server_var($_GET, 'from', '');
    
    // ** API Event **
    // Allows other methods to add o modify the list of available widgets
    $modules = RMEvents::get()->run_event('rmcommon.blocks.modules', $modules);

    // Cargamos los grupos
    $sql = "SELECT groupid, name FROM " . $db->prefix("groups") . " ORDER BY name";
    $result = $db->query($sql);
    $groups = array();
    while ($row = $db->fetchArray($result)) {
        $groups[] = array('id' => $row['groupid'], 'name' => $row['name']);
    }
    
    // Cargamos las posiciones de bloques
    $bpos = RMBlocksFunctions::block_positions();

    $sql = createSQL();
    $result = $db->query($sql);
    $blocks = array();
    $used_blocks = array();
    while ($row = $db->fetchArray($result)) {
        $mod = RMFunctions::load_module($row['element']);
        if(!$mod) continue;
        $used_blocks[$row['canvas']][] = array(
            'id' => $row['bid'],
            'title' => $row['name'],
            'module' => array('id' => $mod->mid(), 'dir' => $mod->dirname(), 'name' => $mod->name()),
            'canvas' => $bpos[$row['canvas']],
            'weight' => $row['weight'],
            'visible'=>$row['visible'],
            'active'=>$row['isactive'],
            'type'=>$row['type'],
            'options'=>$row['edit_func']!='' ? 1 : 0,
            'description'=>$row['description']
        );
    }

    // ** API **
    // Event for manege the used widgets list
    $used_blocks = RMEvents::get()->run_event('rmcommon.used.blocks.list', $used_blocks);
    
    $positions = array();
    foreach ($bpos as $row){
        $positions[] = array(
            'id' => $row['id_position'],
            'name' => $row['name'],
            'tag' => $row['tag'],
            'active' => $row['active']
        );
    }
    
    $positions = RMEvents::get()->run_event('rmcommon.block.positions.list', $positions);

    RMTemplate::get()->add_script('jquery.nestable.js', 'rmcommon', array('directory' => 'include'));

	RMBreadCrumb::get()->add_crumb(__('Blocks Management','rmcommon'));
	$rmTpl->assign('xoops_pagetitle', __('Blocks Management','rmcommon'));

    RMTemplate::get()->add_style('blocks.css', 'rmcommon');
    RMTemplate::get()->add_script('blocks.js', 'rmcommon', array('directory' => 'include'));
    RMTemplate::get()->add_script('jkmenu.js', 'rmcommon', array('directory' => 'include'));
    RMTemplate::get()->add_style('forms.css', 'rmcommon');
    RMTemplate::get()->add_script('jquery-ui.min.js', 'rmcommon', array('directory' => 'include'));
    
    if(!$rmc_config['blocks_enable']){
        showMessage(__('Internal blocks manager is currenlty disabled!','rmcommon'), 0);
    }
    
    RMTemplate::get()->add_script('jquery.checkboxes.js','rmcommon','include');
    RMTemplate::get()->add_head_script(include('include/js-lang.php'));
    
    xoops_cp_header();
    
    // Available Widgets
    
    $blocks = RMBlocksFunctions::get_available_list($modules);
    
    foreach($blocks as $id => $block){
        if(empty($block['blocks'])) continue;
        foreach($block['blocks'] as $bid => $val){
            $str = isset($val['show_func']) ? $val['show_func'] : '';
            $str .= isset($val['edit_func']) ? $val['edit_func'] : '';
            $str .= isset($val['dir']) ? $val['dir'] : $id;
            $val['id'] = md5($str);
            $blocks[$id]['blocks'][$bid] = $val;
        }
    }
    
    // Position
    $the_position = isset($_GET['pos']) ? intval($_GET['pos']) : '';
    
    // Parameters
    $mid = rmc_server_var($_GET,'mid',0);
    $subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';
    $group = isset($_GET['group']) ? intval($_GET['group']) : 0;
    $visible = rmc_server_var($_GET,'visible',-1);
    $pid = rmc_server_var($_GET,'pos',0);

    include RMTemplate::get()->get_template("rmc-blocks.php", 'module', 'rmcommon');
    
    xoops_cp_footer();
}

/**
* Save the current positions
*/
function save_position($edit = 0){
    global $xoopsSecurity;
    
    if (!$xoopsSecurity->check()){
        redirectMsg('blocks.php', __('You are not allowed to do this action!','rmcommon'), 1);
        die();
    }
    
    $name = rmc_server_var($_POST, 'posname', '');
    $tag = rmc_server_var($_POST, 'postag', '');
    
    if($name==''){
        redirectMsg('blocks.php', __('Please provide a name and tag for this new position!','rmcommon'), RMMSG_ERROR);
        die();
    }

    if($tag=='')
        $tag = str_replace("-", "_", TextCleaner::getInstance()->sweetstring($name));
    
    if($edit){
        
        $id = rmc_server_var($_POST, 'id', '');
        if($id<=0)
            redirectMsg('blocks.php',__('You must specify a valid position ID!','rmcommon'), 1);
        
        $pos = new RMBlockPosition($id);
        if($pos->isNew())
            redirectMsg('blocks.php', __('Specified position does not exists!','rmcommon'), 1);
        
    } else {
        $pos = new RMBlockPosition();
    }
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $pos->setVar('name',$name);
    $pos->setVar('tag',$tag);
    $pos->setVar('active',1);
    
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_blocks_positions")." WHERE name='$name' OR tag='$tag'";
    if($edit) $sql .= " AND id_position<>$id";
    
    list($num) = $db->fetchRow($db->query($sql));

    if($num>0)
        redirectMsg('blocks.php', __('Already exists another position with same name or same tag!','rmcommon'), 1);
    
    if($pos->save())
        redirectMsg('blocks.php?from=positions',__('Database updated successfully!','rmcommon'));
    else
        redirectMsg('blocks.php', __('Errors ocurred while trying to save data','rmcommon').'<br />'.$pos->errors());
    
}

/**
* Change the current visibility status for a set of selected widgets
*/
function toggle_visibility($s){
    global $xoopsSecurity;
    
    if (!$xoopsSecurity->check()){
        redirectMsg('blocks.php', __('You are not allowed to do this action!','rmcommon'), 1);
        die();
    }
    
    $ids = rmc_server_var($_POST, 'ids', array());
    
    if(empty($ids) || !is_array($ids)){
        redirectMsg('blocks.php', __('Select at least a block!','rmcommon'), 1);
        die();
    }
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $db->queryF("UPDATE ".$db->prefix("mod_rmcommon_blocks")." SET visible=$s WHERE bid IN (".join(",",$ids).")");
    
    if ($db->error()==''){
        redirectMsg('blocks.php', __('Database updated successfully','rmcommon'), 0);
    } else {
        redirectMsg('blocks.php', __('Errors ocurred while trying to do this action','rmcommon').'<br />'.$db->error(), 1);
    }
    
}

function delete_positions(){
    global $xoopsSecurity;

    if (!$xoopsSecurity->check()){
        redirectMsg('blocks.php?from=positions', __('You are not allowed to do this action!','rmcommon'), 1);
        die();
    }
    
    $ids = rmc_server_var($_POST, 'ids', array());
    
    if(empty($ids) || !is_array($ids)){
        redirectMsg('blocks.php?from=positions', __('You must select at least one position!','rmcommon'), 1);
        die();
    }
    
    $errors = '';
    foreach ($ids as $id){
        $pos = new RMBlockPosition($id);
        
        $pos = RMEvents::get()->run_event('rmcommon.deleting.block.position',$pos);
        if (!$pos->delete()) $errors .= $pos->errors();
    }

    if ($errors!=''){
        redirectMsg('blocks.php?from=positions', __('There was some errors:','rmcommon').'<br />'.$error, 1);
    } else {
        redirectMsg('blocks.php?from=positions',__('Database updated successfully','rmcommon'), 0);
    }
}


$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch($action){
    case 'save_position':
        save_position();
        break;
    case 'hidden':
        toggle_visibility(0);
        break;
    case 'visible':
        toggle_visibility(1);
        break;
    case 'delete':
        delete_blocks();
        break;
    case 'deletepos':
        delete_positions();
        break;
    case 'upload-widget':
        upload_widget();
        break;
    default:
        show_rm_blocks();
        break;
}