<?php
// $Id: functions.php 1071 2012-09-22 23:45:24Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include_once RMCPATH . '/class/helpers/settings.class.php';
include_once RMCPATH . '/class/helpers/modules.class.php';
include_once RMCPATH . '/class/helpers/uris.class.php';

class RMFunctions
{
    public $settings = '';
    public $modules = '';
    public $uris = '';

    public function __construct()
    {

        $this->settings = new RMSettings();
        $this->modules = new RMModules;
        $this->uris = new RMUris;

    }

    static function get()
    {
        static $instance;

        if (!isset($instance))
            $instance = new RMFunctions();

        return $instance;

    }

    /**
     * Check the number of images category on database
     */
    public static function get_num_records($table, $filters = '')
    {

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $sql = "SELECT COUNT(*) FROM " . $db->prefix($table);
        $sql .= $filters != '' ? " WHERE $filters" : '';

        list($num) = $db->fetchRow($db->query($sql));

        return $num;

    }

    /**
     * Create the module toolbar. This function must be called only from rmcommon module administration
     */
    public static function create_toolbar()
    {

        if (RMCLOCATION == 'users') {

            RMTemplate::getInstance()->add_tool(
                __('Users', 'rmcommon'),
                'users.php',
                'icon icon-users',
                'allusers',
                array('class' => 'cu-tool tool-users-list')
            );

            RMTemplate::getInstance()->add_tool(
                __('Add', 'rmcommon'),
                'users.php?action=new',
                'icon icon-plus',
                'newuser',
                array('class' => 'cu-tool tool-user-add')
            );


        } elseif (RMCLOCATION == 'groups') {

            RMTemplate::getInstance()->add_tool(
                __('Groups', 'rmcommon'),
                'groups.php',
                'icon icon-users',
                'allgroups',
                array(
                    'class' => 'cu-tool tool-groups'
                )
            );

            RMTemplate::getInstance()->add_tool(
                __('Add', 'rmcommon'),
                '#',
                'icon icon-plus',
                'newgroup',
                array(
                    'class' => 'cu-tool tool-group-add',
                    'data-action' => 'load-remote-dialog',
                    'data-url' => 'groups.php?action=new-group',
                    'data-parameters' => '{action: \'new-group\'}'
                )
            );

            /*
             * Next buttons are available only when groups list is shown
             */
            RMTemplate::getInstance()->add_tool(
                __('Edit', 'rmcommon'),
                '#',
                'icon icon-pencil',
                'editgroup',
                array(
                    'class' => 'cu-tool tool-group-edit',
                    'data-activator' => 'groups-list',
                    'data-oncount' => '== 1',
                    'data-action' => 'groupsController.edit',
                    'disabled' => 'disabled',
                    'title' => __('Edit Group', 'rmcommon')
                )
            );

            RMTemplate::getInstance()->add_tool(
                __('Delete', 'rmcommon'),
                '#',
                'icon icon-bin',
                'deletegroup',
                array(
                    'class' => 'cu-tool tool-group-delete',
                    'data-activator' => 'groups-list',
                    'data-oncount' => '> 0',
                    'disabled' => 'disabled',
                    'title' => __('Delete Groups', 'rmcommon'),
                    'data-action' => 'groupsController.delete'
                )
            );

        } elseif (RMCLOCATION == 'imgmanager') {

            RMTemplate::getInstance()->add_tool(
                __('Categories', 'rmcommon'),
                'images.php?action=showcats',
                'svg-rmcommon-folder text-orange',
                'showcategories',
                array('class' => 'cu-tool tool-categories-images')
            );
            RMTemplate::getInstance()->add_tool(
                __('New', 'rmcommon'),
                'images.php?action=newcat',
                'svg-rmcommon-folder-plus text-orange',
                'newcategory',
                array('class' => 'cu-tool tool-category-add')
            );
            $cat = rmc_server_var($_REQUEST, 'category', 0);
            if ($cat > 0) {
                RMTemplate::getInstance()->add_tool(
                    __('Images', 'rmcommon'),
                    'images.php?category=' . $cat,
                    'svg-rmcommon-camera',
                    'showimages',
                    array('class' => 'cu-tool tool-images')
                );
            }
            RMTemplate::getInstance()->add_tool(
                __('Add', 'rmcommon'),
                'images.php?action=new' . ($cat > 0 ? "&amp;category=$cat" : ''),
                'svg-rmcommon-camera-plus text-info',
                'addimages',
                array('class' => 'cu-tool tool-images-add')
            );

        } else {

            RMTemplate::getInstance()->add_tool(__('Dashboard', 'rmcommon'), 'index.php', '', 'dashboard', array('class' => 'cu-tool tool-dashboard'));
            RMTemplate::getInstance()->add_tool(__('Modules', 'rmcommon'), 'modules.php', '', 'modules', array('class' => 'cu-tool tool-modules'));
            RMTemplate::getInstance()->add_tool(__('Blocks', 'rmcommon'), 'blocks.php', '', 'blocks', array('class' => 'cu-tool tool-blocks'));
            RMTemplate::getInstance()->add_tool(__('Groups', 'rmcommon'), 'groups.php', '', 'groups', array('class' => 'cu-tool tool-groups'));
            RMTemplate::getInstance()->add_tool(__('Users', 'rmcommon'), 'users.php', '', 'users', array('class' => 'cu-tool tool-users'));
            RMTemplate::getInstance()->add_tool(__('Images', 'rmcommon'), 'images.php', '', 'imgmanager', array('class' => 'cu-tool tool-images'));
            RMTemplate::getInstance()->add_tool(__('Comments', 'rmcommon'), 'comments.php', '', 'comments', array('class' => 'cu-tool tool-comments'));
            RMTemplate::getInstance()->add_tool(__('Plugins', 'rmcommon'), 'plugins.php', '', 'plugins', array('class' => 'cu-tool tool-plugins'));
            RMTemplate::getInstance()->add_tool(__('Updates', 'rmcommon'), 'updates.php', '', 'updates', array('class' => 'cu-tool tool-updates'));

        }

        RMEvents::get()->run_event('rmcommon.create.toolbar');

    }

