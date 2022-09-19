<?php

/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
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
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */
class CUCommentsService extends \Common\Core\Helpers\ServiceAbstract implements \Common\Core\Helpers\ServiceInterface
{
    public function load($parameters)
    {
        global $xoopsUser, $common, $cuSettings, $cuServices;

        define('COMMENTS_INCLUDED', 1);
        $db = \XoopsDatabaseFactory::getDatabaseConnection();

        $object = '';
        $identifier = '';
        $type = 'module';
        $assign = false;
        $user = null;
        $parent = 0;

        extract($parameters);

        //$rmc_config = RMSettings::cu_settings();

        $identifier = urlencode($identifier);
        $sql = 'SELECT * FROM ' . $db->prefix('mod_rmcommon_comments') . " WHERE status='approved' AND id_obj='$object' AND params='$identifier' AND type='$type' AND parent='$parent'" . (null === $user ? '' : " AND user='$user'") . ' ORDER BY posted';
        $result = $db->query($sql);

        $ucache = [];
        $ecache = [];
        $comms = [];

        while (false !== ($row = $db->fetchArray($result))) {
            $com = new \RMComment();
            $com->assignVars($row);

            // Editor data
            if (!isset($ecache[$com->getVar('user')])) {
                $ecache[$com->getVar('user')] = new \RMCommentUser($com->getVar('user'));
            }

            $editor = $ecache[$com->getVar('user')];

            if ($editor->getVar('xuid') > 0) {
                if (!isset($ucache[$editor->getVar('xuid')])) {
                    $ucache[$editor->getVar('xuid')] = new \XoopsUser($editor->getVar('xuid'));
                }

                $user = $ucache[$editor->getVar('xuid')];

                $poster = [
                    'id' => $user->getVar('uid'),
                    'name' => '' != $user->getVar('name') ? $user->getVar('name') : $user->getVar('uname'),
                    'email' => $user->getVar('email'),
                    'posts' => $user->getVar('posts'),
                    'avatar' => $cuServices->service('avatar') ? $cuServices->avatar->getAvatarSrc($xoopsUser, 0) : XOOPS_UPLOAD_URL . '/' . $user->getVar('image'),
                    'rank' => $user->rank(),
                    'url' => 'http://' != $user->getVar('url') ? $user->getVar('url') : '',
                ];
            } else {
                $poster = [
                    'id' => 0,
                    'name' => $editor->getVar('name'),
                    'email' => $editor->getVar('email'),
                    'posts' => 0,
                    'avatar' => '',
                    'rank' => '',
                    'url' => 'http://' != $editor->getVar('url') ? $editor->getVar('url') : '',
                ];
            }

            if ($xoopsUser && $xoopsUser->isAdmin()) {
                $editlink = RMCURL . '/comments.php?action=edit&amp;id=' . $com->id() . '&amp;ret=' . urlencode(\RMUris::current_url());
            } elseif ($cuSettings->allow_edit) {
                $time_limit = time() - $com->getVar('posted');
                if ($xoopsUser && $xoopsUser->getVar('uid') == $editor->getVar('xuid') && $time_limit < ($cuSettings->edit_limit * 3600)) {
                    $editlink = RMCURL . '/post-comment.php?action=edit&amp;id=' . $com->id() . '&amp;ret=' . urlencode(\RMUris::current_url());
                } else {
                    $editlink = '';
                }
            }

            $comms[] = [
                'id' => $row['id_com'],
                'text' => \TextCleaner::getInstance()->clean_disabled_tags(\TextCleaner::getInstance()->popuplinks(\TextCleaner::getInstance()->nofollow($com->getVar('content')))),
                'poster' => $poster,
                'posted' => sprintf(__('Posted on %s'), formatTimestamp($com->getVar('posted'), 'l')),
                'ip' => $com->getVar('ip'),
                'edit' => $editlink,
                'time' => $com->getVar('posted'),
            ];

            unset($editor);
        }

        $comms = $common->events()->trigger('rmcommon.loading.comments', $comms, $object, $identifier, $type, $parent, $user);

        global $xoopsTpl;
        $xoopsTpl->assign('lang_edit', __('Edit', 'rmcommon'));

        if ($assign) {
            $xoopsTpl->assign('comments', $comms);

            return true;
        }

        return $comms;
    }

    public function form($parameters)
    {
        global $xoopsTpl, $xoopsRequestUri, $xoopsUser, $cuSettings, $common;

        $url = '';
        $object = '';
        $identifier = '';
        $file = '';
        $type = 'module';

        extract($parameters);

        if (!$cuSettings->enable_comments) {
            return false;
        }

        if (!$xoopsUser && !$cuSettings->anonymous_comments) {
            return false;
        }

        if (!defined('COMMENTS_INCLUDED')) {
            define('COMMENTS_INCLUDED', 1);
        }

        $xoopsTpl->assign('enable_comments_form', 1);

        $form = [
            'show_name' => !($xoopsUser),
            'lang_name' => __('Name', 'rmcommon'),
            'show_email' => !($xoopsUser),
            'lang_email' => __('Email address', 'rmcommon'),
            'show_url' => !($xoopsUser),
            'lang_url' => __('Web site', 'rmcommon'),
            'lang_text' => __('Your comment', 'rmcommon'),
            'lang_submit' => __('Submit Comment', 'rmcommon'),
            'lang_title' => __('Submit a comment', 'rmcommon'),
            'uri' => urlencode(\RMUris::current_url()),
            'actionurl' => RMCURL . '/post-comment.php',
            'params' => urlencode($identifier),
            'update' => urlencode(str_replace(XOOPS_ROOT_PATH, '', $file)),
            'type' => $type,
            'object' => $object,
            'action' => 'save',
        ];

        if ($common->services()->service('captcha')) {
            $form['fields'] = [
                'captcha' => $common->services()->captcha->render(),
            ];
        }

        // You can include new content into Comments form
        // eg. Captcha checker, etc

        $form = $common->events()->trigger('rmcommon.comments.form', $form, $object, $identifier, $type);
        \RMTemplate::getInstance()->add_jquery();
        \RMTemplate::getInstance()->add_script('jquery.validate.min.js', 'rmcommon', ['footer' => 1]);
        \RMTemplate::getInstance()->add_inline_script('$(document).ready(function(){
        	$("#rmc-comment-form").validate({
        		messages: {
        			comment_name: "' . __('Please specify your name', 'rmcommon') . '",
        			comment_email: "' . __('Please specify a valid email', 'rmcommon') . '",
        			comment_text: "' . __('Please write a message', 'rmcommon') . '",
        			comment_url: "' . __('Please enter a valid URL', 'rmcommon') . '"
        		}
        	});
        });', 1);

        $xoopsTpl->assign('cf', $form);

        return $xoopsTpl->fetch(RMCPATH . '/templates/rmc-comments-form.tpl');
    }

    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
