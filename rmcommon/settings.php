<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include_once '../../include/cp_header.php';
//require_once XOOPS_ROOT_PATH . '/modules/rmcommon/admin-loader.php';
define('RMCLOCATION','cu-settings');

class AjaxResponse{
    use RMModuleAjax;
}

/**
 * Shows all modules that can be configured with this tool
 */
function show_configurable_items(){
    global $xoopsModule, $cuSettings, $xoopsModuleConfig, $rmTpl;

    $rmTpl->add_style("settings.css", 'rmcommon', array('footer' => 1));
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "SELECT mid FROM " . $db->prefix("modules") . " WHERE dirname='system' OR hasconfig=1 ORDER BY name ASC";
    $result = $db->query( $sql );

    $mh = xoops_getHandler('module' );
    $modules = array();

    while ( $row = $db->fetchArray( $result ) ) {

        $mod = $mh->get( $row['mid'] );
        $modules[] = array(
            'id' => $mod->mid(),
            'name' => $mod->getVar('name'),
            'logo' => XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('image')
        );

    }

    $rmTpl->header();
    include $rmTpl->get_template("rmc-settings.php", 'module', 'rmcommon');
    $rmTpl->footer();

}

/**
 * Show the preferences for a specific module
 */
function show_module_preferences(){
    global $rmTpl, $xoopsSecurity, $cuIcons;

    $quick = RMHttpRequest::get( 'popup', 'integer', 0 );
    $token = RMHttpRequest::get( 'CUTOKEN_REQUEST', 'string', '' );
    $ajax = new AjaxResponse();

    $is_popup = $quick == 1 && $token != '';

    if ($is_popup) {
        $ajax->prepare();
        if ( !$xoopsSecurity->validateToken(false, true, 'CUTOKEN') )
            $ajax->response(
                __('Unauthorized action', 'rmcommon'), 1, 0, array('reload' => true)
            );
    }

    $mod = RMHttpRequest::get( 'mod', 'integer', 0 );
    if ( $mod <= 0 )
        RMUris::redirect_with_message(
            __('You have not specified a module!', 'rmcommon'), 'settings.php', RMMSG_WARN
        );

    $mh = xoops_getHandler('module' );
    $module = $mh->get( $mod );

    if ( $module->isNew() )
        RMUris::redirect_with_message(
            __('You have not specified a valid module ID!', 'rmcommon'), 'settings.php', RMMSG_ERROR
        );

    /**
     * Verify if module is rmcommon native or not
     */
    if ( $module->getInfo('rmnative') != 1 )
        RMUris::redirect_with_message(
            __('This module can not be configured with Common Utilities', 'rmcommon'),
            XOOPS_URL. '/modules/system/admin.php?fct=preferences&op=showmod&mod=' . $mod,
            RMMSG_INFO
        );

    /*
    Cargamos los valores y los datos para formar los campos
    */
    $values = RMSettings::module_settings( $module->getVar('dirname') );
    $configs = $module->getInfo('config');
    $settings_categories = $module->getInfo('categories');
    $categories = array();

    if ( empty( $settings_categories ) )
        $categories = array(
            'all' => array(
                'caption' => __( 'Preferences', 'rmcommon' )
            )
        );
    else{
        foreach ($settings_categories as $category => $caption) {
            // Verify if category has been provided in array format
            if(is_array($caption) && array_key_exists('caption', $caption)){
                $categories[$category] = $caption;
            } else {
                $categories[$category] = array('caption' => $caption);
            }
        }
    }

    unset($settings_categories);

    $fields = array(); // Container for all fields and values
    foreach ($configs as $option) {

        $name = ucfirst($module->getVar('dirname')).'['.$option['name'].']';

        $field = new stdClass();
        $field->name = $name;
        $field->id = $option['name'];
        $field->value = isset( $values->{$option['name']} ) ? $values->{$option['name']} : $option['default'];
        $field->caption = defined($option['title']) ? constant( $option['title'] ) : $option['title'];
        $field->description = defined($option['description']) ? constant( $option['description'] ) : $option['description'];
        $field->field = $option['formtype'];
        $field->type = $option['valuetype'];
        $field->options = isset($option['options']) ? $option['options'] : null;

        $category = isset($option['category']) ? $option['category'] : 'all';

        if ( isset( $categories[$category] ) )
            $categories[$category]['fields'][$field->id] = $field;
        else{
            if ( !isset( $categories['all'] ) )
                $categories['all'] = array('caption' => __('Preferences', 'rmcommon'));

            $categories['all']['fields'][$field->id] = $field;
        }

    }

    $categories = RMEvents::get()->trigger( 'rmcommon.settings.fields', $categories, $module );

    $rmTpl->add_style('settings.css', 'rmcommon', array('footer' => 1));

    /* Breadcrumb */
    $bc = RMBreadCrumb::get();
    if ($module->getVar('hasadmin'))
        $bc->add_crumb( $module->getVar('name'), XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getVar('adminindex') );
    else
        $bc->add_crumb( $module->getVar('name'), '' );

    $bc->add_crumb( __('Settings', 'rmcommon') );

    $rmTpl->assign( 'xoops_pagetitle', sprintf( __('%s Settings', 'rmcommon'), $module->getVar('name') ) );

    if (!$is_popup) {

        $rmTpl->header();
        require $rmTpl->get_template('rmc-settings-form.php', 'module', 'rmcommon');
        $rmTpl->footer();

    } else {

        ob_start();
        require $rmTpl->get_template('rmc-settings-form.php', 'module', 'rmcommon');
        $response = ob_get_clean();

        $ajax->ajax_response(
            sprintf( __('%s Settings', 'rmcommon'), $module->getVar('name') ),
            0, 1, array(
                'content' => $response,
                'width' => 'large',
                'closeButton' => 1,
                'id' => 'cu-settings-form',
                'color' => 'primary',
                'solid' => true
            )
        );

    }

}