    /**
     * This functions allows to get the groups names for a single category
     * @param array Groups ids
     * @param bool Return as list
     * @return array|list
     */
    public static function get_groups_names($groups, $list = true)
    {

        $ret = array();
        if (count($groups) == 1 && $groups[0] == 0) {
            $ret[] = __('All', 'rmcommon');
            return $list ? __('All', 'rmcommon') : $ret;
        }

        if (in_array(0, $groups)) $ret[] = __('All', 'rmcommon');


        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT name FROM " . $db->prefix("groups") . " WHERE groupid IN(" . implode(',', $groups) . ")";
        $result = $db->query($sql);
        while ($row = $db->fetchArray($result)) {
            $ret[] = $row['name'];
        }

        if ($list) return implode(', ', $ret);
        return $ret;
    }

    /**
     * Load all categories from database
     * @param string SQL Filters
     * @param bool $object Determines if the return data is an array with objects (true) or values
     * @return array
     */
    public static function load_images_categories($filters = 'ORDER BY id_cat DESC', $object = false)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT * FROM " . $db->prefix("mod_rmcommon_images_categories") . " $filters";
        $result = $db->query($sql);
        $categories = array();
        while ($row = $db->fetchArray($result)) {
            $tc = new RMImageCategory();
            $tc->assignVars($row);
            if (!$object) {
                $categories[] = array(
                    'id' => $tc->id(),
                    'name' => $tc->getVar('name')
                );
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
     * @return array
     */
    static public function get_comments($obj, $params, $type = 'module', $parent = 0, $user = null, $assign = true)
    {

        global $common;

        $parameters = [
            'url' => $common->uris()->current_url(),
            'object' => $obj,
            'type' => 'module',
            'identifier' => $params,
            'parent' => $parent,
            'user' => $user,
            'assign' => $assign
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
     * @return mixed
     * @deprecated since 2.3.3
     */
    static function comments_form($obj, $params, $type = 'module', $file = array())
    {
        global $common;

        $parameters = [
            'url' => $common->uris()->current_url(),
            'object' => $obj,
            'type' => 'module',
            'identifier' => $params,
            'file' => $file
        ];

        return $common->comments()->form($parameters);

    }

    /**
     * @Todo Move this method to RMComments class
     * Delete comments assigned to a object
     * @param string Module name
     * @param string Params
     */
    public function delete_comments($module, $params)
    {

        if ($module == '' || $params == '') return null;

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "DELETE FROM " . $db->prefix("mod_rmcommon_comments") . " WHERE id_obj='$module' AND params='$params'";

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
     */
    function get_image($id, $size = '')
    {

        if ($id <= 0) return false;

        $img = new RMImage($id);

        if ($img->isNew()) return false;

        $cat = new RMImageCategory($img->getVar('cat'));

        $sizes = $cat->getVar('sizes');

        foreach ($sizes as $s) {
            if ($s['name'] == $size)
                break;
        }

        $date = explode('-', date('d-m-Y', $img->getVar('date')));
        $file = XOOPS_UPLOAD_URL . '/' . $date[2] . '/' . $date[1] . '/';
        if ($size == '') {
            $file .= $img->getVar('file');
            return $file;
        }

        $file .= 'sizes/' . substr($img->getVar('file'), 0, -4) . '_' . $s['width'] . 'x' . $s['height'] . substr($img->getVar('file'), -4);

        if (!is_file(str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $file)))
            return $img->getOriginal();

        return $file;

    }

    /**
     * Add keywords and description metas
     * @param string Description for meta content
     * @param string Keywords for meta content. If hti svalue is empty then will generate from description
     * @param int Limit of keywrods to generate
     */
    public function add_keywords_description($description, $keywords = '', $limit = 50)
    {

        if ($description == '') return;

        $tpl = RMTemplate::getInstance();
        $tc = TextCleaner::getInstance();
        $description = strip_tags($description);
        $tpl->add_meta('description', $tc->truncate($description, 255));
        if ($keywords != '') {
            $tpl->add_meta('keywords', $keywords);
            return;
        }

        $description = preg_replace("/[^[[:alnum:]]]|[\.,:]/", '', $description);
        $description = preg_replace("/[[:space:]][[:alnum:]]{0,4}[[:space:]]/", ' ', $description);

        $words = explode(" ", $description);
        asort($words);
        $keys = array_rand($words, $limit > count($words) ? count($words) : $limit);

        foreach ($keys as $id) {
            $keywords .= $keywords == '' ? $words[$id] : ', ' . $words[$id];
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

        if (is_object($ret))
            return (array)$ret;
        else
            return $ret;

    }

    /**
     * Encode array keys to make a valid url string
     *
     * @deprecated
     * @param array Array to encode
     * @param string Var name to generate url
     * @param string URL separator
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

        if (is_object($settings))
            return (array)$settings;
        else
            return $settings;

    }

    /**
     * Load a module as XoopsModule object
     * @deprecated
     * @param int|string Module id or module name
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
     */
    public function get_modules_list($active = -1)
    {

        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMModules', 'get_modules_list'));

        $status = 'all';
        if ($active == 0)
            $status = 'inactive';
        elseif ($active == 1)
            $status = 'active';

        return RMModules::get_modules_list($status);

    }

    static public function error_404($message, $module = '', $params = null)
    {

        header("HTTP/1.0 404 " . __('Not Found', 'mywords'));
        if (substr(php_sapi_name(), 0, 3) == 'cgi')
            header("Status: 404 " . __('Not Found', 'mywords'), TRUE);
        else
            header($_SERVER['SERVER_PROTOCOL'] . " 404 " . __('Not Found', 'mywords'));

        global $xoopsOption;
        unset($xoopsOption['template_main']);
        require RMCPATH . '/404.php';
        exit();

    }

}
