<?php
// $Id: breadcrumb.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * This class allow to manage a breadcrumb navigation for
 * modules and other components
 */
class RMBreadCrumb
{
    private $crumbs = [];

    public function construct()
    {
    }

    /**
     * Add a new crumb to the crumbs
     *
     * You can add new items to the crumbs array. A crumb must contain a caption text,
     * the link that this item will follow and, optionally, an icon and a submenu.
     *
     * @param string Caption text
     * @param string Link address
     * @param string Icon url for this item
     * @param array  Submenu for this item. This paramter must be passed as array containing caption, link and [icon]
     * @param mixed $caption
     * @param mixed $link
     * @param mixed $icon
     * @param mixed $menu
     * @return int Id for item
     */
    public function add_crumb($caption, $link = '', $icon = '', $menu = [])
    {
        if ('' == trim($caption)) {
            return 0;
        }

        $this->crumbs[] = [
            'caption' => $caption,
            'link' => $link,
            'icon' => $icon,
            'menu' => $menu,
        ];

        end($this->crumbs);

        return key($this->crumbs);
    }

    /**
     * Add a submenu to an existing crumb
     *
     * @param string Caption text
     * @param string Link address
     * @param string Icon url for this item
     * @param mixed $id
     * @param mixed $caption
     * @param mixed $link
     * @param mixed $icon
     */
    public function add_menu($id, $caption, $link, $icon = '')
    {
        if ('' == trim($caption) || '' == trim($link)) {
            return 0;
        }

        if (!isset($this->crumbs[$id])) {
            return 0;
        }

        $this->crumbs[$id]['menu'] = [
            'caption' => $caption,
            'link' => $link,
            'icon' => $icon,
        ];

        return null;
    }

    /**
     * Clear the crumbs array
     */
    public function clear()
    {
        $this->crumbs = [];
    }

    public function count()
    {
        return count($this->crumbs);
    }

    /**
     * Render the current crumbs array
     *
     * @return string
     */
    public function render()
    {
        global $cuIcons, $xoopsModule;

        RMTemplate::getInstance()->add_style('breadcrumb.css', 'rmcommon');

        // Include module controller if exists
        if ($xoopsModule) {
            $controller = RMFunctions::loadModuleController($xoopsModule->getVar('dirname'));
        } else {
            $controller = false;
        }

        ob_start();

        include RMTemplate::getInstance()->path('rmc-breadcrumb.php', 'module', 'rmcommon');

        $ret = ob_get_clean();

        return $ret;
    }

    /**
     * Use this method to instantiate EXMTemplate
     * @staticvar <type> $instance
     * @return RMBreadCrumb
     */
    public static function get()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
