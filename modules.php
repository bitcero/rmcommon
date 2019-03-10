<?php
// $Id: modules.php 965 2012-05-28 03:18:09Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require_once dirname(__DIR__) . '/../include/cp_header.php';
$common->location = 'modules';

/**
 * function to update compiled template file in templates_c folder
 *
 * @param string $tpl_id
 * @param bool $clear_old
 * @return bool
 */
function modules_template_touch($tpl_id, $clear_old = true)
{
    $tplfileHandler =  xoops_getHandler('tplfile');
    $tplfile = $tplfileHandler->get($tpl_id);

    if (is_object($tplfile)) {
        $file = $tplfile->getVar('tpl_file', 'n');
        $tpl = new XoopsTpl();

        $tpl->plugins_dir = RMEvents::get()->trigger('rmcommon.smarty.plugins', $tpl->plugins_dir);

        return $tpl->touch('db:' . $file);
    }

    return false;
}

function modules_install_function($dirname)
{
    global $xoopsUser, $xoopsConfig;
    $dirname = trim($dirname);
    $db = $GLOBALS['xoopsDB'];
    $reservedTables = ['avatar', 'avatar_users_link', 'block_module_link', 'xoopscomments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups', 'groups_users_link', 'group_permission', 'online', 'bannerclient', 'banner', 'bannerfinish', 'priv_msgs', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'modules', 'tplfile', 'tplset', 'tplsource', 'xoopsnotifications', 'banner', 'bannerclient', 'bannerfinish'];
    $moduleHandler = xoops_getHandler('module');
    if (0 == $moduleHandler->getCount(new Criteria('dirname', $dirname))) {
        $module = $moduleHandler->create();
        $module->loadInfoAsVar($dirname);
        $module->setVar('weight', 1);
        $module->setVar('isactive', 1);
        $module->setVar('last_update', time());
        $error = false;
        $errs = [];
        $msgs = [];

        $msgs[] = '<div id="xo-module-log"><div class="header">';
        $msgs[] = $errs[] = '<h4>' . _AM_SYSTEM_MODULES_INSTALLING . $module->getInfo('name', 's') . '</h4>';
        if (false !== $module->getInfo('image') && '' != trim($module->getInfo('image'))) {
            $msgs[] = '<a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/' . $module->getInfo('adminindex') . '"><img src="' . XOOPS_URL . '/modules/' . $dirname . '/' . trim($module->getInfo('image')) . '" alt=""></a>';
        }
        $msgs[] = '<strong>' . _VERSION . ':</strong> ' . $module->getInfo('version') . '&nbsp;' . $module->getInfo('module_status');
        if (false !== $module->getInfo('author') && '' != trim($module->getInfo('author'))) {
            $msgs[] = '<strong>' . _AUTHOR . ':</strong> ' . htmlspecialchars(trim($module->getInfo('author')));
        }
        $msgs[] = '</div><div class="logger">';
        // Load module specific install script if any
        $install_script = $module->getInfo('onInstall');
        if ($install_script && '' != trim($install_script)) {
            require_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($install_script);
        }
        $func = "xoops_module_pre_install_{$dirname}";
        // If pre install function is defined, execute
        if (function_exists($func)) {
            $result = $func($module);
            if (!$result) {
                $error = true;
                $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                $errs = array_merge($errs, $module->getErrors());
            } else {
                $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>{$func}</strong>") . '</p>';
                $msgs += $module->getErrors();
            }
        }

        if (false === $error) {
            $sqlfile = $module->getInfo('sqlfile');
            if (is_array($sqlfile) && !empty($sqlfile[XOOPS_DB_TYPE])) {
                $sql_file_path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . $sqlfile[XOOPS_DB_TYPE];
                if (!file_exists($sql_file_path)) {
                    $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_SQL_NOT_FOUND, "<strong>{$sql_file_path}</strong>");
                    $error = true;
                } else {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_SQL_FOUND, "<strong>{$sql_file_path}</strong>") . '<br >' . _AM_SYSTEM_MODULES_CREATE_TABLES;
                    require_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
                    $sql_query = fread(fopen($sql_file_path, 'r'), filesize($sql_file_path));
                    $sql_query = trim($sql_query);
                    SqlUtility::splitMySqlFile($pieces, $sql_query);
                    $created_tables = [];
                    foreach ($pieces as $piece) {
                        // [0] contains the prefixed query
                        // [4] contains unprefixed table name
                        $prefixed_query = SqlUtility::prefixQuery($piece, $db->prefix());
                        if (!$prefixed_query) {
                            $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_SQL_NOT_VALID, '<strong>' . $piece . '</strong>');
                            $error = true;
                            break;
                        }
                        // check if the table name is reserved
                        if (!in_array($prefixed_query[4], $reservedTables, true)) {
                            // not reserved, so try to create one
                            if (!$db->query($prefixed_query[0])) {
                                $errs[] = $db->error();
                                $error = true;
                                break;
                            }
                            if (!in_array($prefixed_query[4], $created_tables, true)) {
                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TABLE_CREATED, '<strong>' . $db->prefix($prefixed_query[4]) . '</strong>');
                                $created_tables[] = $prefixed_query[4];
                            } else {
                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_INSERT_DATA, '<strong>' . $db->prefix($prefixed_query[4]) . '</strong>');
                            }
                        } else {
                            // the table name is reserved, so halt the installation
                            $errs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TABLE_RESERVED, '<strong>' . $prefixed_query[4] . '</strong>');
                            $error = true;
                            break;
                        }
                    }
                    // if there was an error, delete the tables created so far, so the next installation will not fail
                    if (true === $error) {
                        foreach ($created_tables as $ct) {
                            $db->query('DROP TABLE ' . $db->prefix($ct));
                        }
                    }
                }
            }
        }
        // if no error, save the module info and blocks info associated with it
        if (false === $error) {
            if (!$moduleHandler->insert($module)) {
                $errs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_INSERT_DATA_FAILD, '<strong>' . $module->getVar('name') . '</strong>');
                foreach ($created_tables as $ct) {
                    $db->query('DROP TABLE ' . $db->prefix($ct));
                }
                $ret = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILINS, '<strong>' . $module->name() . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>';
                foreach ($errs as $err) {
                    $ret .= ' - ' . $err . '<br>';
                }
                $ret .= '</p>';
                unset($module);
                unset($created_tables);
                unset($errs);
                unset($msgs);

                return $ret;
            }
            $newmid = $module->getVar('mid');
            unset($created_tables);
            $msgs[] = '<p>' . _AM_SYSTEM_MODULES_INSERT_DATA_DONE . sprintf(_AM_SYSTEM_MODULES_MODULEID, '<strong>' . $newmid . '</strong>');
            $tplfileHandler = xoops_getHandler('tplfile');
            $templates = $module->getInfo('templates');
            if (false !== $templates) {
                $msgs[] = _AM_SYSTEM_MODULES_TEMPLATES_ADD;
                foreach ($templates as $tpl) {
                    $tplfile = $tplfileHandler->create();
                    $type = (isset($tpl['type']) ? $tpl['type'] : 'module');
                    $tpldata = &xoops_module_gettemplate($dirname, $tpl['file'], $type);
                    $tplfile->setVar('tpl_source', $tpldata, true);
                    $tplfile->setVar('tpl_refid', $newmid);

                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $tpl['file']);
                    $tplfile->setVar('tpl_desc', $tpl['description'], true);
                    $tplfile->setVar('tpl_module', $dirname);
                    $tplfile->setVar('tpl_lastmodified', time());
                    $tplfile->setVar('tpl_lastimported', time());
                    $tplfile->setVar('tpl_type', $type);
                    if (!$tplfileHandler->insert($tplfile)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                    } else {
                        $newtplid = $tplfile->getVar('tpl_id');
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_DATA, '<strong>' . $tpl['file'] . '</strong>') . '(ID: <strong>' . $newtplid . '</strong>)';
                        // generate compiled file
                        require_once XOOPS_ROOT_PATH . '/class/template.php';
                        if (!modules_template_touch($newtplid)) {
                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED_FAILED, '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                        } else {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED, '<strong>' . $tpl['file'] . '</strong>');
                        }
                    }
                    unset($tplfile, $tpldata);
                }
            }
            require_once XOOPS_ROOT_PATH . '/class/template.php';
            xoops_template_clear_module_cache($newmid);
            $blocks = $module->getInfo('blocks');
            if (false !== $blocks) {
                $msgs[] = _AM_SYSTEM_MODULES_BLOCKS_ADD;
                foreach ($blocks as $blockkey => $block) {
                    // break the loop if missing block config
                    if (!isset($block['file']) || !isset($block['show_func'])) {
                        break;
                    }
                    $options = '';
                    if (!empty($block['options']) && is_string($block['options'])) {
                        $options = trim($block['options']);
                    }
                    $newbid = $db->genId($db->prefix('newblocks') . '_bid_seq');
                    $edit_func = isset($block['edit_func']) ? trim($block['edit_func']) : '';
                    $template = '';
                    if (isset($block['template']) && '' != trim($block['template'])) {
                        $content = &xoops_module_gettemplate($dirname, $block['template'], 'blocks');
                    }
                    if (empty($content)) {
                        $content = '';
                    } else {
                        $template = trim($block['template']);
                    }
                    $block_name = addslashes(trim($block['name']));
                    $sql = 'INSERT INTO ' . $db->prefix('newblocks') . " (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified) VALUES ($newbid, $newmid, " . (int)$blockkey
                               . ", '$options', '" . $block_name . "','" . $block_name . "', '', 0, 0, 0, 'M', 'H', 1, '" . addslashes($dirname) . "', '" . addslashes(trim($block['file'])) . "', '" . addslashes(trim($block['show_func'])) . "', '" . addslashes($edit_func) . "', '" . $template . "', 0, " . time() . ')';
                    if (!$db->query($sql)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_BLOCK_ADD_ERROR, '<strong>' . $block['name'] . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ADD_ERROR_DATABASE, '<strong>' . $db->error() . '</strong>') . '</span>';
                    } else {
                        if (empty($newbid)) {
                            $newbid = $db->getInsertId();
                        }
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_BLOCK_ADD, '<strong>' . $block['name'] . '</strong>') . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $newbid . '</strong>');
                        $sql = 'INSERT INTO ' . $db->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newbid . ', -1)';
                        $db->query($sql);
                        if ('' != $template) {
                            $tplfile = $tplfileHandler->create();
                            $tplfile->setVar('tpl_refid', $newbid);
                            $tplfile->setVar('tpl_source', $content, true);
                            $tplfile->setVar('tpl_tplset', 'default');
                            $tplfile->setVar('tpl_file', $block['template']);
                            $tplfile->setVar('tpl_module', $dirname);
                            $tplfile->setVar('tpl_type', 'block');
                            $tplfile->setVar('tpl_desc', $block['description'], true);
                            $tplfile->setVar('tpl_lastimported', 0);
                            $tplfile->setVar('tpl_lastmodified', time());
                            if (!$tplfileHandler->insert($tplfile)) {
                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_ERROR, '<strong>' . $block['template'] . '</strong>') . '</span>';
                            } else {
                                $newtplid = $tplfile->getVar('tpl_id');
                                $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_ADD_DATA, '<strong>' . $block['template'] . '</strong>') . ' (ID: <strong>' . $newtplid . '</strong>)';
                                // generate compiled file
                                require_once XOOPS_ROOT_PATH . '/class/template.php';
                                if (!xoops_template_touch($newtplid)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED_FAILED, '<strong>' . $block['template'] . '</strong>') . '</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_TEMPLATE_COMPILED, '<strong>' . $block['template'] . '</strong>');
                                }
                            }
                            unset($tplfile);
                        }
                    }
                    unset($content);
                }
                unset($blocks);
            }
            $configs = $module->getInfo('config');
            if (false !== $configs) {
                if (0 != $module->getVar('hascomments')) {
                    require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                    array_push($configs, ['name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => ['_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN]]);
                    array_push($configs, ['name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0]);
                }
            } else {
                if (0 != $module->getVar('hascomments')) {
                    $configs = [];
                    require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                    $configs[] = ['name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => ['_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN]];
                    $configs[] = ['name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0];
                }
            }
            // RMV-NOTIFY
            if (0 != $module->getVar('hasnotification')) {
                if (empty($configs)) {
                    $configs = [];
                }
                // Main notification options
                require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
                $options = [];
                $options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
                $options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
                $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
                $options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;

                $configs[] = ['name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLE', 'description' => '_NOT_CONFIG_ENABLEDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => XOOPS_NOTIFICATION_ENABLEBOTH, 'options' => $options];
                // Event-specific notification options
                // FIXME: doesn't work when update module... can't read back the array of options properly...  " changing to &quot;
                $options = [];
                $categories = &notificationCategoryInfo('', $module->getVar('mid'));
                foreach ($categories as $category) {
                    $events = &notificationEvents($category['name'], false, $module->getVar('mid'));
                    foreach ($events as $event) {
                        if (!empty($event['invisible'])) {
                            continue;
                        }
                        $option_name = $category['title'] . ' : ' . $event['title'];
                        $option_value = $category['name'] . '-' . $event['name'];
                        $options[$option_name] = $option_value;
                    }
                    unset($events);
                }
                unset($categories);
                $configs[] = ['name' => 'notification_events', 'title' => '_NOT_CONFIG_EVENTS', 'description' => '_NOT_CONFIG_EVENTSDSC', 'formtype' => 'select_multi', 'valuetype' => 'array', 'default' => array_values($options), 'options' => $options];
            }

            if (false !== $configs) {
                $msgs[] = _AM_SYSTEM_MODULES_MODULE_DATA_ADD;
                $configHandler = xoops_getHandler('config');
                $order = 0;
                foreach ($configs as $config) {
                    $confobj = $configHandler->createConfig();
                    $confobj->setVar('conf_modid', $newmid);
                    $confobj->setVar('conf_catid', 0);
                    $confobj->setVar('conf_name', $config['name']);
                    $confobj->setVar('conf_title', $config['title'], true);
                    $confobj->setVar('conf_desc', isset($config['description']) ? $config['description'] : '', true);
                    $confobj->setVar('conf_formtype', $config['formtype']);
                    $confobj->setVar('conf_valuetype', $config['valuetype']);
                    $confobj->setConfValueForInput($config['default'], true);
                    $confobj->setVar('conf_order', $order);
                    $confop_msgs = '';
                    if (isset($config['options']) && is_array($config['options'])) {
                        foreach ($config['options'] as $key => $value) {
                            $confop = $configHandler->createConfigOption();
                            $confop->setVar('confop_name', $key, true);
                            $confop->setVar('confop_value', $value, true);
                            $confobj->setConfOptions($confop);
                            $confop_msgs .= '<br>&nbsp;&nbsp;&nbsp;&nbsp; ' . _AM_SYSTEM_MODULES_CONFIG_ADD . _AM_SYSTEM_MODULES_NAME . ' <strong>' . (defined($key) ? constant($key) : $key) . '</strong> ' . _AM_SYSTEM_MODULES_VALUE . ' <strong>' . $value . '</strong> ';
                            unset($confop);
                        }
                    }
                    $order++;
                    if (false !== $configHandler->insertConfig($confobj)) {
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD, '<strong>' . $config['name'] . '</strong>') . $confop_msgs;
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_CONFIG_DATA_ADD_ERROR, '<strong>' . $config['name'] . '</strong>') . '</span>';
                    }
                    unset($confobj);
                }
                unset($configs);
            }

            if ($module->getInfo('hasMain')) {
                $groups = [XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS];
            } else {
                $groups = [XOOPS_GROUP_ADMIN];
            }
            // retrieve all block ids for this module
            $blocks = XoopsBlock::getByModule($newmid, false);
            $msgs[] = _AM_SYSTEM_MODULES_GROUP_SETTINGS_ADD;
            $gpermHandler = xoops_getHandler('groupperm');
            foreach ($groups as $mygroup) {
                if ($gpermHandler->checkRight('module_admin', 0, $mygroup)) {
                    $mperm = $gpermHandler->create();
                    $mperm->setVar('gperm_groupid', $mygroup);
                    $mperm->setVar('gperm_itemid', $newmid);
                    $mperm->setVar('gperm_name', 'module_admin');
                    $mperm->setVar('gperm_modid', 1);
                    if (!$gpermHandler->insert($mperm)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_ACCESS_ADMIN_ADD_ERROR, '<strong>' . $mygroup . '</strong>') . '</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_ACCESS_ADMIN_ADD, '<strong>' . $mygroup . '</strong>');
                    }
                    unset($mperm);
                }
                $mperm = $gpermHandler->create();
                $mperm->setVar('gperm_groupid', $mygroup);
                $mperm->setVar('gperm_itemid', $newmid);
                $mperm->setVar('gperm_name', 'module_read');
                $mperm->setVar('gperm_modid', 1);
                if (!$gpermHandler->insert($mperm)) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(_AM_SYSTEM_MODULES_ACCESS_USER_ADD_ERROR, '<strong>' . $mygroup . '</strong>') . '</span>';
                } else {
                    $msgs[] = '&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_ACCESS_USER_ADD_ERROR, '<strong>' . $mygroup . '</strong>');
                }
                unset($mperm);
                foreach ($blocks as $blc) {
                    $bperm = $gpermHandler->create();
                    $bperm->setVar('gperm_groupid', $mygroup);
                    $bperm->setVar('gperm_itemid', $blc);
                    $bperm->setVar('gperm_name', 'block_read');
                    $bperm->setVar('gperm_modid', 1);
                    if (!$gpermHandler->insert($bperm)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . _AM_SYSTEM_MODULES_BLOCK_ACCESS_ERROR . ' Block ID: <strong>' . $blc . '</strong> Group ID: <strong>' . $mygroup . '</strong></span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;' . _AM_SYSTEM_MODULES_BLOCK_ACCESS . sprintf(_AM_SYSTEM_MODULES_BLOCK_ID, '<strong>' . $blc . '</strong>') . sprintf(_AM_SYSTEM_MODULES_GROUP_ID, '<strong>' . $mygroup . '</strong>');
                    }
                    unset($bperm);
                }
            }
            unset($blocks);
            unset($groups);

            // execute module specific install script if any
            $func = "xoops_module_install_{$dirname}";
            if (function_exists($func)) {
                if (!$lastmsg = $func($module)) {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_EXECUTE, $func) . '</p>';
                } else {
                    $msgs[] = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILED_SUCESS, "<strong>{$func}</strong>") . '</p>';
                    if (is_string($lastmsg)) {
                        $msgs[] = $lastmsg;
                    }
                }
            }

            $msgs[] = sprintf(_AM_SYSTEM_MODULES_OKINS, '<strong>' . $module->getVar('name', 's') . '</strong>');
            $msgs[] = '</div></div>';

            $blocks = $module->getInfo('blocks');
            $msgs[] = '<div class="noininstall center"><a href="admin.php?fct=modulesadmin">' . _AM_SYSTEM_MODULES_BTOMADMIN . '</a> |
                        <a href="admin.php?fct=modulesadmin&op=installlist">' . _AM_SYSTEM_MODULES_TOINSTALL . '</a> | ';
            $msgs[] = '<br><span class="red bold">' . _AM_SYSTEM_MODULES_MODULE . ' ' . $module->getInfo('name') . ': </span></div>';
            if (false !== $blocks) {
                $msgs[] = '<div class="center"><a href="admin.php?fct=blocksadmin&op=list&filter=1&selgen=' . $newmid . '&selmod=-2&selgrp=-1&selvis=-1&filsave=1">' . _AM_SYSTEM_BLOCKS . '</a></div>';
            }
            $msgs[] = '<div class="noininstall center"><a href="admin.php?fct=preferences&op=showmod&mod=' . $newmid . '">' . _AM_SYSTEM_PREF . '</a>';
            $msgs[] = '<a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/' . $module->getInfo('adminindex') . '">' . _AM_SYSTEM_MODULES_ADMIN . '</a>';

            $testdataDirectory = XOOPS_ROOT_PATH . '/modules/' . $module->getInfo('dirname', 'e') . '/testdata';
            if (file_exists($testdataDirectory)) {
                $msgs[] = '<a href="' . XOOPS_URL . '/modules/' . $module->getInfo('dirname', 'e') . '/testdata/index.php' . '">' . _AM_SYSTEM_MODULES_INSTALL_TESTDATA . '</a></div>';
            } else {
                $msgs[] = '</div>';
            }

            $ret = implode('<br>', $msgs);
            unset($blocks);
            unset($msgs);
            unset($errs);
            unset($module);

            return $ret;
        }
        $ret = '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILINS, '<strong>' . $dirname . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>' . implode('<br>', $errs) . '</p>';
        unset($msgs);
        unset($errs);

        return $ret;
    }

    return '<p>' . sprintf(_AM_SYSTEM_MODULES_FAILINS, '<strong>' . $dirname . '</strong>') . '&nbsp;' . _AM_SYSTEM_MODULES_ERRORSC . '<br>&nbsp;&nbsp;' . sprintf(_AM_SYSTEM_MODULES_ALEXISTS, $dirname) . '</p>';
}

