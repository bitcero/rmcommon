<?php
// $Id: right_widgets.php 902 2012-01-03 07:09:16Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function rmc_available_mods(){
	global $available_mods, $xoopsSecurity;
	
	$ret['title'] = __('Available Modules','rmcommon');
	$ret['icon'] = RMCURL.'/images/modules.png';
    
    $limit = 10;
    $tpages = ceil(count($available_mods)/$limit);
    
    $nav = new RMPageNav(count($available_mods), $limit, 1, 3);
    $nav->target_url('javascript:;" onclick="load_page({PAGE_NUM});');
	
	ob_start();
    $i = 0;
?>
	<div class="rmc_widget_content_reduced rmc_modules_widget">
        <img id="img-load" src="images/loading.gif" style="display: none; margin: 15px auto;" />
        <div id="mods-widget-container">
		<?php foreach($available_mods as $mod): ?>
        <?php if($i==$limit) break; ?>
		<div class="<?php echo tpl_cycle("even,odd"); ?>">
			<a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>" class="rmc_mod_img" style="background: url(<?php echo XOOPS_URL; ?>/modules/<?php echo $mod->getInfo('dirname'); ?>/<?php echo $mod->getInfo('image'); ?>) no-repeat center;"><span>&nbsp;</span></a>
			<strong><a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>"><?php echo $mod->getInfo('name'); ?></a></strong>
			<span class="rmc_available_options">
				<a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>"><?php _e('Install','rmcommon'); ?></a> |
				<a href="javascript:;" onclick="show_module_info('<?php echo $mod->getInfo('dirname'); ?>');"><?php _e('More info','rmcommon'); ?></a>
			</span>
			<span class="rmc_mod_info" id="mod-<?php echo $mod->getInfo('dirname'); ?>">
				<?php _e('Version:','rmcommon'); ?> 
				<?php if($mod->getInfo('rmnative')): ?>
					<?php echo RMUtilities::format_version($mod->getInfo('rmversion')); ?>
				<?php else: ?>
					<?php echo $mod->getInfo('version'); ?>
				<?php endif; ?><br />
				<?php _e('Author:', 'rmcommon'); ?> <?php echo substr(strip_tags($mod->getInfo('author')), 0, 12); ?>
			</span>
		</div>
		<?php $i++; endforeach; ?>
        <?php $nav->display(false); ?>
        </div>
	</div>
    <input type="hidden" id="token" value="<?php echo $xoopsSecurity->createToken(); ?>" />
<?php
	$ret['content'] = ob_get_clean();
	return $ret;
	//print_r($available_mods);
	
}

/**
* Show the widget with blocks positions
*/
function rmc_blocks_new(){
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $blocks = RMBlocksFunctions::get_available_list($modules);
    
    // Get intalled modules
    $result = $db->query("SELECT * FROM ".$db->prefix("modules")." WHERE isactive=1 ORDER BY `name`");
    while($row = $db->fetchArray($result)){
        $modules[] = array('dir'=>$row['dirname'], 'name'=>$row['name']);
    }
    
    // Cargamos los grupos
    $sql = "SELECT groupid, name FROM " . $db->prefix("groups") . " ORDER BY name";
    $result = $db->query($sql);
    $groups = array();
    while ($row = $db->fetchArray($result)) {
        $groups[] = array('id' => $row['groupid'], 'name' => $row['name']);
    }
    
    $widget['title'] = 'Add Block';
    $widget['icon'] = '';
    ob_start();
    include RMTemplate::get()->get_template('widgets/rmc_aw_bknew.php');
    $widget['content'] = ob_get_clean();
    return $widget;
}

/**
* Add new position
*/
function rmc_blocks_addpos(){
    global $xoopsSecurity;
    
    $widget['title'] = 'Add Position';
    $widget['icon'] = '';
    
    $positions = RMBlocksFunctions::block_positions();
    
    ob_start();
    include RMTemplate::get()->get_template('widgets/rmc_aw_posnew.php','module','rmcommon');
    $widget['content'] = ob_get_clean();
    return $widget;
    
}