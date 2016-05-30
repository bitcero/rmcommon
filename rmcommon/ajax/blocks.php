<?php
// $Id: blocks.php 1037 2012-09-07 21:19:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../../mainfile.php';

// Deactivate the logger
error_reporting(0);
$xoopsLogger->activated = false;

function response($message, $data = array(), $error=0, $token=0){
    global $xoopsSecurity;

    $response = array(
        'message'       => $message,
        'data'          => $data,
        'error'         => $error,
        'token'         => $token==1 ? $xoopsSecurity->createToken() : '',
    );

    echo json_encode($response);
    die();

}

// Check Security settings
if(!$xoopsSecurity->checkReferer(1))
    response(__('Operation not allowed!','rmcommon'), array(), 1, 0);

/**
* This function allows to insert a new block in database
*/
function insert_block(){
    global $xoopsSecurity;

    $mod = RMHttpRequest::post( 'module', 'string', '' );
    $id = RMHttpRequest::post( 'block', 'string', '' );
    $token = RMHttpRequest::post( 'XOOPS_TOKEN_REQUEST', 'string', '' );
    $canvas = RMHttpRequest::post( 'canvas', 'integer', 0 );

    if (!$xoopsSecurity->check())
        response(__('Sorry, you are not allowed to view this page','rmcommon'), array(), 1, 0);

    if($mod=='' || $id=='')
        response(__('The block specified seems to be invalid. Please try again.','rmcommon'), array(), 1, 0);

    $module = RMModules::load_module($mod);
    if(!$module)
        response(__('The specified module does not exists!','rmcommon'), array(), 1, 0);

    $module->loadInfoAsVar($mod);
    $blocks = $module->getInfo('blocks');
    $ms = $module->name().'<br />';
    $found = false;
    foreach ($blocks as $bk) {
        $str = isset($bk['show_func']) ? $bk['show_func'] : '';
        $str .= isset($bk['edit_func']) ? $bk['edit_func'] : '';
        $str .= isset($bk['dir']) ? $bk['dir'] : $mod;
        $idb = md5($str);
        if ($idb==$id) {
            $found = true;
            break;
        }
    }

    if(!$found)
        response(__('The specified block does not exists, please verify your selection.','rmcommon'), array(), 1, 1);

    $block = new RMInternalBlock();

    if ($canvas<=0) {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        // Get a default side
        $sql = "SELECT id_position FROM ".$db->prefix("mod_rmcommon_blocks_positions")." ORDER BY id_position LIMIT 0, 1";
        $result = $db->query($sql);
        if ($result)
            list($canvas) = $db->fetchRow($result);
        else
            $canvas = '';
    }

    $block->setReadGroups(array(0));
    $block->setVar('name', $bk['name']);
    $block->setVar('element', $mod);
    $block->setVar('element_type', $bk['type']=='' ? 'module' : ($bk['type'] == 'theme' || $bk['type'] == 'plugin' ? $bk['type'] : 'module'));
    $block->setVar('canvas', $canvas);
    $block->setVar('visible', 0);
    $block->setVar('type', $bk['type']);
    $block->setVar('isactive', 1);
    $block->setVar('dirname', isset($bk['dirname']) ? $bk['dirname'] : $mod);
    $block->setVar('file', $bk['file']);
    $block->setVar('show_func', $bk['show_func']);
    $block->setVar('edit_func', $bk['edit_func']);
    $block->setVar('description', $bk['description']);
    $block->setVar('widget', $id);
    $block->setVar('options', is_array($bk['options']) ? serialize($bk['options']) : serialize(explode("|", $bk['options'])));
    $block->setVar('template', $bk['template']);
    $block->setVar('bcachetime', 0);
    $block->sections(array(0));

    if(!$block->save())
        response(__('Block could not be created. Please try again!', 'rmcommon'), array('error' => $block->errors()), 1, 1);

    RMEvents::get()->run_event('rmcommon.block.added', $block);

    $pos = RMBlocksFunctions::block_positions();

    $ret = array(
        'id'            => $block->id(),
        'title'         => $block->getVar('name'),
        'module'        => $block->getVar('element'),
        'description'   => $block->getVar('description'),
        'canvas'        => $pos[$canvas],
        'weight'        => $block->getVar('weight'),
        'visible'       => $block->getVar('visible'),
    );

    response(sprintf(__('Block "%s" was added successfully! Please configure it.','rmcommon'), $block->getVar('name')), array('block' => $ret), 0, 1);
    die();
}