/**
 * Parse module authors
 *
 * @param $mod
 *
 * @return array
 */
function get_authors($mod)
{
    if (is_array($mod->getInfo('author'))) {
        return $mod->getInfo('author');
    }
    if ($mod->getInfo('rmnative')) {
        $author = [[
                'name' => $mod->getInfo('author'),
                'email' => $mod->getInfo('authormail'),
                'web' => $mod->getInfo('authorweb'),
                'url' => $mod->getInfo('url'),
                'aka' => $mod->getInfo('aka'),
            ]];

        return $author;
    }
    if ('' != $mod->getInfo('author')) {
        $persons = explode(',', $mod->getInfo('author'));
    } else {
        $persons = explode(',', $mod->getInfo('credits'));
    }

    $authors = [];
    foreach ($persons as $person) {
        $authors[] = [
                    'name' => $person,
                    'email' => '',
                    'web' => 'XOOPS',
                    'url' => 'http://xoops.org',
                    'aka' => $person,
                ];
    }

    return $authors;
}

/**
 * Parse social icons
 * @param mixed $type
 */
function parse_social_icons($type)
{
    $icons = [
        'twitter' => 'fa fa-twitter-square',
        'facebook' => 'fa fa-facebook-square',
        'linkedin' => 'fa fa-linkedin-square',
        'instagram' => 'fa fa-instagram',
        'google' => 'fa fa-google-plus-square',
        'flickr' => 'fa fa-flickr',
        'pinterest' => 'fa fa-pinterest-square',
        'blog' => 'fa fa-wordpress',
        'youtube' => 'fa fa-youtube-square',
        'github' => 'fa fa-github',
        'medium' => 'fa fa-medium',
        'slideshare' => 'fa fa-slideshare',
        'tumblr' => 'fa fa-tumblr-square',
        'vimeo' => 'fa fa-vimeo-square',
        'default' => 'fa fa-external-link-square',
    ];

    if (array_key_exists($type, $icons)) {
        return $icons[$type];
    }

    return $icons['default'];
}

