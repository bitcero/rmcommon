<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$rmcommon_permissions = array(

    /**
     * Modules managament privileges
     */
    'admin-modules' => array(
        'caption' => __('Modules management (Install, uninstall and update)', 'rmcommon'),
        'default' => 'deny'
    ),

    'admin-images' => array(
        'caption' => __('Images management', 'rmcommon'),
        'default' => 'deny'
    ),

    'admin-comments' => array(
        'caption' => __('Comments management', 'rmcommon'),
        'default' => 'deny'
    ),

    'admin-plugins' => array(
        'caption' => __('Plugins management (Install, uninstall and update)', 'rmcommon'),
        'default' => 'deny'
    ),

    'admin-groups' => array(
        'caption' => __('Groups management (create, delete and update)', 'rmcommon'),
        'default' => 'deny'
    ),

    'configure-system' => array(
        'caption' => __('Grant access to system and modules configuration.', 'rmcommon'),
        'default' => 'deny'
    ),



);

return $rmcommon_permissions;