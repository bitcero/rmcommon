<?php
// $Id: menu.php 884 2011-12-28 02:09:44Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

if (!function_exists('__'))
    include_once XOOPS_ROOT_PATH.'/modules/rmcommon/loader.php';

$adminmenu[] = array(
    'title' => __('Dashboard','rmcommon'),
    'link' => "index.php",
    'icon' => "images/dashboard.png",
    'location' => "dashboard"
);

$adminmenu[] = array(
    'title' => __('Modules','rmcommon'),
    'link' => "modules.php",
    'icon' => "images/modules.png",
    'location' => "modules"
);

$adminmenu[] = array(
    'title' => __('Blocks','rmcommon'),
    'link' => "blocks.php",
    'icon' => "images/blocks.png",
    'location' => "blocks"
);

$adminmenu[] = array(
    'title' => __('Users','rmcommon'),
    'link' => 'users.php',
    'icon' => 'images/users.png',
    'location' => 'users',
    'options' => array(
        array(
            'title'     => __('Groups','rmcommon'),
            'link'      => 'groups.php',
            'selected'  => 'allgroups',
            'icon'      => 'images/group.png',
        ),
        array(
            'title'     => __('Add Group','rmcommon'),
            'link'      => 'groups.php?action=new-group',
            'selected'  => 'addgroup',
            'icon'      => 'images/group_add.png',
            'attributes' => array(
                'data-action' => 'load-remote-dialog'
            )
        ),
        array(
            'divider'   => 1,
        ),
        array(
            'title'     => __('Users','rmcommon'),
            'link'      => 'users.php',
            'selected'  => 'allusers',
            'icon'      => 'images/users.png',
        ),
        array(
            'title'     => __('New user','rmcommon'),
            'link'      => 'users.php?action=new',
            'selected'  => 'newuser',
            'icon'      => 'images/user_add.png',
        ),
    )
);

$adminmenu[] = array(
    'title' => __('Images','rmcommon'),
    'link' => "images.php",
    'icon' => "images/images.png",
    'location' => "imgmanager",
    'options' => array(0 => array(
                    'title'		=>	__('Categories','rmcommon'),
                    'link'		=> 'images.php?action=showcats',
                    'selected'	=> 'rmc_imgcats',
                    'icon'      => 'images/category.png',
            ), 1 => array(
                    'title'		=>	__('New category','rmcommon'),
                    'link'		=> 'images.php?action=newcat',
                    'selected'	=> 'rmc_imgnewcat',
                    'icon'      => 'images/category_add.png',
            ), 2 => array(
                    'divider'   => 1
            ), 3 => array(
                    'title'		=>	__('Images','rmcommon'),
                    'link'		=> 'images.php',
                    'selected'	=> 'rmc_images',
                    'icon'      => 'images/image.png'
            ), 4 => array(
                    'title'		=>	__('Add images','rmcommon'),
                    'link'		=> 'images.php?action=new',
                    'selected'	=> 'rmc_newimages',
                    'icon'      => 'images/image_add.png',
            )
    )
);

$adminmenu[] = array(
    'title' => __('Comments','rmcommon'),
    'link' => "comments.php",
    'icon' => "images/comments.png",
    'location' => "comments"
);

$adminmenu[] = array(
    'title' => __('Plugins','rmcommon'),
    'link' => "plugins.php",
    'icon' => "images/plugin.png",
    'location' => "plugins"
);

$adminmenu[] = array(
    'title' => __('Updates','rmcommon'),
    'link' => "updates.php",
    'icon' => "images/updates.png",
    'location' => "updates"
);