function show_modules_list()
{
    global $xoopsSecurity, $cuIcons;

    $installed_modules = [];

    $limit = rmc_server_var($_SESSION, 'mods_limit', 4);

    require_once XOOPS_ROOT_PATH . '/kernel/module.php';

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = 'SELECT * FROM ' . $db->prefix('modules') . ' ORDER BY `name`';
    $result = $db->query($sql);
    $installed_dirs = [];

    while (false !== ($row = $db->fetchArray($result))) {
        $mod = new XoopsModule();
        $mod->assignVars($row);
        $installed_dirs[] = $mod->dirname();

        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $mod->getVar('dirname') . '/class/' . mb_strtolower($mod->getVar('dirname') . 'controller') . '.php')) {
            require_once XOOPS_ROOT_PATH . '/modules/' . $mod->getVar('dirname') . '/class/' . mb_strtolower($mod->getVar('dirname') . 'controller') . '.php';
            $class = ucfirst($mod->getVar('dirname')) . 'Controller';
            $class = new $class();
            if (method_exists($class, 'get_main_link')) {
                $main_link = $class->get_main_link();
            } else {
                if ($mod->getVar('hasmain')) {
                    $main_link = XOOPS_URL . '/modules/' . $mod->dirname();
                } else {
                    $main_link = '#';
                }
            }
        } else {
            if ($mod->getVar('hasmain')) {
                $main_link = XOOPS_URL . '/modules/' . $mod->dirname();
            } else {
                $main_link = '#';
            }
        }

        // Admin section
        $admin_link = $mod->getVar('hasadmin') ? XOOPS_URL . '/modules/' . $mod->dirname() . '/' . $mod->getInfo('adminindex') : '';
        $icon = '' != $mod->getInfo('icon') ? $cuIcons->getIcon($mod->getInfo('icon')) : (XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . ($mod->getInfo('icon48') ? $mod->getInfo('icon48') : $mod->getInfo('image')));

        $modules[] = [
            'id' => $mod->getVar('mid'),
            'name' => $mod->getVar('name'),
            'realname' => $mod->getInfo('name'),
            'version' => $mod->getInfo('rmnative') ? RMModules::format_module_version($mod->getInfo('rmversion')) : $mod->getInfo('version'),
            'description' => $mod->getInfo('description'),
            'icon' => $icon,
            'image' => XOOPS_URL . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('image'),
            'link' => $main_link,
            'admin_link' => $admin_link,
            'updated' => formatTimestamp($mod->getVar('last_update'), 's'),
            'url' => $mod->getInfo('url'),
            'authors' => get_authors($mod),
            //'avatar'        => RMEvents::get()->run_event('rmcommon.get.avatar', $mod->getInfo('authormail'), 80),
            'license' => $mod->getInfo('license'),
            'dirname' => $mod->getInfo('dirname'),
            'active' => $mod->getVar('isactive'),
            'help' => $mod->getInfo('help'),
            'social' => $mod->getInfo('social'),
        ];
    }

    // Event for installed modules
    $modules = RMEvents::get()->trigger('rmcommon.installed.modules', $modules, $installed_dirs);

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
    $dirlist = XoopsLists::getModulesList();
    $available_mods = [];
    $moduleHandler = xoops_getHandler('module');
    foreach ($dirlist as $file) {
        clearstatcache();
        $file = trim($file);
        if (!in_array($file, $installed_dirs, true)) {
            $module = $moduleHandler->create();
            if (!$module->loadInfo($file, false)) {
                continue;
            }

            $available_mods[] = [
                'name' => $module->getInfo('name'),
                'version' => $module->getInfo('rmnative') ? RMModules::format_module_version($module->getInfo('rmversion')) : $module->getInfo('version'),
                'description' => $module->getInfo('description'),
                'image' => XOOPS_URL . '/modules/' . $module->getInfo('dirname') . '/' . $module->getInfo('image'),
                'authors' => get_authors($module),
                'license' => $module->getInfo('license'),
                'dirname' => $module->getInfo('dirname'),
                'help' => $module->getInfo('help'),
                'social' => $module->getInfo('social'),
            ];
            unset($module);
        }
    }

    // Event for available modules
    $available_mods = RMEvents::get()->trigger('rmcommon.available.modules', $available_mods);

    RMBreadCrumb::get()->add_crumb(__('Modules Management', 'rmcommon'));

    ////RMFunctions::create_toolbar();
    RMTemplate::get()->assign('xoops_pagetitle', __('Modules Management', 'rmcommon'));
    RMTemplate::get()->add_style('modules.min.css', 'rmcommon');
    RMTemplate::get()->add_script('modules.min.js', 'rmcommon');
    RMTemplate::get()->set_help('http://www.redmexico.com.mx/docs/common-utilities/uso-de-common-utilities/standalone/1/#administrador-de-modulos');
    xoops_cp_header();
    include RMTemplate::get()->path('rmc-modules.php', 'module', 'rmcommon');
    xoops_cp_footer();
}

