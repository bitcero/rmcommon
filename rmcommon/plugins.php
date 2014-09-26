<?php
// $Id: plugins.php 1026 2012-09-04 23:10:36Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$p = isset($_REQUEST['p']) ? $_REQUEST['p'] : '';
if ($p==''){
    define('RMCLOCATION','plugins');
}
include_once '../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/rmcommon/admin-loader.php';

function rm_reload_plugins(){
	
	$path = RMCPATH.'/plugins';
	$dir_list = XoopsLists::getDirListAsArray($path);

	$installed_plugins = array();
	
    foreach ($dir_list as $dir){
        
        if (!file_exists($path.'/'.$dir.'/'.strtolower($dir).'-plugin.php')) continue;
        
        $phand = new RMPlugin($dir); // PLugin handler
        
        if (!$phand->isNew() && $phand->getVar('status')){
		    
			$installed_plugins[] = $phand;
			
        }
	}
	
	$plugins = array();
    foreach($installed_plugins as $p){
		$plugins[] = $p->getVar('dir');
    }
    
    file_put_contents(XOOPS_CACHE_PATH.'/plgs.cnf', json_encode($plugins));
	
}

function show_rm_plugins(){
	global $rmTpl;

	$path = RMCPATH.'/plugins';
	$dir_list = XoopsLists::getDirListAsArray($path);

	$available_plugins = array();
	$installed_plugins = array();
	
    foreach ($dir_list as $dir){
        
        if (!file_exists($path.'/'.$dir.'/'.strtolower($dir).'-plugin.php')) continue;
        
        $phand = new RMPlugin($dir); // PLugin handler
        
        if ($phand->isNew()){
		    
            $phand->setVar('dir', $dir);	
			$available_plugins[] = $phand;
			
        } else {
		
			$installed_plugins[] = $phand;
			
        }
        
    }
    
    rm_reload_plugins();

	RMBreadCrumb::get()->add_crumb(__('Plugins Manager','rmcommon'));

	$rmTpl->assign('xoops_pagetitle', __('Plugins Manager','rmcommon'));
	
	//RMFunctions::create_toolbar();
	xoops_cp_header();
	
	include RMTemplate::get()->get_template('rmc-plugins.php', 'module', 'rmcommon');
	
	xoops_cp_footer();
	
}

/**
* This function install a plugin and all their functionallity
*/
function install_rm_plugin(){
	
	$name = rmc_server_var($_GET, 'plugin', '');
    if ($name==''){
        redirectMsg('plugins.php', __('You must specify a existing plugin', 'rmcommon'), 1);
        die();
    }
	
    $plugin = new RMPlugin($name);

    if (!$plugin->isNew()){
        redirectMsg('plugins.php', __('Specified plugin is installed already!', 'rmcommon'), 1);
        die();
    }
    
    if (!$plugin->load_from_dir($name)){
        redirectMsg('plugins.php', sprintf(__('%s is not a valid plugin!', 'rmcommon'), $name), 1);
        die();
    }
    
    if (!$plugin->save()){
		redirectMsg('plugins.php', __('Plugin could not be installed, please try again.','rmcommon'), 1);
		die();
    }
    
    if (!$plugin->on_install()){
        redirectMsg('plugins.php', __('The plugin has been inserted on database, but erros ocurred on this process.', 'rmcommon').'<br />'.$plugin->errors(), 1);
        die();
    }
    
    rm_reload_plugins();
    
    redirectMsg('plugins.php', __('Plugin installed succesfully!','rmcommon'), 0);
    
}

function uninstall_rm_plugin(){
    
    $name = rmc_server_var($_GET, 'plugin', '');
    if ($name==''){
        redirectMsg('plugins.php', __('You must specify a existing plugin', 'rmcommon'), 1);
        die();
    }
    
    $plugin = new RMPlugin($name);
    
    if ($plugin->isNew()){
        redirectMsg('plugins.php', __('Specified plugin is not installed yet!', 'rmcommon'), 1);
        die();
    }
    
    
    if (!$plugin->delete()){
        redirectMsg('plugins.php', __('Plugin could not be uninstalled, please try again.','rmcommon'), 1);
        die();
    }
    
    if (!$plugin->on_uninstall()){
        redirectMsg('plugins.php', __('The plugin has been deleted from database, but erros ocurred on this process.', 'rmcommon').'<br />'.$plugin->errors(), 1);
        die();
    }
    
    rm_reload_plugins();
    
    redirectMsg('plugins.php', __('Plugin uninstalled succesfully!','rmcommon'), 0);
    
}