/**
 * Save module settings
 */
function save_module_settings(){
    global $xoopsSecurity, $xoopsDB, $common;

    $mod = RMHttpRequest::post( 'mod', 'string', '' );
    $via_ajax = RMHttpRequest::post( 'via_ajax', 'integer', 0 );

    if ($via_ajax) {
        $ajax = new AjaxResponse();
        $common->ajax()->prepare();
    }

    if ( $mod == '' )
        RMUris::redirect_with_message( __('A module has not been specified!', 'rmcommon' ), 'settings.php', RMMSG_ERROR );

    //echo RMHttpRequest::request('CUTOKEN_REQUEST', 'string', '') . ' ' . print_r($_SESSION['CUTOKEN_SESSION'], true); die();

    if ( !$xoopsSecurity->check(true, false, $via_ajax ? 'CUTOKEN' : 'XOOPS_TOKEN') ) {
        if ($via_ajax)
            $ajax->ajax_response(
                __('Session token expired. Please try again.', 'rmcommon'), 1, 0
            );
        else
            RMUris::redirect_with_message( __('Session token expired. Please try again.', 'rmcommon'), 'settings.php', RMMSG_WARN );
    }

    $module = RMModules::load_module( $mod );
    if (!$module) {
        if ($via_ajax)
            $ajax->ajax_response(
                __('The specified module does not exists.', 'rmcommon'), 1, 1
            );
        else
            RMUris::redirect_with_message( __('The specified module does not exists.', 'rmcommon'), 'settings.php', RMMSG_ERROR );
    }

    $current_settings = (array) RMSettings::module_settings( $module->getVar('dirname') );
    $new_settings = RMHttpRequest::post( ucfirst( $module->getVar('dirname') ), 'array', array() );

    $configs =& $module->getInfo( 'config' );
    $fields = array(); // Container for all fields and values
    foreach ($configs as $option) {

        $id = $option['name'];

        $field = new stdClass();
        $field->id = $id;
        $field->value = isset( $values->$id) ? $values->$id : $option['default'];
        $field->caption = defined($option['title']) ? constant( $option['title'] ) : $option['title'];
        $field->description = defined($option['description']) ? constant( $option['description'] ) : $option['description'];
        $field->field = $option['formtype'];
        $field->type = $option['valuetype'];
        $field->options = isset($option['options']) ? $option['options'] : null;

        $category = isset($option['category']) ? $option['category'] : 'all';
        $fields[$id] = $field;

    }


    /**
     * This keys already exists in database
     */
    $to_save = array_intersect_key( $new_settings, $current_settings );
    /**
     * This settings will be added to database beacause don't exists in table
     */
    $to_add = array_diff_key( $new_settings, $current_settings );
    /**
     * This keys has been removed from xoops_version.php file and then
     * must be removed from table
     */
    $to_delete = array_diff_key( $current_settings, $new_settings );

    $errors = ''; // Errors ocurred while saving
    /**
     * First for all, remove unused items
     */
    $keys = array_keys( $to_delete );
    if ( !empty( $keys ) ) {
        $sql = "DELETE FROM " . $xoopsDB->prefix("config") . " WHERE conf_modid = " . $module->mid() . " AND (conf_name = '" . implode("' OR conf_name='", $keys) . "')";
        if ( !$xoopsDB->queryF( $sql ) )
            $errors .= $xoopsDB->error() . '<br>';
    }

    /**
     * Save existing items
     */
    if ( !empty( $to_save ) ) {

        foreach ($to_save as $name => $value) {

            $item = new Rmcommon_Config_Item( $name, $module->mid() );
            if ( isset( $fields[$name] ) ) {
                $item->setVar( 'conf_valuetype', $fields[$name]->type );
                $item->setVar( 'conf_title', $fields[$name]->caption );
                $item->setVar( 'conf_desc', $fields[$name]->description );
                $item->setVar( 'conf_formtype', $fields[$name]->field );
            }
            $item->set_value( $value, $item->getVar( 'conf_valuetype' ) );
            $item->save();

        }

    }

    /**
     * Add new items
     */
    if ( !empty( $to_add ) ) {

        foreach ($to_add as $name => $value) {

            $item = new Rmcommon_Config_Item( $name, $module->mid() );
            if ( isset( $fields[$name] ) ) {
                $item->setVar( 'conf_modid', $module->mid() );
                $item->setVar( 'conf_name', $name );
                $item->setVar( 'conf_valuetype', $fields[$name]->type );
                $item->setVar( 'conf_title', $fields[$name]->caption );
                $item->setVar( 'conf_desc', $fields[$name]->description );
                $item->setVar( 'conf_formtype', $fields[$name]->field );
            }
            $item->set_value( $value, $item->getVar( 'conf_valuetype' ) );
            $item->save();

        }

    }

    /**
     * Notify to system events
     */
    RMEvents::get()->trigger( 'rmcommon.saved.settings', $module->dirname(), $to_save, $to_add, $to_delete );

    if ( $module->getInfo( 'hasAdmin' ) )
        $goto = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getInfo('adminindex');
    else
        $goto = 'settings.php';

    if ( $via_ajax )
        $ajax->ajax_response(
            __('Settings saved successfully!', 'rmcommon'), 0, 1, array(
                'closeWindow' => '#cu-settings-form',
                'notify' => array(
                    'icon' => 'svg-rmcommon-ok-circle',
                    'type' => 'alert-success'
                )
            )
        );
    else
        RMUris::redirect_with_message( __('Settings saved successfully!', 'rmcommon'), $goto, RMMSG_SUCCESS, 'fa fa-check' );

}

$action = RMHttpRequest::request( 'action', 'string', '' );

switch ($action) {

    /**
     * Show options for a specific element
     */
    case 'configure':
        show_module_preferences();
        break;

    /**
     * Save settings for a single module
     */
    case 'save-settings':
        save_module_settings();
        break;

    default:
        show_configurable_items();
        break;

}
