<?php
// $Id: functions.php 1071 2012-09-22 23:45:24Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require_once RMCPATH . '/class/helpers/settings.class.php';
require_once RMCPATH . '/class/helpers/modules.class.php';
require_once RMCPATH . '/class/helpers/uris.class.php';

class RMFunctions
{
    public $settings = '';
    public $modules = '';
    public $uris = '';

    public function __construct()
    {
        $this->settings = new RMSettings();
        $this->modules = new RMModules();
        $this->uris = new RMUris();
    }

    public static function get()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Check the number of images category on database
     * @param mixed $table
     * @param mixed $filters
     */
    public static function get_num_records($table, $filters = '')
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $sql = 'SELECT COUNT(*) FROM ' . $db->prefix($table);
        $sql .= '' != $filters ? " WHERE $filters" : '';

        list($num) = $db->fetchRow($db->query($sql));

        return $num;
    }

    /**
     * Create the module toolbar. This function must be called only from rmcommon module administration
     */
    public static function create_toolbar()
    {
        global $common;

        if ('users' == $common->location) {
            RMTemplate::getInstance()->add_tool(
                __('Users', 'rmcommon'),
                'users.php',
                'icon icon-users',
                'allusers',
                ['class' => 'cu-tool tool-users-list']
            );

            RMTemplate::getInstance()->add_tool(
                __('Add', 'rmcommon'),
                'users.php?action=new',
                'icon icon-plus',
                'newuser',
                ['class' => 'cu-tool tool-user-add']
            );
        } elseif ('groups' == $common->location) {
            RMTemplate::getInstance()->add_tool(
                __('Groups', 'rmcommon'),
                'groups.php',
                'icon icon-users',
                '',
                [
                    'class' => 'cu-tool tool-groups',
                ]
            );

            RMTemplate::getInstance()->add_tool(
                __('Add', 'rmcommon'),
                '#',
                'icon icon-plus',
                'newgroup',
                [
                    'class' => 'cu-tool tool-group-add',
                    'data-action' => 'load-remote-dialog',
                    'data-url' => 'groups.php?action=new-group',
                    'data-parameters' => '{action: \'new-group\'}',
                ]
            );

            /*
             * Next buttons are available only when groups list is shown
             */
            RMTemplate::getInstance()->add_tool(
                __('Edit', 'rmcommon'),
                '#',
                'icon icon-pencil',
                'editgroup',
                [
                    'class' => 'cu-tool tool-group-edit',
                    'data-activator' => 'groups-list',
                    'data-oncount' => '== 1',
                    'data-action' => 'groupsController.edit',
                    'disabled' => 'disabled',
                    'title' => __('Edit Group', 'rmcommon'),
                ]
            );

            RMTemplate::getInstance()->add_tool(
                __('Delete', 'rmcommon'),
                '#',
                'icon icon-bin',
                'deletegroup',
                [
                    'class' => 'cu-tool tool-group-delete',
                    'data-activator' => 'groups-list',
                    'data-oncount' => '> 0',
                    'disabled' => 'disabled',
                    'title' => __('Delete Groups', 'rmcommon'),
                    'data-action' => 'groupsController.delete',
                ]
            );
        } elseif ('imgmanager' == $common->location) {
            RMTemplate::getInstance()->add_tool(
                __('Categories', 'rmcommon'),
                'images.php?action=showcats',
                'svg-rmcommon-folder text-orange',
                'showcategories',
                ['class' => 'cu-tool tool-categories-images']
            );
            RMTemplate::getInstance()->add_tool(
                __('New', 'rmcommon'),
                'images.php?action=newcat',
                'svg-rmcommon-folder-plus text-orange',
                'newcategory',
                ['class' => 'cu-tool tool-category-add']
            );
            $cat = rmc_server_var($_REQUEST, 'category', 0);
            if ($cat > 0) {
                RMTemplate::getInstance()->add_tool(
                    __('Images', 'rmcommon'),
                    'images.php?category=' . $cat,
                    'svg-rmcommon-camera',
                    'showimages',
                    ['class' => 'cu-tool tool-images']
                );
            }
            RMTemplate::getInstance()->add_tool(
                __('Add', 'rmcommon'),
                'images.php?action=new' . ($cat > 0 ? "&amp;category=$cat" : ''),
                'svg-rmcommon-camera-plus text-info',
                'addimages',
                ['class' => 'cu-tool tool-images-add']
            );
        } else {
            RMTemplate::getInstance()->add_tool(__('Dashboard', 'rmcommon'), 'index.php', '', 'dashboard', ['class' => 'cu-tool tool-dashboard']);
            RMTemplate::getInstance()->add_tool(__('Modules', 'rmcommon'), 'modules.php', '', 'modules', ['class' => 'cu-tool tool-modules']);
            RMTemplate::getInstance()->add_tool(__('Blocks', 'rmcommon'), 'blocks.php', '', 'blocks', ['class' => 'cu-tool tool-blocks']);
            RMTemplate::getInstance()->add_tool(__('Groups', 'rmcommon'), 'groups.php', '', 'groups', ['class' => 'cu-tool tool-groups']);
            RMTemplate::getInstance()->add_tool(__('Users', 'rmcommon'), 'users.php', '', 'users', ['class' => 'cu-tool tool-users']);
            RMTemplate::getInstance()->add_tool(__('Images', 'rmcommon'), 'images.php', '', 'imgmanager', ['class' => 'cu-tool tool-images']);
            RMTemplate::getInstance()->add_tool(__('Comments', 'rmcommon'), 'comments.php', '', 'comments', ['class' => 'cu-tool tool-comments']);
            RMTemplate::getInstance()->add_tool(__('Plugins', 'rmcommon'), 'plugins.php', '', 'plugins', ['class' => 'cu-tool tool-plugins']);
            RMTemplate::getInstance()->add_tool(__('Updates', 'rmcommon'), 'updates.php', '', 'updates', ['class' => 'cu-tool tool-updates']);
        }

        RMEvents::get()->run_event('rmcommon.create.toolbar');
    }

    /**
     * This functions allows to get the groups names for a single category
     * @param array Groups ids
     * @param bool Return as list
     * @param mixed $groups
     * @param mixed $list
     * @return array|list
     */
    public static function get_groups_names($groups, $list = true)
    {
        $ret = [];
        if (1 == count($groups) && 0 == $groups[0]) {
            $ret[] = __('All', 'rmcommon');

            return $list ? __('All', 'rmcommon') : $ret;
        }

        if (in_array(0, $groups, true)) {
            $ret[] = __('All', 'rmcommon');
        }

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT name FROM ' . $db->prefix('groups') . ' WHERE groupid IN(' . implode(',', $groups) . ')';
        $result = $db->query($sql);
        while (false !== ($row = $db->fetchArray($result))) {
            $ret[] = $row['name'];
        }

        if ($list) {
            return implode(', ', $ret);
        }

        return $ret;
    }

    /**
     * Load all categories from database
     * @param string SQL Filters
     * @param bool $object Determines if the return data is an array with objects (true) or values
     * @param mixed $filters
     * @return array
     */
    public static function load_images_categories($filters = 'ORDER BY id_cat DESC', $object = false)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT * FROM ' . $db->prefix('mod_rmcommon_images_categories') . " $filters";
        $result = $db->query($sql);
        $categories = [];
        while (false !== ($row = $db->fetchArray($result))) {
            $tc = new RMImageCategory();
            $tc->assignVars($row);
            if (!$object) {
                $categories[] = [
                    'id' => $tc->id(),
                    'name' => $tc->getVar('name'),
                ];
            } else {
                $categories[] = $tc;
            }
        }

        return $categories;
    }

    /**
     * @Todo Move this method to RMComments functions class
     * Get all comments for given parameters
     * @param string Object id (can be a module name)
     * @param string Params for comment item
     * @param string Object type (eg. module, plugin, etc)
     * @param int Comment parent id, will return all comments under a given parent
     * @param int User that has been posted the comments
     * @param bool $assign Determines if the output will be assigned to a smarty variable
     * @param mixed $obj
     * @param mixed $params
     * @param mixed $type
     * @param mixed $parent
     * @param null|mixed $user
     * @return array
     */
    public static function get_comments($obj, $params, $type = 'module', $parent = 0, $user = null, $assign = true)
    {
        global $common;

        $parameters = [
            'url' => $common->uris()->current_url(),
            'object' => $obj,
            'type' => 'module',
            'identifier' => $params,
            'parent' => $parent,
            'user' => $user,
            'assign' => $assign,
        ];

        return $common->comments()->load($parameters);
    }

    /**
     * Create the comments form
     * You need to include the template 'rmc-comments-form.html' where
     * you wish to show this form
     * @param string Object name (eg. mywords, qpages, etc.)
     * @param string Params to be included in form
     * @param string Object type (eg. module, plugin, etc.)
     * @param array File path to get the methods to update comments
     * @param mixed $obj
     * @param mixed $params
     * @param mixed $type
     * @param mixed $file
     * @return mixed
     * @deprecated since 2.3.3
     */
    public static function comments_form($obj, $params, $type = 'module', $file = [])
    {
        global $common;

        $parameters = [
            'url' => $common->uris()->current_url(),
            'object' => $obj,
            'type' => 'module',
            'identifier' => $params,
            'file' => $file,
        ];

        return $common->comments()->form($parameters);
    }

    /**
     * @Todo Move this method to RMComments class
     * Delete comments assigned to a object
     * @param string Module name
     * @param string Params
     * @param mixed $module
     * @param mixed $params
     */
    public function delete_comments($module, $params)
    {
        if ('' == $module || '' == $params) {
            return null;
        }

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'DELETE FROM ' . $db->prefix('mod_rmcommon_comments') . " WHERE id_obj='$module' AND params='$params'";

        // Event
        RMEvents::get()->run_event('rmcommon.deleting.comments', $module, $params);

        return $db->queryF($sql);
    }

    /**
     * Check if a plugin is installed and active in Common Utilities
     * @param string $dir Plugin directory name
     * @return bool|object
     * @deprecated
     */
    public static function plugin_installed($dir)
    {
        return Common\Core\Helpers\Plugins::isInstalled($dir);
    }

    /**
     * Get a existing plugin
     * @param string $dir Plugin directory name
     * @return bool|object
     * @deprecated
     */
    public static function load_plugin($dir)
    {
        return Common\Core\Helpers\Plugins::getInstance()->load($dir);
    }

    public static function installed_plugins()
    {
        return Common\Core\Helpers\Plugins::allInstalled();
    }

    /**
     * Get an image from image manager
     * @param $id int Image id
     * @param string Size name from category
     * @param mixed $size
     */
    public function get_image($id, $size = '')
    {
        if ($id <= 0) {
            return false;
        }

        $img = new RMImage($id);

        if ($img->isNew()) {
            return false;
        }

        $cat = new RMImageCategory($img->getVar('cat'));

        $sizes = $cat->getVar('sizes');

        foreach ($sizes as $s) {
            if ($s['name'] == $size) {
                break;
            }
        }

        $date = explode('-', date('d-m-Y', $img->getVar('date')));
        $file = XOOPS_UPLOAD_URL . '/' . $date[2] . '/' . $date[1] . '/';
        if ('' == $size) {
            $file .= $img->getVar('file');

            return $file;
        }

        $file .= 'sizes/' . mb_substr($img->getVar('file'), 0, -4) . '_' . $s['width'] . 'x' . $s['height'] . mb_substr($img->getVar('file'), -4);

        if (!is_file(str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $file))) {
            return $img->getOriginal();
        }

        return $file;
    }

    /**
     * Add keywords and description metas
     * @param string Description for meta content
     * @param string Keywords for meta content. If hti svalue is empty then will generate from description
     * @param int Limit of keywrods to generate
     * @param mixed $description
     * @param mixed $keywords
     * @param mixed $limit
     */
    public function add_keywords_description($description, $keywords = '', $limit = 50)
    {
        if ('' == $description) {
            return;
        }

        $tpl = RMTemplate::getInstance();
        $tc = TextCleaner::getInstance();
        $description = strip_tags($description);
        $tpl->add_meta('description', $tc->truncate($description, 255));
        if ('' != $keywords) {
            $tpl->add_meta('keywords', $keywords);

            return;
        }

        $description = preg_replace("/[^[[:alnum:]]]|[\.,:]/", '', $description);
        $description = preg_replace('/[[:space:]][[:alnum:]]{0,4}[[:space:]]/', ' ', $description);

        $words = explode(' ', $description);
        asort($words);
        $keys = array_rand($words, $limit > count($words) ? count($words) : $limit);

        foreach ($keys as $id) {
            $keywords .= '' == $keywords ? $words[$id] : ', ' . $words[$id];
        }

        $tpl->add_meta('keywords', $keywords);
    }

    /*
            DEPRECATED METHODS
    ===================================
    */

    /**
     * Get Common Utilities Settings.
     * ¡DO NOT USE ANYMORE!
     * This method is deprecated and it's scheduled for remove
     * Use RMSettings::cu_settings() instead
     *
     * @param string $name Settings option name
     * @return mixed Settings array or option value
     * @deprecated
     */
    public function configs($name = '')
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMSettings', 'cu_settings'));

        $ret = RMSettings::cu_settings($name);

        if (is_object($ret)) {
            return (array)$ret;
        }

        return $ret;
    }

    /**
     * Encode array keys to make a valid url string
     *
     * @deprecated
     * @param array Array to encode
     * @param string Var name to generate url
     * @param string URL separator
     * @param mixed $array
     * @param mixed $name
     * @param mixed $separator
     */
    public static function urlencode_array($array, $name, $separator = '&')
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMUris', 'url_encode_array'));

        RMUris::url_encode_array($array, $name, $separator);
    }

    /**
     * Returns the current url
     * @deprecated
     * @return string
     */
    public function current_url()
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMUris', 'current_url'), E_USER_DEPRECATED);

        return RMUris::current_url();
    }

    /**
     * @deprecated
     * Retrieves the settings for a given plugin.
     * This function is deprecated, use RMSettings::plugins_settings() instead;
     * @param string $dir Directory name for plugin
     * @param bool $values Retrieves only key => value (true) or the full array (false)
     * @return array
     */
    public static function plugin_settings($dir, $values = false)
    {
        $settings = RMSettings::plugin_settings($dir, $values);

        if (is_object($settings)) {
            return (array)$settings;
        }

        return $settings;
    }

    /**
     * Load a module as XoopsModule object
     * @deprecated
     * @param int|string Module id or module name
     * @param mixed $mod
     * @return object XoopsModule
     */
    public function load_module($mod)
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMModules', 'load_module'));

        return RMModules::load_module($mod);
    }

    /**
     * See RMModules::get_modules_list
     * @deprecated
     * @param mixed $active
     */
    public function get_modules_list($active = -1)
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMModules', 'get_modules_list'));

        $status = 'all';
        if (0 == $active) {
            $status = 'inactive';
        } elseif (1 == $active) {
            $status = 'active';
        }

        return RMModules::get_modules_list($status);
    }

    public static function error_404($message, $module = '', $params = null)
    {
        global $common;
        header('HTTP/1.0 404 ' . __('Not Found', 'mywords'));
        if ('cgi' == mb_substr(php_sapi_name(), 0, 3)) {
            header('Status: 404 ' . __('Not Found', 'mywords'), true);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 ' . __('Not Found', 'mywords'));
        }

        global $xoopsOption;
        unset($xoopsOption['template_main']);

        $common->template()->header();

        require $common->template()->path('404.php', 'module', 'rmcommon');

        $common->template()->footer();
        exit();
    }

    public static function loadModuleController($dirname)
    {
        if ('' == $dirname) {
            return false;
        }

        $class = ucfirst($dirname) . 'Controller';

        if (class_exists($class)) {
            return $class::getInstance();
        }

        $dirname = mb_strtolower($dirname);

        $file = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/class/' . $dirname . 'controller.php';

        if (false === file_exists($file) || false === is_file($file)) {
            return false;
        }

        include $file;
        $class = ucfirst($dirname) . 'Controller';
        $controller = $class::getInstance();

        return $controller;
    }
}