function update_rm_plugin(){
    
    $name = rmc_server_var($_GET, 'plugin', '');
    if ($name==''){
        redirectMsg('plugins.php', __('You must specify a existing plugin', 'rmcommon'), 1);
        die();
    }
    
    $plugin = new RMPlugin($name);
    
    if ($plugin->isNew()){
        redirectMsg('plugins.php', __('Specified plugin is not installed yet!', 'rmcommon'), 1);
        die();
    }
    
    if (!$plugin->save()){
        redirectMsg('plugins.php', __('Plugin could not be updated, please try again.','rmcommon'), 1);
        die();
    }
    
    if (!$plugin->on_update()){
        redirectMsg('plugins.php', __('The database has been updated, but erros ocurred on this process.', 'rmcommon').'<br />'.$plugin->errors(), 1);
        die();
    }
    
    rm_reload_plugins();
    
    redirectMsg('plugins.php', __('Plugin updated succesfully!','rmcommon'), 0);
    
}

function activate_rm_plugin($q){
    
    $name = rmc_server_var($_GET, 'plugin', '');
    if ($name==''){
        redirectMsg('plugins.php', __('You must specify a existing plugin', 'rmcommon'), 1);
        die();
    }
    
    $plugin = new RMPlugin($name);
    
    if ($plugin->isNew()){
        redirectMsg('plugins.php', __('Specified plugin is not installed yet!', 'rmcommon'), 1);
        die();
    }
    
    $plugin->setVar('status', $q);
    
    if (!$plugin->save()){
        redirectMsg('plugins.php', __('Plugin could not be updated, please try again.','rmcommon'), 1);
        die();
    }
    
    if (!$plugin->on_activate()){
        redirectMsg('plugins.php', __('The database has been updated, but erros ocurred on this process.', 'rmcommon').'<br />'.$plugin->errors(), 1);
        die();
    }
    
    rm_reload_plugins();
    
    redirectMsg('plugins.php', __('Plugin status changed succesfully!','rmcommon'), 0);
    
}

