<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

// Constant to specify the internal location
// Could be useful for themes, plugins and modules
define('RMCLOCATION', 'groups');

include '../../include/cp_header.php';

/**
 * Shows existing groups
 */
function show_groups_list(){
    global $xoopsDB;

    define('RMCSUBLOCATION', 'allgroups');

    list($total) = $xoopsDB->fetchRow( $xoopsDB->query( "SELECT COUNT(*) FROM " . $xoopsDB->prefix("groups")));
    $navigation = new RMPageNav( $total, 20, RMHttpRequest::get( 'page', 'integer', 1 ) );
    $navigation->target_url( RMCURL . '/users.php?action=groups&amp;page={PAGE_NUM}' );

    $sql = "SELECT g.*, (SELECT COUNT(*) FROM ". $xoopsDB->prefix("groups_users_link") . "
            WHERE groupid = g.groupid) as total_users FROM " . $xoopsDB->prefix( "groups" ) . " as g ORDER BY g.name
            LIMIT " . $navigation->start() . ", 20";
    $result = $xoopsDB->query( $sql );
    $groups = array();
    while ( $row = $xoopsDB->fetchArray( $result ) ) {
        $groups[] = (object) $row;
    }

    $form = new RMForm('');

    $bc = RMBreadCrumb::get();
    $bc->add_crumb( __('Users Management', 'rmcommon'), RMCURL . '/users.php' );
    $bc->add_crumb( __('Groups', 'rmcommon') );

    RMTemplate::get()->assign('xoops_pagetitle', __('Groups Management', 'rmcommon'));

    RMFunctions::get()->create_toolbar();
    RMTemplate::get()->add_script( 'cu-groups.js', 'rmcommon', array('footer' => 1) );
    include RMCPATH . '/js/cu-js-language.php';

    RMTemplate::get()->header();

    include RMTemplate::get()->get_template( 'rmc-groups.php', 'module', 'rmcommon' );

    RMTemplate::get()->footer();

}

function show_group_form(){
    global $xoopsDB, $xoopsSecurity;

    $ajax = new Rmcommon_Ajax();

    $ajax->prepare_ajax_response();

    $id = RMHttpRequest::get( 'id', 'integer', 0 );

    if ($id > 0) {

        $group = new Rmcommon_Group( $id );
        if ( $group->isNew() )
            $ajax->ajax_response(
                __('The specified group does not exists!', 'rmcommon'), 1, 1
            );

    } else{
        $group = new Rmcommon_Group();
    }

    $form = new RMForm('');

    $result = $xoopsDB->query( "SELECT * FROM " . $xoopsDB->prefix("modules") . " ORDER BY name ASC" );
    $modules = array();

    $admin_rights = $group->load_permissions( 'module_admin' );
    $access_rights = $group->load_permissions( 'module_read' );

    while ( $row = $xoopsDB->fetchArray( $result ) ) {

        $modules[$row['mid']] = (object) $row;
        $modules[$row['mid']]->permissions = RMPrivileges::module_permissions( $row['dirname'] );
        $modules[$row['mid']]->privileges = RMPrivileges::read_permissions( $row['dirname'], $group->id() );
        $modules[$row['mid']]->admin = isset( $admin_rights[ $row['mid'] ] );
        $modules[$row['mid']]->read = isset( $access_rights[ $row['mid'] ] );

    }

    ob_start();
    include RMTemplate::get()->get_template( 'rmc-groups-form.php', 'module', 'rmcommon' );
    $content = ob_get_clean();

    $ajax->ajax_response(
        $group->isNew() ? __('Create new group', 'rmcommon') : sprintf(__('Edit group "%s"', 'rmcommon'), $group->getVar('name')),
        0, 1, array(
            'content' => $content,
            'width' => 'xlarge',
            'color' => 'green',
            'windowId' => 'window-form-groups',
            'id' => 'groups-dialog'
        )
    );

}

