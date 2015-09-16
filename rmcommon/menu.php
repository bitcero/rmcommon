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
    'icon' => "icon icon-meter2",
    'location' => "dashboard"
);

$adminmenu[] = array(
    'title' => __('Modules','rmcommon'),
    'link' => "modules.php",
    'icon' => "glyphicon glyphicon-th-large",
    'location' => "modules"
);

$adminmenu[] = array(
    'title' => __('Blocks','rmcommon'),
    'link' => "blocks.php",
    'icon' => "fa fa-cubes",
    'location' => "blocks"
);

$adminmenu[] = array(
    'title' => __('Users','rmcommon'),
    'link' => 'users.php',
    'icon' => 'icon icon-user',
    'location' => 'users'
);

$adminmenu[] = array(
    'title' => __('Groups','rmcommon'),
    'link' => 'groups.php',
    'icon' => 'icon icon-users',
    'location' => 'groups'
);

$adminmenu[] = array(
    'title' => __('Images','rmcommon'),
    'link' => "images.php",
    'icon' => "icon icon-images",
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
    'icon' => "icon icon-bubbles4",
    'location' => "comments"
);

$adminmenu[] = array(
    'title' => __('Plugins','rmcommon'),
    'link' => "plugins.php",
    'icon' => "icon icon-power-cord",
    'location' => "plugins"
);

$adminmenu[] = array(
    'title' => __('Updates','rmcommon'),
    'link' => "updates.php",
    'icon' => "icon icon-loop2",
    'location' => "updates"
);

$adminmenu[] = array(
    'title' => __('About','rmcommon'),
    'link' => "about.php",
    'icon' => "icon icon-info",
    'location' => "about"
);