/**
* Return the form to configure blocks
*/
function configure_block(){
    global $xoopsSecurity;

    if (!$xoopsSecurity->check())
        response(__('Sorry, you are not allowed to view this page','rmcommon'), array(), 1, 0);

    $id = rmc_server_var($_POST, 'block', 0);

    if($id<=0)
        response(__('The block that you specified seems to be invalid. Please try again', 'rmcommon'), array(), 1, 1);

    $block = new RMInternalBlock($id);
    if($block->isNew())
        response(__('Specified block does not exists!. Please try again', 'rmcommon'), array(), 1, 1);

    $positions = RMBlocksFunctions::block_positions( 1 );
    $form = new RMForm('','','');
    $canvas = new RMFormModules([
        'caption' => '',
        'name' => 'bk_mod',
        'id' => 'bk_mod',
        'type' => 'checkbox',
        'multiple' => null,
        'selected' => $block->sections(),
        'subpages' => null,
        'dirname' => false,
        'selectedSubs' => $block->subpages(),
        'dirnames' => false
    ]);
    // Groups
    $groups = new RMFormGroups('', 'bk_groups', true, 1, 3, $block->readGroups());

    $block_options = $block->getOptions();

    ob_start();
    include RMTemplate::get()->get_template('rmc-block-form.php', 'module', 'rmcommon');
    $form = ob_get_clean();

    $ret = array(
        'id'=>$block->id(),
        'content'=>$form,
    );
    response(sprintf(__('Configuration form for block "%s" was loaded successfully!','rmcommon'), $block->getVar('name')), $ret, 0, 1);

    die();

}

function save_block_config(){
    global $xoopsSecurity;

    foreach ($_POST as $k => $v) {
        $$k = $v;
    }

    if(!$xoopsSecurity->check($XOOPS_TOKEN_REQUEST))
        response(__('Session token expired. Please try again.','rmcommon'), array(), 1, 0);

    if($bid<=0)
        response(__('You must provide a block ID!','rmcommon'), array(), 1, 1);

    $block = new RMInternalBlock($bid);
    if($block->isNew())
        response(__('Specified block does not exists!','rmcommon'), array(), 1, 1);

    if(isset($options)) $block->setVar('options', serialize($options));
    $block->setVar('name', $bk_name);
    $block->setVar('canvas', $bk_pos);
    $block->setVar('weight', $bk_weight);
    $block->setVar('visible', $bk_visible);
    $block->setVar('bcachetime', $bk_cache);
    if (isset($bk_content)) {
        $block->setVar('content', $bk_content);
        $block->setVar('content_type', $bk_ctype);
    }

    // Set modules
    $block->sections($bk_mod);
    // Set Groups
    $block->setReadGroups($bk_groups);

    if(!$block->save())
        response(sprintf(__('Settings for the block "%s" could not be saved!','rmcommon'), $block->getVar('name')), array('error' => $block->errors()), 1, 1);

    RMEvents::get()->run_event( 'rmcommon.block.saved', $block );

    $ret = array(
        'id'            => $block->id(),
        'canvas'        => $block->getVar('canvas'),
        'visible'       => $block->getVar('visible'),
        'weight'        => $block->getVar('weight'),
    );

    response(sprintf(__('Settings for block "%s" were saved successfully!','rmcommon'), $block->getVar('name')), $ret, 0, 1);

    die();

}

function save_block_position(){
    global $xoopsSecurity;

    if(!$xoopsSecurity->check())
        response(__('Session token expired. Please try again.','rmcommon'), array(), 1, 0);

    $id = rmc_server_var($_POST, 'id', 0);
    $name = rmc_server_var($_POST, 'name', '');
    $tag = rmc_server_var($_POST, 'tag', '');
    $active = rmc_server_var($_POST, 'active', 1);

    if($id<=0)
        response(__('Specified position is not valid!','rmcommon'), array(), 1, 1);

    if($name==''||$tag=='')
        response(__('You must fill name and tag input fields!','rmcommon'), array(), 1, 1);

    $pos = new RMBlockPosition($id);
    if($pos->isNew())
        response(__('Specified blocks position does not exists!','rmcommon'), array(), 1, 1);

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_blocks_positions")." WHERE (name='$name' OR tag='$tag') AND id_position<>$id";

    list($num) = $db->fetchRow($db->query($sql));

    if($num>0)
        response(__('Already exists another blocks position with same name or tag!','rmcommon'), array(), 1, 1);

    $pos->setVar('name', $name);
    $pos->setVar('tag', $tag);
    $pos->setVar('active', $active);

    if($pos->save())
        response(sprintf(__('Position "%s" was saved successfylly!','rmcommon'), $pos->getVar('name')), array(), 0, 1);
    else
        response(sprintf(__('Position "%s" could not be saved!','rmcommon'), $pos->getVar('name')), array('error' => $pos->errors()), 1, 1);

}