function save_group_data(){

    global $xoopsSecurity;

    $ajax = new Rmcommon_Ajax();

    $ajax->prepare_ajax_response();

    if ( !$xoopsSecurity->validateToken( false, true, 'CUTOKEN' ) )
        $ajax->ajax_response(
            __( 'Session token expired', 'rmcommon' ),
            1, 0, array(
                'reload' => 'reload'
            )
        );

    $name = RMHttpRequest::post( 'name', 'string', '' );
    $description = RMHttpRequest::post( 'description', 'string', '' );
    $icon = RMHttpRequest::post( 'icon', 'string', '' );
    $id = RMHttpRequest::post( 'id', 'integer', 0 );

    if( $name == '' )
        $ajax->ajax_response(
            __('No name has been provided.', 'rmcommon' ),
            1, 1
        );

    if( $description == '' )
        $ajax->ajax_response(
            __('Description has not been provided.', 'rmcommon' ),
            1, 1
        );

    if ($id > 0) {
        $group = new Rmcommon_Group( $id );
        if( $group->isNew() )
            $ajax->ajax_response(
                __('Specified group does not exists. Please try again.', 'rmcommon' ),
                1, 1, array(
                    'closeWindow' => 'window-form-groups'
                )
            );
    } else {
        $group = new Rmcommon_Group();
    }

    $group->setVar('name', $name);
    $group->setVar('description', $description);
    $group->setVar('icon', $icon);

    if ( !$group->save() )
        $ajax->ajax_response(
            __('Errors ocurred while trying to save group data.', 'rmcommon' ) . "\n" . $group->errors() ,
            1, 1
        );

    // Guardamos los permisos de administración
    $admin = RMHttpRequest::post( 'admin_modules', 'array', array() );
    if ( !$group->set_admin_permissions( $admin ) )
        $ajax->ajax_response(
            __('Administrative rights could not be saved.', 'rmcommon' ) . "\n" . $group->errors() ,
            1, 1, array(
                'reload' => 'reload'
            )
        );

    // Guardamos los permisos operativos
    $read = RMHttpRequest::post( 'read_modules', 'array', array() );
    if ( !$group->set_read_permissions( $read ) )
        $ajax->ajax_response(
            __('Access rights could not be saved.', 'rmcommon' ) . "\n" . $group->errors() ,
            1, 1, array(
                'reload' => 'reload'
            )
        );

    // Guardamos los permisos específicos
    $admin = RMHttpRequest::post( 'specific_perms', 'array', array() );
    if ( !$group->set_specific_permissions( $admin ) )
        $ajax->ajax_response(
            __('Specific permissions could not be saved.', 'rmcommon' ) . "\n" . $group->errors() ,
            1, 1, array(
                'reload' => 'reload'
            )
        );

    showMessage(__( 'Data saved successfully.', 'rmcommon'), RMMSG_SUCCESS, 'fa fa-hd');

    // ëxito en las operaciones
    $ajax->ajax_response(
        __( 'Data saved successfully.', 'rmcommon'),
        0, 1, array(
            'reload' => 'reload'
        )
    );

}

function delete_group_data() {
    global $xoopsSecurity, $xoopsDB;

    $ajax = new Rmcommon_Ajax();
    $ajax->prepare_ajax_response();

    if(!$xoopsSecurity->validateToken(false, true, 'CUTOKEN'))
        $ajax->ajax_response(
            __('Session token expired!', 'rmcommon'),
            1, 0, array('action' => 'reload')
        );

    $ids = RMHttpRequest::post( 'ids', 'array', array() );

    if( empty( $ids ) )
        $ajax->ajax_response( __('You must select at least one group. Please, try again.', 'rmcommon'), 1, 1 );

    $to_delete = array_search( XOOPS_GROUP_ADMIN, $ids );

    if ( FALSE !== $to_delete )
        unset( $ids[$to_delete] );

    $to_delete = array_search( XOOPS_GROUP_USERS, $ids );

    if ( FALSE !== $to_delete )
        unset( $ids[$to_delete] );

    $to_delete = array_search( XOOPS_GROUP_ANONYMOUS, $ids );

    if ( FALSE !== $to_delete )
        unset( $ids[$to_delete] );

    if( empty( $ids ) )
        $ajax->ajax_response(
            __( 'No valid groups has been selected. Note that system groups could not be deleted.', 'rmcommon'),
            1, 1
        );

    $errors = '';
    // Eliminar permisos del grupo
    $sql = "DELETE FROM " . $xoopsDB->prefix("group_permission")." WHERE gperm_groupid IN (" . implode(",", $ids).")";

    if( !$xoopsDB->queryF( $sql ) )
        $errors .= $xoopsDB->error();

    // Eliminar permisos específicos
    $sql = "DELETE FROM " . $xoopsDB->prefix("mod_rmcommon_permissions") . " WHERE `group` IN (" . implode(",", $ids) . ")";
    if( !$xoopsDB->queryF( $sql ) )
        $errors .= '<br>' . $xoopsDB->error();

    // Eliminar relaciones con usuarios
    $sql = "DELETE FROM " . $xoopsDB->prefix("groups_users_link") . " WHERE `groupid` IN (" . implode(",", $ids) . ")";
    if( !$xoopsDB->queryF( $sql ) )
        $errors .= '<br>' . $xoopsDB->error();

    // Eliminar datos del grupo
    $sql = "DELETE FROM " . $xoopsDB->prefix("groups") . " WHERE `groupid` IN (" . implode(",", $ids) . ")";
    if( !$xoopsDB->queryF( $sql ) )
        $errors .= '<br>' . $xoopsDB->error();

    if ('' == $errors) {

        showMessage( __('Selected groups has been deleted.', 'rmcommon' ), RMMSG_SUCCESS, 'fa fa-remove-circle' );

        $ajax->ajax_response( '', 0, 1, array( 'reload' => true ) );

    } else {

        $ajax->ajax_response( __('Errors ocurred while trying to delete selected groups.', 'rmcommon') . "\n" . $errors, 1, 1 );

    }

}

// get the action
$action = RMHttpRequest::request( 'action', 'string', '' );

switch ($action) {

    case 'new-group':
        show_group_form();
        break;

    case 'save-group':
        save_group_data();
        break;

    case 'delete-group':
        delete_group_data();
        break;

    default:
        show_groups_list();
        break;

}