/**
 * This function show a resume of the changes that will be made by module
 */
function module_install()
{
    global $xoopsSecurity, $rmTpl;

    $dir = rmc_server_var($_GET, 'dir', '');

    if ('' == $dir || !file_exists(XOOPS_ROOT_PATH . '/modules/' . $dir . '/xoops_version.php')) {
        redirectMsg('modules.php', __('Specified module is not valid!', 'rmcommon'), 1);
        die();
    }

    $moduleHandler = xoops_getHandler('module');

    if ($module = $moduleHandler->getByDirname($dir)) {
        redirectMsg('modules.php', sprintf(__('%s is already installed!', 'rmcommon'), $module->name()), 1);
        die();
    }

    $module = $moduleHandler->create();
    if (!$module->loadInfo($dir, false)) {
        redirectMsg('modules.php', __('Sepecified module is not a valid Xoops Module!', 'rmcommon'), 1);
        die();
    }

    $module = RMEvents::get()->run_event('rmcommon.preinstall.module', $module);

    RMTEmplate::get()->add_script('modules.min.js', 'rmcommon');
    RMTemplate::get()->add_style('modules.min.css', 'rmcommon');
    //RMFunctions::create_toolbar();

    RMBreadCrumb::get()->add_crumb(__('Modules Management', 'rmcommon'), 'modules.php');
    RMBreadCrumb::get()->add_crumb(sprintf(__('Install %s', 'rmcommon'), $module->getInfo('name')));

    $rmTpl->assign('xoops_pagetitle', sprintf(__('Install %s', 'rmcommon'), $module->getInfo('name')));

    xoops_cp_header();

    include RMTemplate::get()->get_template('rmc-module-preinstall.php', 'module', 'rmcommon');

    xoops_cp_footer();
}

function module_install_now()
{
    global $xoopsSecurity, $xoopsConfig;

    $mod = RMHttpRequest::post('module', 'string', '');

    if (!$xoopsSecurity->check()) {
        redirectMsg('modules.php?action=install&dir=' . $mod, __('Sorry, this operation could not be completed!', 'rmcommon'), 1);
        die();
    }

    $moduleHandler = xoops_getHandler('module');

    if ($module = $moduleHandler->getByDirname($mod)) {
        redirectMsg('modules.php', sprintf(__('%s is already installed!', 'rmcommon'), $module->name()), 1);
        die();
    }

    xoops_loadLanguage('admin', 'system');

    // Include system language file
    $file = XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        require_once str_replace($xoopsConfig['language'], 'english', $file);
    }

    // Include system language file
    $file = XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin/modulesadmin.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        require_once str_replace($xoopsConfig['language'], 'english', $file);
    }

    require_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';

    $module_log = modules_install_function($mod); //xoops_module_install($mod);

    RMEvents::get()->run_event('rmcommon.installing.module', $mod);

    $module_log = RMEvents::get()->run_event('rmcommon.module.installed', $module_log, $mod);

    //RMFunctions::create_toolbar();
    RMTemplate::get()->add_style('modules.min.css', 'rmcommon');
    xoops_cp_header();

    $module = $moduleHandler->getByDirname($mod);
    $log_title = sprintf(__('Installation log for %s', 'rmcommon'), $module ? $module->name() : $mod);
    $action = rmc_server_var($_POST, 'action', '');
    include RMTemplate::get()->get_template('rmc-modules-log.php', 'module', 'rmcommon');

    xoops_cp_footer();
}

function module_uninstall_now()
{
    global $xoopsSecurity, $xoopsConfig, $rmTpl, $xoopsDB;

    $dir = RMHttpRequest::post('module', 'string', '');

    if (!$xoopsSecurity->check()) {
        redirectMsg('modules.php', __('Sorry, this operation could not be completed!', 'rmcommon'), 1);
        die();
    }

    $moduleHandler = xoops_getHandler('module');

    if (!$mod = $moduleHandler->getByDirname($dir)) {
        redirectMsg('modules.php', sprintf(__('Module %s is not installed yet!', 'rmcommon'), $mod), 1);
        die();
    }

    $file = XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin/modulesadmin.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        require_once str_replace($xoopsConfig['language'], 'english', $file);
    }

    require_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';

    RMEvents::get()->run_event('rmcommon.uninstalling.module', $mod);

    $module_log = xoops_module_uninstall($dir);

    // Remove comments
    $sql = 'DELETE FROM ' . $xoopsDB->prefix('mod_rmcommon_comments') . " WHERE id_obj='$dir'";
    if ($xoopsDB->queryF($sql)) {
        $module_log .= '<br><span class="text-success"><span class="fa fa-check"></span> Comments deleted successfully!</span>';
    } else {
        $module_log .= '<br><span class="text-danger"><span class="fa fa-exclamation-triangle"></span> Comments could not be deleted: ' . $xoopsDB->error() . '</span>';
    }

    // Remove notifications
    $sql = 'DELETE FROM ' . $xoopsDB->prefix('mod_rmcommon_notifications') . " WHERE element='$dir' AND `type`='module'";
    if ($xoopsDB->queryF($sql)) {
        $module_log .= '<br><span class="text-success"><span class="fa fa-check"></span> Notifications deleted successfully!</span>';
    } else {
        $module_log .= '<br><span class="text-danger"><span class="fa fa-exclamation-triangle"></span> Notifications could not be deleted: ' . $xoopsDB->error() . '</span>';
    }

    $module_log = RMEvents::get()->run_event('rmcommon.module.uninstalled', $module_log, $mod);

    //RMFunctions::create_toolbar();
    RMTemplate::get()->add_style('modules.min.css', 'rmcommon');

    RMBreadCrumb::get()->add_crumb(__('Modules Management', 'rmcommon'), 'modules.php');
    RMBreadCrumb::get()->add_crumb(sprintf(__('%s install log', 'rmcommon'), $mod->getVar('name')));

    $rmTpl->assign('xoops_pagetitle', sprintf(__('%s install log', 'rmcommon'), $mod->getVar('name')));

    xoops_cp_header();

    $module = new XoopsModule();
    $module->loadInfo($mod, false);
    $log_title = sprintf(__('Uninstall log for %s', 'rmcommon'), $module ? $module->getInfo('name') : $mod);
    $action = rmc_server_var($_POST, 'action', '');
    include RMTemplate::get()->get_template('rmc-modules-log.php', 'module', 'rmcommon');

    xoops_cp_footer();
}

