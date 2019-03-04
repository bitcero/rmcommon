<?php
// $Id: comments.php 1015 2012-08-23 05:36:42Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortes <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function rmc_bkcomments_show($options)
{
    global $cuServices;

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = 'SELECT * FROM ' . $db->prefix('mod_rmcommon_comments') . ' ORDER BY id_com DESC';
    $limit = $options[0] > 0 ? $options[0] : 10;
    $sql .= " LIMIT 0,$limit";
    $result = $db->query($sql);
    $comments = [];

    $ucache = [];
    $ecache = [];
    $mods = [];

    while (false !== ($row = $db->fetchArray($result))) {
        $com = new RMComment();
        $com->assignVars($row);

        if ($options[3]) {
            // Editor data
            if (!isset($ecache[$com->getVar('user')])) {
                $ecache[$com->getVar('user')] = new RMCommentUser($com->getVar('user'));
            }

            $editor = $ecache[$com->getVar('user')];

            if ($editor->getVar('xuid') > 0) {
                if (!isset($ucache[$editor->getVar('xuid')])) {
                    $ucache[$editor->getVar('xuid')] = new XoopsUser($editor->getVar('xuid'));
                }

                $user = $ucache[$editor->getVar('xuid')];

                $poster = [
                    'id' => $user->getVar('uid'),
                    'name' => $user->getVar('uname'),
                    'email' => $user->getVar('email'),
                    'posts' => $user->getVar('posts'),
                    'avatar' => $cuServices->avatar->getAvatarSrc($user->getVar('email'), 100),
                    'rank' => $user->rank(),
                ];
            } else {
                $poster = [
                    'id' => 0,
                    'name' => $editor->getVar('name'),
                    'email' => $editor->getVar('email'),
                    'posts' => 0,
                    'avatar' => RMCURL . '/images/avatar.gif',
                    'rank' => '',
                ];
            }
        }

        // Get item
        $cpath = XOOPS_ROOT_PATH . '/modules/' . $row['id_obj'] . '/class/' . $row['id_obj'] . 'controller.php';

        if (is_file($cpath)) {
            if (!class_exists(ucfirst($row['id_obj']) . 'Controller')) {
                require_once $cpath;
            }

            $class = ucfirst($row['id_obj']) . 'Controller';
            $controller = new $class();
            $item = $controller->get_item($row['params'], $com);
            $item_url = $controller->get_item_url($row['params'], $com);
        } else {
            $item = __('Unknow', 'rmcommon');
            $item_url = '';
        }

        if (isset($mods[$row['id_obj']])) {
            $mod = $mods[$row['id_obj']];
        } else {
            $m = RMModules::load_module($row['id_obj']);
            $mod = $m->getVar('name');
            $mods[$row['id_obj']] = $mod;
        }

        $comments[] = [
            'id' => $row['id_com'],
            'text' => TextCleaner::truncate(TextCleaner::getInstance()->clean_disabled_tags(TextCleaner::getInstance()->popuplinks(TextCleaner::getInstance()->nofollow($com->getVar('content')))), 50),
            'poster' => isset($poster) ? $poster : null,
            'posted' => formatTimestamp($com->getVar('posted'), 'l'),
            'item' => $item,
            'item_url' => $item_url,
            'module' => $row['id_obj'],
            'status' => $com->getVar('status'),
            'module' => $mod,
        ];
    }

    $comments = RMEvents::get()->run_event('rmcommon.loading.block.comments', $comments);
    $block['comments'] = $comments;
    $block['show_module'] = $options[1];
    $block['show_name'] = $options[2];
    $block['show_user'] = $options[3];
    $block['show_date'] = $options[4];

    $num = $options[2] + $options[3] + $options[4];
    $block['data_width'] = floor(100 / $num);

    RMTemplate::get()->add_style('bk_comments.css', 'rmcommon');

    return $block;
}

function rmc_bkcomments_edit($options)
{
    $form = '<div class="form-horizontal">';

    ob_start(); ?>
    <div class="control-group">
        <label class="control-label" for="number-comments"><?php _e('Number of Comments:', 'rmcommon'); ?></label>
        <div class="controls">
            <input type="text" size="5" name="options[0]" value="<?php echo $options[0]; ?>" id="number-comments">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label"><?php _e('Show module name:', 'rmcommon'); ?></label>
        <div class="controls">
            <label class="radio inline">
                <input type="radio" name="options[1]" value="1"'<?php echo 1 == $options[1] ? ' checked' : ''; ?>><?php _e('Yes', 'rmcommon'); ?>
            </label>
            <label class="radio inline">
                <input type="radio" name="options[1]" value="1"'<?php echo 0 == $options[1] ? ' checked' : ''; ?>><?php _e('No', 'rmcommon'); ?>
            </label>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label"><?php _e('Show item name:', 'rmcommon'); ?></label>
        <div class="controls">
            <label class="radio inline">
                <input type="radio" name="options[2]" value="1"'<?php echo 1 == $options[2] ? ' checked' : ''; ?>><?php _e('Yes', 'rmcommon'); ?>
            </label>
            <label class="radio inline">
                <input type="radio" name="options[2]" value="1"'<?php echo 0 == $options[2] ? ' checked' : ''; ?>><?php _e('No', 'rmcommon'); ?>
            </label>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label"><?php _e('Show username:', 'rmcommon'); ?></label>
        <div class="controls">
            <label class="radio inline">
                <input type="radio" name="options[3]" value="1"'<?php echo 1 == $options[3] ? ' checked' : ''; ?>><?php _e('Yes', 'rmcommon'); ?>
            </label>
            <label class="radio inline">
                <input type="radio" name="options[3]" value="1"'<?php echo 0 == $options[3] ? ' checked' : ''; ?>><?php _e('No', 'rmcommon'); ?>
            </label>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label"><?php _e('Show date:', 'rmcommon'); ?></label>
        <div class="controls">
            <label class="radio inline">
                <input type="radio" name="options[4]" value="1"'<?php echo 1 == $options[4] ? ' checked' : ''; ?>><?php _e('Yes', 'rmcommon'); ?>
            </label>
            <label class="radio inline">
                <input type="radio" name="options[4]" value="1"'<?php echo 0 == $options[4] ? ' checked' : ''; ?>><?php _e('No', 'rmcommon'); ?>
            </label>
        </div>
    </div>
    <?php
    $form .= ob_get_clean();

    $form .= '</div>';

    return $form;
}