/**
* Save blocks orders
*/
function save_block_order(){
    global $xoopsSecurity;

    $blocks = rmc_server_var($_POST, 'blocks', '');
    $pos_id = rmc_server_var($_POST, 'position', '');

    if(!$xoopsSecurity->check(false, false))
        response(__('Blocks order could not be saved!','rmcommon'), array('error' => __('Session token expired!','rmcommon')), 1, 0);

    if($pos_id<=0)
        response(__('Position not specified!','rmcommon'), array('error' => ''), 1, 0);

    $blocks = json_decode($blocks, true);

    if(empty($blocks))
        response('', array('position' => $pos_id), 0, 0);

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $ids = array();
    $sql = "UPDATE ".$db->prefix("mod_rmcommon_blocks")." SET canvas = $pos_id, weight = CASE bid\n";
    foreach ($blocks as $i => $block) {
        $sql .= "WHEN $block[id] THEN $i\n";
        $ids[] = $block['id'];
    }
    $sql .= "END\n WHERE bid IN (".implode(",",$ids).");";

    $pos = new RMBlockPosition($pos_id);

    if($db->queryF($sql))
        response(sprintf(__('Blocks order for position "%s" saved successfully!','rmcommon'), $pos->getVar('name')), array('position'=>$pos_id), 0, 0);
    else
        response(
            sprintf(__('Blocks order for position "%s" could not be saved!','rmcommon'), $pos->getVar('name')),
            array(
                'position'  => $pos_id,
                'error'     => $db->error()
            ),
            1, 0
        );

}

function change_block_visibility($visible){
    global $xoopsSecurity;

    $block_id = rmc_server_var($_POST, 'id', 0);

    if(!$xoopsSecurity->check(false, false))
        response(__('Block visibility could not be saved!','rmcommon'), array('error' => __('Session token expired!','rmcommon')), 1, 0);

    if($block_id<=0)
        response(__('Block identifier not specified!','rmcommon'), array('error' => ''), 1, 0);

    $block = new RMInternalBlock($block_id);
    if($block->isNew())
        response(__('Specified block does not exists!','rmcommon'), array(), 1, 0);

    $block->setVar('visible', $visible);

    if($block->save())
        response(sprintf(__('The visibility of the block %s was changed successfully!','rmcommon'), $block->getVar('name')), array('visible' => $visible), 0, 0);
    else
        response(
            sprintf(__('Visibility for block %s could not be changed!','rmcommon'), $block->getVar('name')),
            array(
                'error'     => $block->error()
            ),
            1, 0
        );

}

/**
 * Delete a set of selected widgets
 */
function delete_block(){

    global $xoopsSecurity;

    if (!$xoopsSecurity->check())
        response(__('Session token expired!','rmcommon'), array(), 1, 0);

    $block_id = rmc_server_var($_POST, 'id', 0);

    if($block_id<=0)
        response(__('No block has been specified!','rmcommon'), array(), 1, 1);

    $block = new RMInternalBlock($block_id);
    if($block->isNew())
        response(__('Specified block does not exists!','rmcommon'), array(), 1, 1);

    if (!$block->delete())
        response(
            sprintf(__('The block "%s" could not be deleted!', 'rmcommon'), $block->getVar('name')),
            array('error' => $block->errors()),
            1, 1
        );
    else
        response(
            sprintf(__('The block "%s" was deleted successfully!','rmcommon'), $block->getVar('name')),
            array(), 0, 1
        );

}

$action = rmc_server_var($_POST, 'action', '');

switch ($action) {
    case 'insert':
        insert_block();
        break;
    case 'settings':
        configure_block();
        break;
    case 'saveconfig':
        save_block_config();
        break;
    case 'savepos':
        save_block_position();
        break;
    case 'save-orders':
        save_block_order();
        break;
    case 'show-block':
        change_block_visibility(1);
        break;
    case 'hide-block':
        change_block_visibility(0);
        break;
    case 'delete-block':
        delete_block();
        break;
}