function module_update($dirname)
{
    global $xoopsConfig, $xoopsDB;

    $dirname = trim($dirname);
    $moduleHandler = xoops_getHandler('module');
    $module = $moduleHandler->getByDirname($dirname);
    // Save current version for use in the update function
    $prev_version = $module->getVar('version');
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl = new XoopsTpl();
    $xoopsTpl->clearCache($dirname);
    //xoops_template_clear_module_cache($module->getVar('mid'));
    // we dont want to change the module name set by admin
    $temp_name = $module->getVar('name');
    $module->loadInfoAsVar($dirname);
    $module->setVar('name', $temp_name);

    $log = '';
    if (!$moduleHandler->insert($module)) {
        $log .= sprintf(__('Could not update %s', 'rmcommon'), $module->getVar('name'));
    } else {
        $newmid = $module->getVar('mid');
        $msgs = [];
        $msgs[] = sprintf(__('Updating module %s', 'rmcommon'), $module->getVar('name'));
        $tplfileHandler = xoops_getHandler('tplfile');
        $deltpl = $tplfileHandler->find('default', 'module', $module->getVar('mid'));
        $delng = [];
        if (is_array($deltpl)) {
            // delete template file entry in db
            $dcount = count($deltpl);
            for ($i = 0; $i < $dcount; $i++) {
                if (!$tplfileHandler->delete($deltpl[$i])) {
                    $delng[] = $deltpl[$i]->getVar('tpl_file');
                }
            }
        }
        $templates = $module->getInfo('templates');
        if (false !== $templates) {
            $msgs[] = __('Updating templates...', 'rmcommon');
            foreach ($templates as $tpl) {
                $tpl['file'] = trim($tpl['file']);
                if (!in_array($tpl['file'], $delng, true)) {
                    $tpldata = &xoops_module_gettemplate($dirname, $tpl['file']);
                    $tplfile = $tplfileHandler->create();
                    $tplfile->setVar('tpl_refid', $newmid);
                    $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_lastmodified', time());
                    if (preg_match("/\.css$/i", $tpl['file'])) {
                        $tplfile->setVar('tpl_type', 'css');
                    } else {
                        $tplfile->setVar('tpl_type', 'module');
                    }
                    $tplfile->setVar('tpl_source', $tpldata, true);
                    $tplfile->setVar('tpl_module', $dirname);
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $tpl['file'], true);
                    $tplfile->setVar('tpl_desc', $tpl['description'], true);
                    if (!$tplfileHandler->insert($tplfile)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('Template %s could not be inserted!', 'rmcommon'), '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                    } else {
                        $newid = $tplfile->getVar('tpl_id');
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Template %s inserted to the database.', 'rmcommon'), '<strong>' . $tpl['file'] . '</strong>');
                        if ('default' == $xoopsConfig['template_set']) {
                            if (!modules_template_touch($newid)) {
                                $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not recompile template %s', 'rmcommon'), '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                            } else {
                                $msgs[] = '&nbsp;&nbsp;<span>' . sprintf(__('Template %s recompiled', 'rmcommon'), '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                            }
                        }
                    }
                    unset($tpldata);
                } else {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not delete old template %s. Aborting update of this file.', 'rmcommon'), '<strong>' . $tpl['file'] . '</strong>') . '</span>';
                }
            }
        }
        $blocks = $module->getInfo('blocks');
        $msgs[] = __('Rebuilding blocks...', 'rmcommon');
        if (false !== $blocks) {
            $showfuncs = [];
            $funcfiles = [];
            foreach ($blocks as $i => $block) {
                if (isset($block['show_func']) && '' != $block['show_func'] && isset($block['file']) && '' != $block['file']) {
                    $editfunc = isset($block['edit_func']) ? $block['edit_func'] : '';
                    $showfuncs[] = $block['show_func'];
                    $funcfiles[] = $block['file'];
                    $template = '';
                    if (isset($block['template']) && '' != trim($block['template'])) {
                        $content = &xoops_module_gettemplate($dirname, $block['template'], 'blocks');
                    }

                    if (!$content) {
                        $content = '';
                    } else {
                        $template = $block['template'];
                    }
                    $options = '';
                    if (!empty($block['options'])) {
                        $options = $block['options'];
                    }
                    $sql = 'SELECT bid, name FROM ' . $xoopsDB->prefix('newblocks') . ' WHERE mid=' . $module->getVar('mid') . ' AND func_num=' . $i . " AND show_func='" . addslashes($block['show_func']) . "' AND func_file='" . addslashes($block['file']) . "'";
                    $fresult = $xoopsDB->query($sql);
                    $fcount = 0;
                    while (false !== ($fblock = $xoopsDB->fetchArray($fresult))) {
                        $fcount++;
                        $sql = 'UPDATE ' . $xoopsDB->prefix('newblocks') . " SET name='" . addslashes($block['name']) . "', edit_func='" . addslashes($editfunc) . "', content='', template='" . $template . "', last_modified=" . time() . ' WHERE bid=' . $fblock['bid'];
                        $result = $xoopsDB->query($sql);
                        if (!$result) {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(__('ERROR: Could not update %s'), $fblock['name']);
                        } else {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Block %s updated.', 'rmcommon'), $fblock['name']) . sprintf(__('Block ID: %s', 'rmcommon'), '<strong>' . $fblock['bid'] . '</strong>');
                            if ('' != $template) {
                                $tplfile = $tplfileHandler->find('default', 'block', $fblock['bid']);
                                if (0 == count($tplfile)) {
                                    $tplfile_new = $tplfileHandler->create();
                                    $tplfile_new->setVar('tpl_module', $dirname);
                                    $tplfile_new->setVar('tpl_refid', $fblock['bid']);
                                    $tplfile_new->setVar('tpl_tplset', 'default');
                                    $tplfile_new->setVar('tpl_file', $block['template'], true);
                                    $tplfile_new->setVar('tpl_type', 'block');
                                } else {
                                    $tplfile_new = $tplfile[0];
                                }
                                $tplfile_new->setVar('tpl_source', $content, true);
                                $tplfile_new->setVar('tpl_desc', $block['description'], true);
                                $tplfile_new->setVar('tpl_lastmodified', time());
                                $tplfile_new->setVar('tpl_lastimported', 0);
                                if (!$tplfileHandler->insert($tplfile_new)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not update %s template.', 'rmcommon'), '<strong>' . $block['template'] . '</strong>') . '</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Template %s updated.', 'rmcommon'), '<strong>' . $block['template'] . '</strong>');
                                    if ('default' == $xoopsConfig['template_set']) {
                                        if (!modules_template_touch($tplfile_new->getVar('tpl_id'))) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not recompile template %s', 'rmcommon'), '<strong>' . $block['template'] . '</strong>') . '</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Template %s recompiled', 'rmcommon'), '<strong>' . $block['template'] . '</strong>');
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (0 == $fcount) {
                        $newbid = $xoopsDB->genId($xoopsDB->prefix('newblocks') . '_bid_seq');
                        $block_name = addslashes($block['name']);
                        $block_type = ('system' == $module->getVar('dirname')) ? 'S' : 'M';
                        $sql = 'INSERT INTO ' . $xoopsDB->prefix('newblocks') . ' (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, isactive, dirname, func_file, show_func, edit_func, template, last_modified) VALUES (' . $newbid . ', ' . $module->getVar('mid') . ', ' . $i . ",'" . addslashes($options) . "','" . $block_name . "', '" . $block_name . "', '', 0, 0, 0, '{$block_type}', 1, '" . addslashes($dirname) . "', '" . addslashes($block['file']) . "', '" . addslashes($block['show_func']) . "', '" . addslashes($editfunc) . "', '" . $template . "', " . time() . ')';
                        $result = $xoopsDB->query($sql);
                        if (!$result) {
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(_('ERROR: Could not create %s', 'rmcommon'), $block['name']);
                            $log .= $sql;
                        } else {
                            if (empty($newbid)) {
                                $newbid = $xoopsDB->getInsertId();
                            }
                            if ($module->getInfo('hasMain')) {
                                $groups = [XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS];
                            } else {
                                $groups = [XOOPS_GROUP_ADMIN];
                            }
                            $gpermHandler = xoops_getHandler('groupperm');
                            foreach ($groups as $mygroup) {
                                $bperm = $gpermHandler->create();
                                $bperm->setVar('gperm_groupid', $mygroup);
                                $bperm->setVar('gperm_itemid', $newbid);
                                $bperm->setVar('gperm_name', 'block_read');
                                $bperm->setVar('gperm_modid', 1);
                                if (!$gpermHandler->insert($bperm)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . __('ERROR: Could not add block access right', 'rmcommon') . ' ' . sprintf(__('Block ID: %s', 'rmcommon'), '<strong>' . $newbid . '</strong>') . ' ' . sprintf(__('Group ID: %s', 'rmcommon'), '<strong>' . $mygroup . '</strong>') . '</span>';
                                } else {
                                    $msgs[] = '&nbsp;&nbsp;' . __('Added block access right', 'rmcommon') . ' ' . sprintf(__('Block ID: %s', 'rmcommon'), '<strong>' . $newbid . '</strong>') . ' ' . sprintf(__('Group ID: %s', 'rmcommon'), '<strong>' . $mygroup . '</strong>');
                                }
                            }

                            if ('' != $template) {
                                $tplfile = $tplfileHandler->create();
                                $tplfile->setVar('tpl_module', $dirname);
                                $tplfile->setVar('tpl_refid', $newbid);
                                $tplfile->setVar('tpl_source', $content, true);
                                $tplfile->setVar('tpl_tplset', 'default');
                                $tplfile->setVar('tpl_file', $block['template'], true);
                                $tplfile->setVar('tpl_type', 'block');
                                $tplfile->setVar('tpl_lastimported', 0);
                                $tplfile->setVar('tpl_lastmodified', time());
                                $tplfile->setVar('tpl_desc', $block['description'], true);
                                if (!$tplfileHandler->insert($tplfile)) {
                                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not insert template %s to the database.', 'rmcommon'), '<strong>' . $block['template'] . '</strong>') . '</span>';
                                } else {
                                    $newid = $tplfile->getVar('tpl_id');
                                    $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Template %s added to the database', 'rmcommon'), '<strong>' . $block['template'] . '</strong>');
                                    if ('default' == $xoopsConfig['template_set']) {
                                        if (!modules_template_touch($newid)) {
                                            $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Template %s recompile failed', 'rmcommon'), '<strong>' . $block['template'] . '</strong>') . '</span>';
                                        } else {
                                            $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Template %s recompiled', 'rmcommon'), '<strong>' . $block['template'] . '</strong>');
                                        }
                                    }
                                }
                            }
                            $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Block %s created', 'rmcommon'), '<strong>' . $block['name'] . '</strong>') . sprintf(__('Block ID: %s', 'rmcommon'), '<strong>' . $newbid . '</strong>');
                            $sql = 'INSERT INTO ' . $xoopsDB->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newbid . ', -1)';
                            $xoopsDB->query($sql);
                        }
                    }
                }
            }
            $block_arr = XoopsBlock::getByModule($module->getVar('mid'));
            foreach ($block_arr as $block) {
                if (!in_array($block->getVar('show_func'), $showfuncs, true) || !in_array($block->getVar('func_file'), $funcfiles, true)) {
                    $sql = sprintf('DELETE FROM %s WHERE bid = %u', $xoopsDB->prefix('newblocks'), $block->getVar('bid'));
                    if (!$xoopsDB->query($sql)) {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not delete block %s', 'rmcommon'), '<strong>' . $block->getVar('name') . '</strong>') . sprintf(__('Block ID: %s', 'rmcommon'), '<strong>' . $block->getVar('bid') . '</strong>') . '</span>';
                    } else {
                        $msgs[] = '&nbsp;&nbsp;Block <strong>' . $block->getVar('name') . ' deleted. Block ID: <strong>' . $block->getVar('bid') . '</strong>';
                        if ('' != $block->getVar('template')) {
                            $tplfiles = $tplfileHandler->find(null, 'block', $block->getVar('bid'));
                            if (is_array($tplfiles)) {
                                $btcount = count($tplfiles);
                                for ($k = 0; $k < $btcount; $k++) {
                                    if (!$tplfileHandler->delete($tplfiles[$k])) {
                                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . __('ERROR: Could not remove deprecated block template.', 'rmcommon') . '(ID: <strong>' . $tplfiles[$k]->getVar('tpl_id') . '</strong>)</span>';
                                    } else {
                                        $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Block template %s deprecated', 'rmcommon'), '<strong>' . $tplfiles[$k]->getVar('tpl_file') . '</strong>');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // reset compile_id
        $xoopsTpl->setCompileId();

        // first delete all config entries
        $configHandler = xoops_getHandler('config');
        $configs = $configHandler->getConfigs(new Criteria('conf_modid', $module->getVar('mid')));
        $confcount = count($configs);
        $config_delng = [];
        if ($confcount > 0) {
            $msgs[] = __('Deleting module config options...', 'rmcommon');
            for ($i = 0; $i < $confcount; $i++) {
                if (!$configHandler->deleteConfig($configs[$i])) {
                    $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . __('ERROR: Could not delete config data from the database', 'rmcommon') . sprintf(__('Config ID: %s', 'rmcommon'), '<strong>' . $configs[$i]->getvar('conf_id') . '</strong>') . '</span>';
                    // save the name of config failed to delete for later use
                    $config_delng[] = $configs[$i]->getvar('conf_name');
                } else {
                    $config_old[$configs[$i]->getvar('conf_name')]['value'] = $configs[$i]->getvar('conf_value', 'N');
                    $config_old[$configs[$i]->getvar('conf_name')]['formtype'] = $configs[$i]->getvar('conf_formtype');
                    $config_old[$configs[$i]->getvar('conf_name')]['valuetype'] = $configs[$i]->getvar('conf_valuetype');
                    $msgs[] = '&nbsp;&nbsp;' . __('Config data deleted from the database.', 'rmcommon') . ' ' . sprintf(__('Config ID: %s', 'rmcommon'), '<strong>' . $configs[$i]->getVar('conf_id') . '</strong>');
                }
            }
        }

        // now reinsert them with the new settings
        $configs = $module->getInfo('config');
        // Include
        if (false !== $configs) {
            if (0 != $module->getVar('hascomments')) {
                require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                array_push($configs, ['name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => ['_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN]]);
                array_push($configs, ['name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0]);
            }
        } else {
            if (0 != $module->getVar('hascomments')) {
                $configs = [];
                require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
                $configs[] = ['name' => 'com_rule', 'title' => '_CM_COMRULES', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1, 'options' => ['_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN]];
                $configs[] = ['name' => 'com_anonpost', 'title' => '_CM_COMANONPOST', 'description' => '', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 0];
            }
        }
        // RMV-NOTIFY
        if (0 != $module->getVar('hasnotification')) {
            if (empty($configs)) {
                $configs = [];
            }
            // Main notification options
            require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
            require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
            $options = [];
            $options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
            $options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
            $options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
            $options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;

            //$configs[] = array ('name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLED', 'description' => '_NOT_CONFIG_ENABLEDDSC', 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
            $configs[] = ['name' => 'notification_enabled', 'title' => '_NOT_CONFIG_ENABLE', 'description' => '_NOT_CONFIG_ENABLEDSC', 'formtype' => 'select', 'valuetype' => 'int', 'default' => XOOPS_NOTIFICATION_ENABLEBOTH, 'options' => $options];
            // Event specific notification options
            // FIXME: for some reason the default doesn't come up properly
            //  initially is ok, but not when 'update' module..
            $options = [];
            $categories = &notificationCategoryInfo('', $module->getVar('mid'));
            foreach ($categories as $category) {
                $events = &notificationEvents($category['name'], false, $module->getVar('mid'));
                foreach ($events as $event) {
                    if (!empty($event['invisible'])) {
                        continue;
                    }
                    $option_name = $category['title'] . ' : ' . $event['title'];
                    $option_value = $category['name'] . '-' . $event['name'];
                    $options[$option_name] = $option_value;
                    //$configs[] = array ('name' => notificationGenerateConfig($category,$event,'name'), 'title' => notificationGenerateConfig($category,$event,'title_constant'), 'description' => notificationGenerateConfig($category,$event,'description_constant'), 'formtype' => 'yesno', 'valuetype' => 'int', 'default' => 1);
                }
            }
            $configs[] = ['name' => 'notification_events', 'title' => '_NOT_CONFIG_EVENTS', 'description' => '_NOT_CONFIG_EVENTSDSC', 'formtype' => 'select_multi', 'valuetype' => 'array', 'default' => array_values($options), 'options' => $options];
        }

        if (false !== $configs) {
            $msgs[] = 'Adding module config data...';
            $configHandler = xoops_getHandler('config');
            $order = 0;
            foreach ($configs as $config) {
                // only insert ones that have been deleted previously with success
                if (!in_array($config['name'], $config_delng, true)) {
                    $confobj = $configHandler->createConfig();
                    $confobj->setVar('conf_modid', $newmid);
                    $confobj->setVar('conf_catid', 0);
                    $confobj->setVar('conf_name', $config['name']);
                    $confobj->setVar('conf_title', $config['title'], true);
                    $confobj->setVar('conf_desc', $config['description'], true);
                    $confobj->setVar('conf_formtype', $config['formtype']);
                    $confobj->setVar('conf_valuetype', $config['valuetype']);
                    if (isset($config_old[$config['name']]['value']) && $config_old[$config['name']]['formtype'] == $config['formtype'] && $config_old[$config['name']]['valuetype'] == $config['valuetype']) {
                        // preserver the old value if any
                        // form type and value type must be the same
                        $confobj->setVar('conf_value', $config_old[$config['name']]['value'], true);
                    } else {
                        $confobj->setConfValueForInput($config['default'], true);

                        //$confobj->setVar('conf_value', $config['default'], true);
                    }
                    $confobj->setVar('conf_order', $order);
                    $confop_msgs = '';
                    if (isset($config['options']) && is_array($config['options'])) {
                        foreach ($config['options'] as $key => $value) {
                            $confop = $configHandler->createConfigOption();
                            $confop->setVar('confop_name', $key, true);
                            $confop->setVar('confop_value', $value, true);
                            $confobj->setConfOptions($confop);
                            $confop_msgs .= '<br>&nbsp;&nbsp;&nbsp;&nbsp; ' . __('Config option added', 'rmcommon') . ' ' . __('Name:', 'rmcommon') . ' <strong>' . (defined($key) ? constant($key) : $key) . '</strong> ' . __('Value:', 'rmcommon') . ' <strong>' . $value . '</strong> ';
                            unset($confop);
                        }
                    }
                    $order++;
                    if (false !== $configHandler->insertConfig($confobj)) {
                        //$msgs[] = '&nbsp;&nbsp;Config <strong>'.$config['name'].'</strong> added to the database.'.$confop_msgs;
                        $msgs[] = '&nbsp;&nbsp;' . sprintf(__('Config %s added to the database', 'rmcommon'), '<strong>' . $config['name'] . '</strong>') . $confop_msgs;
                    } else {
                        $msgs[] = '&nbsp;&nbsp;<span style="color:#ff0000;">' . sprintf(__('ERROR: Could not insert config %s to the database.', 'rmcommon'), '<strong>' . $config['name'] . '</strong>') . '</span>';
                    }
                    unset($confobj);
                }
            }
            unset($configs);
        }

        // execute module specific update script if any
        $update_script = $module->getInfo('onUpdate');
        if (false !== $update_script && '' != trim($update_script)) {
            require_once XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . trim($update_script);
            if (function_exists('xoops_module_update_' . $dirname)) {
                $func = 'xoops_module_update_' . $dirname;
                if (!$func($module, $prev_version)) {
                    $msgs[] = '<p>' . sprintf(__('Failed to execute %s', 'rmcommon'), $func) . '</p>';
                } else {
                    $msgs[] = '<p>' . sprintf(__('%s executed successfully.', 'rmcommon'), '<strong>' . $func . '</strong>') . '</p>';
                }
            }
        }

        foreach ($msgs as $msg) {
            $log .= $msg . '<br>';
        }
        $log .= '<p>' . sprintf(__('Module %s updated successfully!', 'rmcommon'), '<strong>' . $module->getVar('name') . '</strong>') . '</p>';
    }

    // Flush cache files for cpanel GUIs
    xoops_load('cpanel', 'system');
    XoopsSystemCpanel::flush();

    return $log;
}

function module_update_now()
{
    global $xoopsSecurity, $xoopsConfig, $rmTpl;

    $mod = rmc_server_var($_POST, 'module', '');

    if (!$xoopsSecurity->check()) {
        redirectMsg('modules.php', __('Sorry, this operation could not be completed!', 'rmcommon'), 1);
        die();
    }

    $moduleHandler = xoops_getHandler('module');
    $module = $moduleHandler->getByDirname($mod);

    if (!$module) {
        redirectMsg('modules.php', sprintf(__('Module %s is not installed yet!', 'rmcommon'), $mod), 1);
        die();
    }

    // Include system language file
    $file = XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        require_once str_replace($xoopsConfig['language'], 'english', $file);
    }

    // Include system language file
    $file = XOOPS_ROOT_PATH . '/modules/system/language/' . $xoopsConfig['language'] . '/admin/modulesadmin.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        require_once str_replace($xoopsConfig['language'], 'english', $file);
    }

    require_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';

    RMEvents::get()->run_event('rmcommon.updating.module', $module);

    $module_log = module_update($mod);

    $module_log = RMEvents::get()->run_event('rmcommon.module.updated', $module_log, $module_log);

    //RMFunctions::create_toolbar();
    RMTemplate::get()->add_style('modules.min.css', 'rmcommon');
    xoops_cp_header();

    $log_title = sprintf(__('Update log for %s', 'rmcommon'), $module ? $module->getInfo('name') : $mod);

    RMBreadCrumb::get()->add_crumb(__('Modules Management', 'rmcommon'), 'modules.php');
    RMBreadCrumb::get()->add_crumb($log_title);

    $rmTpl->assign('xoops_pagetitle', $log_title);

    $action = rmc_server_var($_POST, 'action', '');
    include RMTemplate::get()->get_template('rmc-modules-log.php', 'module', 'rmcommon');

    xoops_cp_footer();
}

function module_disable_now($enable = 0)
{
    global $xoopsSecurity, $xoopsConfig;

    $mod = rmc_server_var($_POST, 'module', '');

    if (!$xoopsSecurity->check()) {
        redirectMsg('modules.php', __('Sorry, this operation could not be completed!', 'rmcommon'), 1);
        die();
    }

    $moduleHandler = xoops_getHandler('module');

    if (!$module = $moduleHandler->getByDirname($mod)) {
        redirectMsg('modules.php', sprintf(__('Module %s is not installed yet!', 'rmcommon'), $mod), 1);
        die();
    }

    RMEvents::get()->run_event('rmcommon.disabling.module', $module);

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'UPDATE ' . $db->prefix('modules') . " SET isactive='$enable' WHERE dirname='$mod'";
    if (!$db->queryF($sql)) {
        redirectMsg('modules.php', sprintf(__('Module %s could not be disabled!', 'rmcommon'), $mod), 1);
        die();
    }

    RMEvents::get()->run_event('rmcommon.module.disabled', $module);

    redirectMsg('modules.php', sprintf(__('Module %s successfully disabled!', 'rmcommon'), $module->name()), 0);
}

function load_modules_page()
{
    global $xoopsLogger, $xoopsSecurity;
    error_reporting(0);
    $xoopsLogger->activated = false;

    if (!$xoopsSecurity->check(true, rmc_server_var($_POST, 'token', ''))) {
        echo __("Sorry, you don't have access to this page", 'rmcommon');
        echo "<br><a href='javascript:;' onclick='location.reload();'>" . __('Click here to refresh', 'rmcommon') . '</a>';
        die();
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'SELECT * FROM ' . $db->prefix('modules') . ' ORDER BY `name`';
    $result = $db->query($sql);
    $installed_dirs = [];

    while (false !== ($row = $db->fetchArray($result))) {
        $installed_dirs[] = $row['dirname'];
    }

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
    $dirlist = XoopsLists::getModulesList();
    $available_mods = [];
    $moduleHandler = xoops_getHandler('module');
    foreach ($dirlist as $file) {
        clearstatcache();
        $file = trim($file);
        if (!in_array($file, $installed_dirs, true)) {
            $module = $moduleHandler->create();
            if (!$module->loadInfo($file, false)) {
                continue;
            }
            $available_mods[] = $module;
            unset($module);
        }
    }

    unset($dirlist);
    unset($moduleHandler);

    $limit = 7;
    $tpages = ceil(count($available_mods) / $limit);
    $page = rmc_server_var($_POST, 'page', 1);
    if ($page > $tpages) {
        $page = 1;
    }
    $start = ($page <= 0 ? 0 : $page - 1) * $limit;

    $nav = new RMPageNav(count($available_mods), $limit, $page, 3);
    $nav->target_url('javascript:;" onclick="load_page({PAGE_NUM});');

    // Event for available modules
    $available_mods = RMEvents::get()->run_event('rmcommon.available.modules', $available_mods);
    $end = ($page * $limit);

    if ($end > count($available_mods)) {
        $end = count($available_mods);
    }

    ob_start(); ?>
    <ul class="list-unstyled">
        <?php for ($i = $start; $i < $end; $i++): ?>
        <?php $mod = $available_mods[$i]; ?>
            <li>
                <div class="the-logo">
                    <?php if ('' != $mod->getInfo('url')): ?>
                        <a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>">
                            <img src="<?php echo XOOPS_URL; ?>/modules/<?php echo $mod->getInfo('dirname'); ?>/<?php echo $mod->getInfo('image'); ?>" alt="<?php echo $mod->getInfo('dirname'); ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo XOOPS_URL; ?>/modules/<?php echo $mod->getInfo('dirname'); ?>/<?php echo $mod->getInfo('image'); ?>" alt="<?php echo $mod->getInfo('dirname'); ?>">
                    <?php endif; ?>
                </div>
                <div class="the-info">
                    <ul>
                        <li class="name">
                            <strong><a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>"><?php echo $mod->getInfo('name'); ?></a></strong>
                            <small><?php echo $mod->getInfo('rmversion') ? RMFormat::version($mod->getInfo('rmversion')) : $mod->getInfo('version'); ?></small>
                        </li>
                        <li class="install">
                            <a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>">
                                <span class="fa fa-cog"></span> <?php _e('Install', 'rmcommon'); ?>
                            </a>
                        </li>
                        <li class="info">
                            <a href="javascript:;" onclick="show_module_info('<?php echo $mod->getInfo('dirname'); ?>');">
                                <span class="fa fa-info-circle"></span>
                                <?php _e('Info', 'rmcommon'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="rmc_mod_info" id="mod-<?php echo $mod->getInfo('dirname'); ?>">
                    <div class="header">
                        <div class="logo">
                            <img src="<?php echo XOOPS_URL; ?>/modules/<?php echo $mod->getInfo('dirname'); ?>/<?php echo $mod->getInfo('image'); ?>" alt="<?php echo $mod->getInfo('dirname'); ?>">
                        </div>
                        <div class="name">
                            <h4><?php echo $mod->getInfo('name'); ?></h4>
                                    <span class="help-block">
                                        <?php echo $mod->getInfo('description'); ?>
                                    </span>
                        </div>
                    </div>
                    <table class="table">
                        <tr>
                            <td><?php _e('Version:', 'rmcommon'); ?></td>
                            <td>
                                <?php if ($mod->getInfo('rmnative')): ?>
                                    <strong><?php echo RMModules::format_module_version($mod->getInfo('rmversion')); ?></strong>
                                <?php else: ?>
                                    <strong><?php echo $mod->getInfo('version'); ?></strong>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php _e('Author:', 'rmcommon'); ?>
                            </td>
                            <td>
                                <strong><?php echo strip_tags($mod->getInfo('author')); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php _e('Web site:', 'rmcommon'); ?>
                            </td>
                            <td>
                                <a target="_blank" href="<?php echo $mod->getInfo('authorurl'); ?>"><?php echo $mod->getInfo('authorweb'); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Updatable:', 'rmcommon'); ?></td>
                            <td>
                                <?php if ('' != $mod->getInfo('updateurl')): ?>
                                    <span class="fa fa-check"></span>
                                <?php else: ?>
                                    <span class="fa fa-times text-danger"></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('License:', 'rmcommon'); ?></td>
                            <td>
                                <?php echo $mod->getInfo('license'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('XOOPS Official:', 'rmcommon'); ?></td>
                            <td>
                                <?php if ($mod->getInfo('official')): ?>
                                    <span class="fa fa-check"></span>
                                <?php else: ?>
                                    <span class="fa fa-times text-danger"></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('C.U. Native:', 'rmcommon'); ?></td>
                            <td>
                                <?php if ($mod->getInfo('rmnative')): ?>
                                    <span class="fa fa-check"></span>
                                <?php else: ?>
                                    <span class="fa fa-times text-danger"></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Directory:', 'rmcommon'); ?></td>
                            <td>
                                <strong><?php echo $mod->getInfo('dirname'); ?></strong>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Released:', 'rmcommon'); ?></td>
                            <td>
                                <?php if ('' != $mod->getInfo('releasedate')): ?>
                                    <?php
                                    $time = strtotime($mod->getInfo('releasedate'));
    echo formatTimestamp($time, 's'); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ('' != $mod->getInfo('help') && $mod->getInfo('rmnative')): ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>
                                    <strong><a href="<?php echo $mod->getInfo('help'); ?>" target="_blank"><?php _e('Get Help', 'rmcommon'); ?></a></strong>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="2" class="contact-options text-center">
                                <?php if ($mod->getInfo('authormail')): ?>
                                    <?php if ('' != $mod->getInfo('authormail')): ?>
                                        <a target="_blank" href="mailto:<?php echo $mod->getInfo('authormail'); ?>"><span class="fa fa-envelope"></span></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($mod->getInfo('social')): ?>
                                    <?php foreach ($mod->getInfo('social') as $social): ?>
                                        <a target="_blank" href="<?php echo $social['url']; ?>"><span class="fa fa-<?php echo $social['type']; ?>-square"></span></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">
                                <a href="modules.php?action=install&amp;dir=<?php echo $mod->getInfo('dirname'); ?>" class="btn btn-success btn-sm"><?php _e('Install', 'rmcommon'); ?></a>
                                <a href="#" onclick="closeInfo();" class="btn btn-warning btn-sm"><?php _e('Close', 'rmcommon'); ?></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </li>
        <?php endfor; ?>
    </ul>
    <?php $nav->display(false); ?>
    <input type="hidden" id="token" value="<?php echo $xoopsSecurity->createToken(); ?>">
<?php
    $ret = ob_get_clean();
    echo $ret;
}

function module_rename()
{
    global $xoopsSecurity, $xoopsLogger;

    error_reporting(0);
    $xoopsLogger->activated = false;

    if (!$xoopsSecurity->check()) {
        $ret = [
            'error' => 1,
            'message' => __('You can not rename modules. Session token not valid!', 'rmcommon') . ' ' . rmc_server_var($_POST, 'token'),
        ];

        echo json_encode($ret);
        die();
    }

    $id = rmc_server_var($_POST, 'id', 0);
    $name = trim(rmc_server_var($_POST, 'name', ''));

    if ($id <= 0 || '' == $name) {
        $ret = [
            'error' => 1,
            'message' => __('Data provided is not valid', 'rmcommon'),
            'token' => $xoopsSecurity->createToken(),
        ];

        echo json_encode($ret);
        die();
    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'UPDATE ' . $db->prefix('modules') . " SET `name`='$name' WHERE mid='$id'";
    if (!$db->queryF($sql)) {
        $ret = [
            'error' => 1,
            'message' => __('Module name could not be changed!', 'rmcommon') . '\n' . $db->error(),
            'token' => $xoopsSecurity->createToken(),
        ];

        echo json_encode($ret);
        die();
    }

    $ret = [
        'error' => 0,
        'message' => __('Module name changed successfully!', 'rmcommon'),
        'token' => $xoopsSecurity->createToken(),
        'id' => $id,
    ];

    echo json_encode($ret);

    die();
}

$action = rmc_server_var($_REQUEST, 'action', '');
switch ($action) {
    case 'install':
        module_install();
        break;
    case 'install_now':
        module_install_now();
        break;
    case 'uninstall_module':
        module_uninstall_now();
        break;
    case 'update_module':
        module_update_now();
        break;
    case 'disable_module':
        module_disable_now(0);
        break;
    case 'enable_module':
        module_disable_now(1);
        break;
    case 'load_page':
        load_modules_page();
        break;
    case 'savename':
        module_rename();
        break;
    default:
        $_REQUEST['action'] = '';
        show_modules_list();
        break;
}
