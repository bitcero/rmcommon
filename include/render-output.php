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
    global $xoTheme, $xoopsTpl, $common, $xoopsModule;

    $rmEvents = RMEvents::get();

    /**
     * Temporal solution to ModuleAdmin __constructor method
     * @todo Delete
     */
    if(false == $common->isAjax) {
        $pos = strpos($output, '<!DOCTYPE');
        if($pos > 0){
            $toMove = substr($output, 0, $pos);
            $output = substr($output, $pos);

            if(!$xoopsModule || !$xoopsModule->getInfo('rmnative')){
                $pos = strpos($output, '</head>', 0);
                $output = substr($output, 0, $pos) . $toMove . "\n" . substr($output, $pos);
                unset($pos, $toMove);
            }

            return $output;
        }
    }

    if (function_exists('xoops_cp_header')) return $output;

    $page = $output;
    if ($xoopsTpl) {
        if (defined('COMMENTS_INCLUDED') && COMMENTS_INCLUDED) {
            RMTemplate::get()->add_style('comments.css', 'rmcommon');
        }
    }

    include_once RMTemplate::get()->path('rmc-header.php', 'module', 'rmcommon');
    /*$rtn = $htmlStyles;
    $rtn .= $htmlScripts['header'];
    $rtn .= $htmlScripts['inlineHeader'];*/
    $rtn = '';

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

    $headerRendered = false;
    $footerRendered = false;

    if(FALSE !== $pos = strpos($page, '<!-- RMTemplateHeader -->')){
        // Replace RMTemplateHeader with scripts and styles
        $ssContent = $rtn . $htmlStyles . $htmlScripts['header'] . $htmlScripts['inlineHeader'];
        $page = str_replace('<!-- RMTemplateHeader -->', $ssContent, $page);
        $headerRendered = true;
    }

    if(FALSE !== $pos = strpos($page, '<!-- RMTemplateFooter -->')){
        // Replace RMTemplateHeader with scripts and styles
        $ssContent = $htmlScripts['footer'] . $htmlScripts['inlineFooter'];
        $page = str_replace('<!-- RMTemplateFooter -->', $ssContent, $page);
        $footerRendered = true;
    }

    // Inject code if this is a standard theme
    // Natives themes must to include appropiate code
    if(false == $common->nativeTheme){
        $pos = strpos($page, "</head>");
        if ($pos !== FALSE && false == $headerRendered){
            $ssContent = $rtn . $htmlStyles . $htmlScripts['header'] . $htmlScripts['inlineHeader'];
            $ret = substr($page, 0, $pos) . "\n";
            $ret .= $ssContent;
            $page = $ret . substr($page, $pos);
        }

        $pos = strpos($page, "</body>");
        if ($pos !== FALSE && false == $footerRendered){
            $ssContent = $htmlScripts['footer'] . $htmlScripts['inlineFooter'];
            $ret = substr($page, 0, $pos) . "\n";
            $ret .= $ssContent;
            $page = $ret . substr($page, $pos);
        }
    }

    unset($rtn, $ssContent, $ret);

    $ret = $rmEvents->trigger('rmcommon.end.flush', $page);

    return $ret;
}

// Start
ob_start('cu_render_output');