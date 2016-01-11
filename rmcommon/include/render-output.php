<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2015 Eduardo Cortés http://www.redmexico.com.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

/**
 * Modify the page output to include some new features
 *
 * @param mixed $output
 * @return string
 */
function cu_render_output($output)
{
    global $xoTheme, $xoopsTpl;

    $rmEvents = RMEvents::get();

    if (function_exists('xoops_cp_header')) return $output;

    $page = $output;
    if ($xoopsTpl) {
        if (defined('COMMENTS_INCLUDED') && COMMENTS_INCLUDED) {
            RMTemplate::get()->add_style('comments.css', 'rmcommon');
        }
    }

    include_once RMTemplate::get()->path('rmc-header.php', 'module', 'rmcommon');
    $rtn = $htmlStyles;
    $rtn .= $htmlScripts['header'];
    $rtn .= $htmlScripts['inlineHeader'];

    $find = [];
    $repl = [];
    foreach ($metas as $name => $content) {

        $str = "<meta\s+name=['\"]??" . $name . "['\"]??\s+content=['\"]??(.+)['\"]??\s*\/?>";
        if (preg_match($str, $page)) {
            $find[] = $str;
            $str = "meta name=\"$name\" content=\"$content\" />\n";
            $repl[] = $str;
        } else {

            $rtn .= "\n<meta name=\"$name\" content=\"$content\" />";

        }

    }

    if (!empty($find))
        $page = preg_replace($find, $repl, $page);

    $pos = strpos($page, "</body>");
    if ($pos === FALSE) return $output;

    $ret = substr($page, 0, $pos) . "\n";
    $ret .= $htmlScripts['footer'] . "\n" . $htmlScripts['inlineFooter'] . "\n" . $htmlScripts['heads'] . "\n";
    $ret .= substr($page, $pos);

    $page = $ret;

    $pos = strpos($page, "<!-- RMTemplateHeader -->");
    if ($pos !== FALSE) {
        $page = str_replace('<!-- RMTemplateHeader -->', $rtn, $page);
        $page = $rmEvents->trigger('rmcommon.end.flush', $page);
        return $page;
    }

    $pos = strpos($page, "</head>");
    if ($pos === FALSE) return $output;

    $ret = substr($page, 0, $pos) . "\n";
    $ret .= $rtn;
    $ret .= substr($page, $pos);

    $ret = $rmEvents->trigger('rmcommon.end.flush', $ret);

    return $ret;
}