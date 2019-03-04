<?php
// $Id: blocksfunctions.php 1064 2012-09-17 16:46:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMBlocksFunctions
{
    /**
     * Get the available widgets list
     *
     * @param null|mixed $mods
     * @return array
     */
    public static function get_available_list($mods = null)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        if (null === $mods || empty($mods)) {
            $mods = RMModules::get_modules_list();
        }

        $list = []; // Block list to return

        foreach ($mods as $mod) {
            if (!file_exists(XOOPS_ROOT_PATH . '/modules/' . $mod['dirname'] . '/xoops_version.php')) {
                continue;
            }

            load_mod_locale($mod['dirname']);
            $module = new XoopsModule();
            $module->loadInfoAsVar($mod['dirname']);

            $icon = &$module->getInfo('icon');

            $list[$mod['dirname']] = [
                'name' => $mod['name'],
                'blocks' => $module->getInfo('blocks'),
                'icon' => $icon,
            ];
        }

        // Event generated to modify the available widgets list
        $list = RMEvents::get()->run_event('rmcommon.available.widgets', $list);

        return $list;
    }

    /**
     * Get blocks positions
     * @param mixed $active
     */
    public static function block_positions($active = '')
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $result = $db->query('SELECT * FROM ' . $db->prefix('mod_rmcommon_blocks_positions') . ('' != $active ? ' WHERE active=' . $active : ''));
        $pos = [];
        while (false !== ($row = $db->fetchArray($result))) {
            $pos[$row['id_position']] = $row;
        }

        return $pos;
    }

    /**
     * Get blocks
     */
    public static function construct_blocks()
    {
        global $xoopsConfig, $xoopsModule, $xoopsUser, $xoopsOption;

        $sides = [];

        foreach (self::block_positions(1) as $id => $row) {
            $sides[$id] = $row['tag'];
            $blocks[$row['tag']] = [];
        }

        $startMod = ('--' == $xoopsConfig['startpage']) ? 'system' : $xoopsConfig['startpage'];
        if (@is_object($xoopsModule)) {
            list($mid, $dirname) = [$xoopsModule->getVar('mid'), $xoopsModule->getVar('dirname')];
            $isStart = ('index.php' == mb_substr($_SERVER['PHP_SELF'], -9) && $xoopsConfig['startpage'] == $dirname);
        } else {
            $sys = RMModules::load_module('system');
            list($mid, $dirname) = [$sys->getVar('mid'), 'system'];
            $isStart = !@empty($GLOBALS['xoopsOption']['show_cblock']);
        }

        $groups = @is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];

        $subpage = isset($xoopsOption['module_subpage']) ? $xoopsOption['module_subpage'] : '';
        $barray = []; // Array of retrieved blocks

        $barray = self::get_blocks($groups, $mid, $isStart, XOOPS_BLOCK_VISIBLE, '', 1, $subpage, array_keys($sides));

        foreach ($barray as $block) {
            if (!isset($sides[$block->getVar('canvas')])) {
                continue;
            }

            $side = $sides[$block->getVar('canvas')];
            if ($content = self::buildBlock($block)) {
                $blocks[$side][$content['id']] = $content;
            }
        }

        unset($side, $sides, $content, $subpage, $barray, $groups, $startMod);

        return is_array($blocks) ? $blocks : [];
    }

    public static function get_blocks($groupid, $mid = 0, $toponlyblock = false, $visible = null, $orderby = 'b.weight,b.wid', $isactive = 1, $subpage = '', $canvas = [])
    {
        $orderby = '' == $orderby ? 'b.weight,b.bid' : $orderby;

        // Get authorized blocks
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $ret = [];
        $sql = 'SELECT DISTINCT
                    gperm_itemid
                FROM
                    ' . $db->prefix('group_permission') . "
                WHERE
                    (gperm_name = 'rmblock_read')
                AND
                    gperm_modid = 1";

        if (is_array($groupid)) {
            $sql .= ' AND gperm_groupid IN (' . implode(',', $groupid) . ',0)';
        } else {
            if ((int)$groupid > 0) {
                $sql .= ' AND gperm_groupid IN (0,' . $groupid . ')';
            }
        }

        $result = $db->query($sql);

        $blockids = [];
        while (false !== ($myrow = $db->fetchArray($result))) {
            $blockids[] = $myrow['gperm_itemid'];
        }

        if (!empty($blockids)) {
            $sql = 'SELECT b.* FROM ' . $db->prefix('mod_rmcommon_blocks') . ' b, ' . $db->prefix('mod_rmcommon_blocks_assignations') . ' m WHERE m.bid=b.bid';
            $sql .= ' AND b.isactive=' . $isactive;
            if (isset($visible)) {
                $sql .= ' AND b.visible=' . (int)$visible;
            }
            $mid = (int)$mid;
            if (!empty($mid)) {
                $sql .= ' AND m.mid IN (0,' . $mid;
                if ($toponlyblock) {
                    $sql .= ',1';
                }
                $sql .= ')';
            } else {
                /*   if ($toponlyblock) {*/
                $sql .= ' AND m.mid IN (-1,1)';
                /*} else {
                    $sql .= ' AND m.app_id=0';
                }*/
            }
            $sql .= '' != $subpage ? " AND (m.page='$subpage' OR m.page='--')" : '';
            $sql .= ' AND b.bid IN (' . implode(',', $blockids) . ')';

            if (is_array($canvas)) {
                $sql .= ' AND b.canvas IN (' . implode(',', $canvas) . ')';
            }

            $sql .= ' ORDER BY ' . $orderby;
            $result = $db->query($sql);

            while (false !== ($myrow = $db->fetchArray($result))) {
                $block = new RMInternalBlock();
                $block->assignVars($myrow);
                $ret[$myrow['bid']] = $block;
                unset($block);
            }
        }

        return $ret;
    }

    public static function buildBlock($bobj)
    {
        global $xoopsTpl, $xoTheme;
        $template = $xoopsTpl;

        $block = [
            'id' => $bobj->getVar('bid'),
            'module' => $bobj->getVar('dirname'),
            'title' => $bobj->getVar('name'),
            // 'name'        => strtolower( preg_replace( '/[^0-9a-zA-Z_]/', '', str_replace( ' ', '_', $bobj->getVar( 'name' ) ) ) ),
            'weight' => $bobj->getVar('weight'),
            'type' => $bobj->getVar('element_type'),
        ];

        $bcachetime = (int)$bobj->getVar('bcachetime');
        if (empty($bcachetime)) {
            $template->caching = 0;
        } else {
            $template->caching = 2;
            $template->cache_lifetime = $bcachetime;
        }
        $template->setCompileId($bobj->getVar('dirname', 'n'));
        if ('plugin' == $bobj->getVar('element_type')) {
            $tplName = XOOPS_ROOT_PATH . '/modules/' . $bobj->getVar('element') . '/plugins/' . $bobj->getVar('dirname') . '/templates/blocks/' . $bobj->getVar('template');
        } elseif ('theme' == $bobj->getVar('element_type')) {
            $tplName = XOOPS_ROOT_PATH . '/themes/' . $bobj->getVar('dirname') . '/templates/blocks/' . $bobj->getVar('template');
        } else {
            $tplName = ($tplName = $bobj->getVar('template')) ? "db:$tplName" : 'db:system_block_dummy.html';
        }
        $cacheid = $xoTheme->generateCacheId('blk_' . $bobj->getVar('bid'));
        /**
         * , $bobj->getVar( 'show_func', 'n' )
         */

        $xoopsLogger = XoopsLogger::getInstance();
        if (!$bcachetime || !$template->is_cached($tplName, $cacheid)) {
            $xoopsLogger->addBlock($bobj->getVar('name'));
            if ($bresult = $bobj->buildBlock()) {
                $template->assign('block', $bresult);
                $block['content'] = $template->fetch($tplName, $cacheid);
            } else {
                $block = false;
            }
        } else {
            $xoopsLogger->addBlock($bobj->getVar('name'), true, $bcachetime);
            $block['content'] = $template->fetch($tplName, $cacheid);
        }

        $template->setCompileId();

        return $block;
    }
}