function configure_rm_plugin(){
    global $rmTpl;

    $name = rmc_server_var($_GET, 'plugin', '');
    if ($name==''){
        redirectMsg('plugins.php', __('You must specify a existing plugin', 'rmcommon'), 1);
        die();
    }
    
    $plugin = new RMPlugin($name);
    
    if ($plugin->isNew()){
        redirectMsg('plugins.php', __('Specified plugin is not installed yet!', 'rmcommon'), 1);
        die();
    }
    
    if (!$plugin->getVar('status')){
        redirectMsg('plugins.php', __('Specified plugin is not active!', 'rmcommon'), 1);
        die();
    }
    
    $cuSettings = RMSettings::cu_settings();
    $settings = RMFunctions::plugin_settings($name, true);

    $form = new RMForm(sprintf(__('%s configuration','rmcommon'), $plugin->getVar('name')),'frmconfig','plugins.php');
    $form->addElement(new RMFormHidden('plugin', $plugin->getVar('dir')));
    $form->addElement(new RMFormHidden('action', 'savesettings'));

    foreach ($plugin->options() as $config => $option){

        if (isset($settings[$config])){
            $option['value'] = $settings[$config];
        }

        if (isset($option['separator']) && !empty($option['separator'])){
            $form->addElement(new RMFormSubTitle($option['separator']['title'], 1, '', $option['separator']['desc']));
            continue;
        }

        switch ($option['fieldtype']){
            case 'checkbox_groups':
            case 'group_multi':
                $ele = new RMFormGroups($option['caption'], 'conf_' . $config, 1, 1, 3, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'radio_groups':
                $ele = new RMFormGroups($option['caption'], 'conf_' . $config, 0, 1, 3, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'group':
            case 'select_groups':
                $ele = new RMFormGroups($option['caption'], 'conf_' . $config, 0, 0, 3, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'select_groups_multi':
                $ele = new RMFormGroups($option['caption'], 'conf_' . $config, 1, 0, 3, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'editor':
                if ($cuSettings->editor_type=='tiny'){
                    $tiny = TinyEditor::getInstance();
                    $tiny->add_config('elements','conf_' . $config);
                }
                $ele = new RMFormEditor($option['caption'], 'conf_' . $config, is_numeric($option['size']) ? '90%' : $option['size'], '300px', $option['value'], '', 1, array('op'));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'theme':
            case 'select_theme':
                $ele = new RMFormTheme($option['caption'], 'conf_' . $config, 0, 0, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);            
                break;
            case 'theme_multi':
            case 'select_theme_multi':
                $ele = new RMFormTheme($option['caption'], 'conf_' . $config, 0, 1, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);            
                break;
            case 'checkbox_theme':
                $ele = new RMFormTheme($option['caption'], 'conf_' . $config, 1, 1, $option['value'], 4);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);            
                break;
            case 'select_theme_admin':
                $ele = new RMFormTheme($option['caption'], 'conf_' . $config, 0, 0, $option['value'], 3, 'GUI');
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'yesno':
                $ele = new RMFormYesNo($option['caption'], 'conf_' . $config, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'email':
                $ele = new RMFormText($option['caption'], 'conf_' . $config, $option['size']>0 ? $option['size'] : 50, null, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele, false, 'email');
                break;
            case 'select':
                $ele = new RMFormSelect($option['caption'], 'conf_' . $config);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                
                foreach ($option['options'] as $op => $opvalue){
                    $ele->addOption($opvalue, $op, $opvalue==$option['value'] ? 1 : 0);
                }
                $form->addElement($ele);
                break;
            case 'select_multi':
                $ele = new RMFormSelect($option['caption'], 'conf_' . $config.'[]', 1, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                
                foreach ($option['options'] as $op => $opvalue){
                    $ele->addOption($opvalue, $op);
                }
                $form->addElement($ele);
                break;
            case 'language':
            case 'select_language':
                $ele = new RMFormLanguageField($option['caption'], 'conf_' . $config, 0, 0, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);            
                break;
            case 'select_language_multi':
                $ele = new RMFormLanguageField($option['caption'], 'conf_' . $config, 1, 0, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);            
                break;
            case 'checkbox_language':
                $ele = new RMFormLanguageField($option['caption'], 'conf_' . $config, 1, 1, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);            
                break;
            case 'startpage':
            case 'select_modules':
                $ele = new RMFormModules($option['caption'], 'conf_' . $config, 0, 0, $option['value'], 3);
                $ele->setInserted(array('--'=>__('None','rmcommon')));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'select_modules_multi':
                $ele = new RMFormModules($option['caption'], 'conf_' . $config, 1, 0, $option['value'], 3);
                $ele->setInserted(array('--'=>__('None','rmcommon')));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'checkbox_modules':
                $ele = new RMFormModules($option['caption'], 'conf_' . $config, 1, 1, $option['value'], 3);
                $ele->setInserted(array('--'=>__('None','rmcommon')));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'radio_modules':
                $ele = new RMFormModules($option['caption'], 'conf_' . $config, 0, 1, $option['value'], 3);
                $ele->setInserted(array('--'=>__('None','rmcommon')));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'timezone':
            case 'select_timezone':
                $ele = new RMFormTimeZoneField($option['caption'], 'conf_' . $config, 0, 0, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'select_timezone_multi':
                $ele = new RMFormTimeZoneField($option['caption'], 'conf_' . $config, 0, 1, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'checkbox_timezone':
                $ele = new RMFormTimeZoneField($option['caption'], 'conf_' . $config, 1, 1, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'radio_timezone':
                $ele = new RMFormTimeZoneField($option['caption'], 'conf_' . $config, 1, 0, $option['value'], 3);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'tplset':
                $ele = new RMFormSelect($option['caption'], 'conf_' . $config);
                $tplset_handler =& exm_gethandler('tplset');
                $tplsetlist =& $tplset_handler->getList();
                asort($tplsetlist);
                foreach ($tplsetlist as $key => $name) {
                    $ele->addOption($key, $name, $option['value']==$key ? 1 : 0);
                }
                $form->addElement($ele);
                break;
            case 'textarea':
                $ele = new RMFormTextArea($option['caption'], 'conf_' . $config,  5, $option['size']>0 ? $option['size'] : 50, $option['valuetype']=='array' ? TextCleaner::getInstance()->specialchars(implode('|', $option['value'])) : TextCleaner::getInstance()->specialchars($option['value']));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'module_cache':
                $ele = new RMFormCacheModuleField($option['caption'], 'conf_' . $config, $option['value']);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'user_select':
                $ele = new RMFormUser($option['caption'], 'conf_' . $config, $form->getName(), $option['value'], 'select', $limit='300', '');
                $ele->setOnPage("document.forms[0].op.value='config';");
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele);
                break;
            case 'radio':
                $ele = new RMFormRadio($option['caption'], 'conf_'.$config, 1);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                
                foreach ($option['options'] as $op => $opvalue){
                    $ele->addOption($op, $opvalue, $opvalue==$option['value'] ? 1 : 0);
                }
                $form->addElement($ele);
                break;
            case 'font_select':
                $ele = new RMFormSelect($option['caption'], 'conf_'.$config, 0, array($option['value']));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $fonts = EXMLists::getFileListAsArray(ABSPATH.'/rmcommon/fonts/');
                foreach ($fonts as $font){
                    $info = pathinfo(ABSPATH.'/rmcommon/fonts/'.$font); 
                    if (strtoupper($info['extension'])!='TTF') continue;
                    $ele->addOption($font, $font);
                }
                $form->addElement($ele);
                break;
            case 'select_editor':
                $ele = new RMFormSelect($option['caption'], 'conf_'.$config, 0, array($option['value']));
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $ele->addOption('tiny', __('Visual Editor','rmcommon'));
                $ele->addOption('code', __('EXMCode Editor','rmcommon'));
                $ele->addOption('textarea', __('Simple Editor','rmcommon'));
                $ele->addOption('html', __('HTML Editor','rmcommon'));
                $form->addElement($ele);
                break;
            case 'textbox':
            case 'password':
            default:
                $ele = new RMFormText($option['caption'], 'conf_' . $config, isset($option['size']) ? $option['size'] : 50, null, $option['valuetype']=='array' ? implode('|', $option['value']) : $option['value'], $option['fieldtype']=='password'? 1: 0);
                if ($option['desc']!='') $ele->setDescription($option['desc']);
                $form->addElement($ele, false, $option['valuetype']=='int' || $option['valuetype']=='float' ? 'num' : '');
                break;
        }



    }
    
    $ele = new RMFormButtonGroup();
    $ele->addButton('', __('Save Settings','rmcommon'), 'submit');
    $ele->addButton('',__('Cancel','rmcommon'), 'button','onclick="history.go(-1);"');
    
    $form->addElement($ele);
    
    // Other components can add items to database
    $form = RMEvents::get()->run_event("rmcommon.settings.form", $form, $plugin);
    
    //RMFunctions::create_toolbar();

	RMBreadCrumb::get()->add_crumb(__('Plugins Manager','rmcommon'), 'plugins.php');
	RMBreadCrumb::get()->add_crumb(__('Configure Plugin ','rmcommon'));

	$rmTpl->assign('xoops_pagetitle', __('Configure Plugin ','rmcommon'));

    xoops_cp_header();
    $form->display();
    xoops_cp_footer();
    
}

function save_settings_rm_plugin(){
    global $xoopsSecurity;
    
    $name = rmc_server_var($_POST, 'plugin', '');
    if ($name==''){
        redirectMsg('plugins.php', __('You must specify a existing plugin', 'rmcommon'), 1);
        die();
    }
    
    $plugin = new RMPlugin($name);
    
    if ($plugin->isNew()){
        redirectMsg('plugins.php', __('Specified plugin is not installed yet!', 'rmcommon'), 1);
        die();
    }
    
    if (!$plugin->getVar('status')){
        redirectMsg('plugins.php', __('Specified plugin is not active!', 'rmcommon'), 1);
        die();
    }
    
    if (!$xoopsSecurity->check()){
        redirectMsg('plugins.php?action=configure&plugin='.$name, __('Session token expired!','rmcommon'), 1);
        die();
    }
    
    $options = $plugin->options();
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $confs = array();
    foreach ($options as $k => $option){
        if (!isset($_POST['conf_'.$k])) continue;
        $value = $_POST['conf_'.$k];
        $option['value'] = is_array($value) ? serialize($value) : $value;
        
        $db->queryF("UPDATE ".$db->prefix("mod_rmcommon_settings")." SET value='$option[value]' WHERE element='$name' AND type='plugin' AND name='$k'");
        
    }
    
    $events = RMEvents::get();
    /**
    * Evnet params
    * 1: Options as array
    * 2: PLugin dir name
    * 3: Plugin object
    */
    $events->run_event('rmcommon.save.plugin.settings', $options, $plugin->getVar('dir'), $plugin);
    
    redirectMsg('plugins.php', __('Settings updated!','rmcommon'), 0);
    
}

/**
* This function allows to plugins to show their own options
*/
function main_rm_plugin($dir){
    
    $path = RMCPATH.'/plugins';
    
    if (!file_exists($path.'/'.$dir.'/'.strtolower($dir).'-plugin.php')){
        header("location: plugins.php"); die();
    }

    $plugin = new RMPlugin($dir);
    if ($plugin->isNew()){
        header("location: plugins.php"); die();
    }
    
    if (!$plugin->get_info('hasmain')){
        header("location: plugins.php"); die();
    }
    
    $class = ucfirst($dir).'CUPlugin';
    $plugin = new $class();
    if (!method_exists($plugin, 'main')){
        header("location: plugins.php"); die();
    }

    load_plugin_locale($dir);
    
    $plugin->main();
    
}

// Allow to plugins to take control over this section and show their own options
RMEvents::get()->run_event('rmcommon.plugins.check.actions');

$dir = rmc_server_var($_REQUEST, 'p', '');
if ($dir!=''){
    main_rm_plugin($dir);
    die();
}

$action = rmc_server_var($_REQUEST,'action','');

switch($action){
	case 'install':
		install_rm_plugin();
		break;
    case 'uninstall':
        uninstall_rm_plugin();
        break;
    case 'update':
        update_rm_plugin();
        break;
    case 'enable':
        activate_rm_plugin(1);
        break;
    case 'disable':
        activate_rm_plugin(0);
        break;
    case 'configure':
        configure_rm_plugin();
        break;
    case 'savesettings':
        save_settings_rm_plugin();
        break;
	default:
		show_rm_plugins();
		break;
}
