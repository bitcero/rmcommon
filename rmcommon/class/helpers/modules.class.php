<?php
// $Id$
// --------------------------------------------------------------
// Common Utilities 2
// A modules framework by Red Mexico
// Author: Eduardo Cortes
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// URI: http://www.redmexico.com.mx
// --------------------------------------------------------------

/**
 * This class contains methods that allow to work with modules directly
 */
class RMModules
{
    private $errors = array();
    /**
     * Retrieves and format the version of a given module.
     * If $module parameter is not given, then will try to get the current module version.
     *
     * Example with <strong>formatted</strong> result:
     * <pre>
     * $version = RMModules::get_module_version('mywords');
     * </pre>
     *
     * Get the same result:
     * <pre>
     * $version = RMModules::get_module_version('mywords', true, 'verbose');
     * </pre>
     *
     * Both previous examples return something like this:
     * <pre>MyWords 2.1.3 production</pre>
     *
     * Example 2. Get an array of values:
     * <pre>
     * print_r(RMModules::get_module_version('mywords', true, 'raw');
     * </pre>
     *
     * Will return:
     * <pre>
     * array
     *
     * @param string $module Module directory to check
     * @param bool $name Return the name and version (true) or only version (false)
     * @param string $type Type of data to get: 'verbose' get a formatted string, 'raw' gets an array with values.
     * @return array|string
     */
    static public function get_module_version( $module = '', $name = true, $type = 'verbose' ){
        global $xoopsModule;

        //global $version;
        if ( $module != '' ){

            if ( $xoopsModule && $xoopsModule->dirname() == $module ){
                $mod = $xoopsModule;
            } else {
                $mod = new XoopsModule();
            }

        } else {
            $mod = new XoopsModule();
        }

        $mod->loadInfoAsVar( $module );
        $version = $mod->getInfo( 'rmversion' );
        $version = is_array( $version ) ? $version : array('major'=>$version, 'minor'=>0, 'revision'=>0, 'stage' => 0, 'name'=>$mod->getInfo('name'));

        if ($type==1)
            return $version;

        return self::format_module_version($version, $name);

    }

    /**
     * Format a given array with version information for a module.
     *
     * @param array $version Array with version values
     * @param bool $name Include module name in return string
     * @return string
     */
    static public function format_module_version($version, $name = false){

        return RMFormat::version( $version, $name );
    }

    /**
     * Load a given Xoops Module
     * @param string|integer $id Indentifier of module. Could be dirname or numeric ID
     * @return XoopsModule
     */
    static public function load_module( $id ){

        $module_handler = xoops_gethandler('module');

        if ( is_numeric( $id ) )
            $module = $module_handler->get( $id );
        else
            $module = $module_handler->getByDirname( $id );

        if ( $module )
            load_mod_locale( $module->getVar('dirname') );

        return $module;
    }

    /**
     * Retrieves the list of installed modules
     *
     * Accepted $status values: 'all', 'active', 'inactive'
     *
     * @param string $status Type of modules to get
     * @return array
     */
    static public function get_modules_list( $status = 'all' ){

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $active = null;

        if ( $status == 'inactive' )
            $active = 0;
        elseif ( $status=='active' )
            $active = 1;

        $sql = "SELECT mid, name, dirname FROM " . $db->prefix("modules");

        if( isset( $active ) )
            $sql .= " WHERE isactive=$active";

        $sql .= " ORDER BY name";
        $result = $db->query($sql);
        $modules = array();

        while ( $row = $db->fetchArray( $result ) ) {
            $modules[] = $row;
        }

        return $modules;
    }

    /**
     * Create the files to store the modules list
     */
    static function build_modules_cache(){

        $modules = XoopsLists::getModulesList();

        print_r($modules);

    }
    
    /**
     * Get the main menu for a module
     * @param string $dirname Directory name of module
     * @return array|bool
     */
    static function main_menu( $dirname ){
        global $xoopsModule;
        
        if ( $xoopsModule && $xoopsModule->getVar('dirname') == $dirname )
            $mod = $xoopsModule;
        else
            $mod = self::load_module( $dirname );

        if ($mod->getInfo('main_menu') && $mod->getInfo('main_menu') != '' && file_exists(XOOPS_ROOT_PATH . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('main_menu') )) {
            $main_menu = array();
            include XOOPS_ROOT_PATH . '/modules/' . $mod->getVar('dirname') . '/' . $mod->getInfo('main_menu');
            return $main_menu;
        }

        return false;
    }

    /**
     * Get the icon url in specified size
     * @param $dirname Module dirname
     * @param $size Icon size. This size must exists in xoops_version.php file
     * @return string
     */
    static function icon( $dirname, $size ){

        global $xoopsModule;

        if ( $xoopsModule && $xoopsModule->getVar('dirname') == $dirname )
            $mod = $xoopsModule;
        else
            $mod = self::load_module( $dirname );

        $icon = XOOPS_URL . '/modules/' . $dirname . '/';
        $icon .= $mod->getInfo( 'icon' . $size );
        return $icon;

    }

    public function install( $mod = '', $force = false ){

        $xoops = Xoops::getInstance();
        $module_handler = $xoops->getHandlerModule();
        $mod = trim($mod);

        $log = RMModules_Logger::get();

        try {
            $exists = $module_handler->getCount(new Criteria('dirname', $mod));
        } catch (DBALException $e) {
            $exists = 0;
        }

        if ( $exists > 0 ){

            $log->add_item( sprintf(
                    __( '"%s" was not installed!', 'rmcommon'),
                    '<strong>' . $mod . '</strong>'
                ), 'danger', 'fa fa-excalamation-triangle' );
            return false;

        }

        /* @var $module XoopsModule */
        $module = $module_handler->create();
        $module->loadInfoAsVar($mod);
        $module->setVar('weight', 1);
        $module->setVar('isactive', 1);
        $module->setVar('last_update', time());

        $install_script = $module->getInfo('onInstall');
        if ($install_script && trim($install_script) != '') {
            XoopsLoad::loadFile($xoops->path('modules/' . $mod . '/' . trim($install_script)));
        }
        $func = "xoops_module_pre_install_{$mod}";
        // If pre install function is defined, execute
        if (function_exists($func)) {
            $result = $func($module);
            if (!$result) {
                $log->add_item( sprintf(__( '"%s" was not executed!', 'rmcommon' ), $func), 'danger', 'fa fa-exclamation-triangle' );
                return false;
            } else {
                $log->add_item( sprintf( __('"%s" executed successfully.', 'rmcommon' ), "<strong>{$func}</strong>"), 'success', 'fa fa-cog' );
            }
        }
        // Create tables
        $created_tables = array();
        if ($log->count( 'danger' ) == 0) {
            $schema_file = $module->getInfo('schema');
            $sql_file = $module->getInfo('sqlfile');
            if (!empty($schema_file)) {
                $schema_file_path = XOOPS_ROOT_PATH . '/modules/' . $mod . '/' . $schema_file;
                if (!XoopsLoad::fileExists($schema_file_path)) {
                    $log->add_item( sprintf( __('SQL file not found at %s!', 'rmcommon'), "<strong>{$schema_file}</strong>" ), 'danger', 'fa fa-exclamation-triangle' );
                    return false;
                }
                $importer = new ImportSchema;
                $importSchema = $importer->importSchemaArray(Yaml::read($schema_file_path));
                $synchronizer = new SingleDatabaseSynchronizer($xoops->db());
                $synchronizer->updateSchema($importSchema, true);
            } elseif (is_array($sql_file) && !empty($sql_file[XOOPS_DB_TYPE])) {
                $xoops->deprecated('Install SQL files are deprecated since 2.6.0. Convert to portable Schemas');

                $sql_file_path = XOOPS_ROOT_PATH . '/modules/' . $mod . '/' . $sql_file[XOOPS_DB_TYPE];
                if (!XoopsLoad::fileExists($sql_file_path)) {
                    $this->error[] =
                        sprintf(SystemLocale::EF_SQL_FILE_NOT_FOUND, "<strong>{$sql_file_path}</strong>");
                    return false;
                } else {
                    $this->trace[] = sprintf(SystemLocale::SF_SQL_FILE_FOUND, "<strong>{$sql_file_path}</strong>");
                    $this->trace[] = SystemLocale::MANAGING_TABLES;

                    $sql_query = fread(fopen($sql_file_path, 'r'), filesize($sql_file_path));
                    $sql_query = trim($sql_query);
                    SqlUtility::splitMySqlFile($pieces, $sql_query);
                    foreach ($pieces as $piece) {
                        // [0] contains the prefixed query
                        // [4] contains unprefixed table name
                        $prefixed_query = SqlUtility::prefixQuery($piece, $xoops->db()->prefix());
                        if (!$prefixed_query) {
                            $this->error[]['sub'] = '<span class="red">' . sprintf(
                                    XoopsLocale::EF_INVALID_SQL,
                                    '<strong>' . $piece . '</strong>'
                                ) . '</span>';
                            break;
                        }
                        // check if the table name is reserved
                        if (!in_array($prefixed_query[4], $this->reservedTables) || $mod == 'system') {
                            // not reserved, so try to create one
                            try {
                                $result = $xoops->db()->query($prefixed_query[0]);
                            } catch (Exception $e) {
                                $xoops->events()->triggerEvent('core.exception', $e);
                                $result=false;
                            }

                            if (!$result) {
                                $this->error[] = $xoops->db()->errorInfo();
                                break;
                            } else {
                                if (!in_array($prefixed_query[4], $created_tables)) {
                                    $this->trace[]['sub'] = sprintf(
                                        XoopsLocale::SF_TABLE_CREATED,
                                        '<strong>' . $xoops->db()->prefix($prefixed_query[4]) . '</strong>'
                                    );
                                    $created_tables[] = $prefixed_query[4];
                                } else {
                                    $this->trace[]['sub'] = sprintf(
                                        XoopsLocale::SF_DATA_INSERTED_TO_TABLE,
                                        '<strong>' . $xoops->db()->prefix($prefixed_query[4]) . '</strong>'
                                    );
                                }
                            }
                        } else {
                            // the table name is reserved, so halt the installation
                            $this->error[]['sub'] = sprintf(
                                SystemLocale::EF_TABLE_IS_RESERVED,
                                '<strong>' . $prefixed_query[4] . '</strong>'
                            );
                            break;
                        }
                    }
                    // if there was an error, delete the tables created so far,
                    // so the next installation will not fail
                    if (count($this->error) > 0) {
                        foreach ($created_tables as $table) {
                            try {
                                $xoops->db()->query('DROP TABLE ' . $xoops->db()->prefix($table));
                            } catch (Exception $e) {
                                $xoops->events()->triggerEvent('core.exception', $e);
                            }
                        }
                        return false;
                    }
                }
            }
        }
        // Save module info, blocks, templates and perms
        if (count($this->error) == 0) {
            if (!$module_handler->insertModule($module)) {
                $this->error[] = sprintf(
                    XoopsLocale::EF_NOT_INSERTED_TO_DATABASE,
                    '<strong>' . $module->getVar('name') . '</strong>'
                );
                foreach ($created_tables as $ct) {
                    try {
                        $xoops->db()->query('DROP TABLE ' . $xoops->db()->prefix($ct));
                    } catch (Exception $e) {
                        $xoops->events()->triggerEvent('core.exception', $e);
                    }
                }
                $this->error[] = sprintf(XoopsLocale::EF_NOT_INSTALLED, "<strong>" . $module->name() . "</strong>");
                $this->error[] = XoopsLocale::C_ERRORS;
                unset($module);
                unset($created_tables);
                return false;
            }
            unset($created_tables);
            $this->trace[] = XoopsLocale::S_DATA_INSERTED . sprintf(
                    SystemLocale::F_MODULE_ID,
                    '<strong>' . $module->getVar('mid') . '</strong>'
                );
            $xoops->db()->beginTransaction();
            // install Templates
            $this->installTemplates($module);

            $xoops->templateClearModuleCache($module->getVar('mid'));

            // install blocks
            $this->installBlocks($module);

            // Install Configs
            $this->installConfigs($module, 'add');

            if ($module->getInfo('hasMain')) {
                $groups = array(XOOPS_GROUP_ADMIN, XOOPS_GROUP_USERS, XOOPS_GROUP_ANONYMOUS);
            } else {
                $groups = array(XOOPS_GROUP_ADMIN);
            }
            // retrieve all block ids for this module
            $block_handler = $xoops->getHandlerBlock();
            $blocks = $block_handler->getByModule($module->getVar('mid'), false);
            $this->trace[] = SystemLocale::MANAGING_PERMISSIONS;
            $gperm_handler = $xoops->getHandlerGroupperm();
            foreach ($groups as $mygroup) {
                if ($gperm_handler->checkRight('module_admin', 0, $mygroup)) {
                    $mperm = $gperm_handler->create();
                    $mperm->setVar('gperm_groupid', $mygroup);
                    $mperm->setVar('gperm_itemid', $module->getVar('mid'));
                    $mperm->setVar('gperm_name', 'module_admin');
                    $mperm->setVar('gperm_modid', 1);
                    if (!$gperm_handler->insert($mperm)) {
                        $this->trace[]['sub'] = '<span class="red">' . sprintf(
                                SystemLocale::EF_GROUP_ID_ADMIN_ACCESS_RIGHT_NOT_ADDED,
                                '<strong>' . $mygroup . '</strong>'
                            ) . '</span>';
                    } else {
                        $this->trace[]['sub'] = sprintf(
                            SystemLocale::SF_GROUP_ID_ADMIN_ACCESS_RIGHT_ADDED,
                            '<strong>' . $mygroup . '</strong>'
                        );
                    }
                    unset($mperm);
                }
                $mperm = $gperm_handler->create();
                $mperm->setVar('gperm_groupid', $mygroup);
                $mperm->setVar('gperm_itemid', $module->getVar('mid'));
                $mperm->setVar('gperm_name', 'module_read');
                $mperm->setVar('gperm_modid', 1);
                if (!$gperm_handler->insert($mperm)) {
                    $this->trace[]['sub'] = '<span class="red">' . sprintf(
                            SystemLocale::EF_GROUP_ID_USER_ACCESS_RIGHT_NOT_ADDED,
                            '<strong>' . $mygroup . '</strong>'
                        ) . '</span>';
                } else {
                    $this->trace[]['sub'] = sprintf(
                        SystemLocale::SF_GROUP_ID_USER_ACCESS_RIGHT_ADDED,
                        '<strong>' . $mygroup . '</strong>'
                    );
                }
                unset($mperm);
                foreach ($blocks as $blc) {
                    $bperm = $gperm_handler->create();
                    $bperm->setVar('gperm_groupid', $mygroup);
                    $bperm->setVar('gperm_itemid', $blc);
                    $bperm->setVar('gperm_name', 'block_read');
                    $bperm->setVar('gperm_modid', 1);
                    if (!$gperm_handler->insert($bperm)) {
                        $this->trace[]['sub'] = '<span class="red">'
                            . SystemLocale::E_BLOCK_ACCESS_NOT_ADDED . ' Block ID: <strong>'
                            . $blc . '</strong> Group ID: <strong>' . $mygroup . '</strong></span>';
                    } else {
                        $this->trace[]['sub'] = SystemLocale::S_BLOCK_ACCESS_ADDED
                            . sprintf(SystemLocale::F_BLOCK_ID, "<strong>" . $blc . "</strong>")
                            . sprintf(SystemLocale::F_GROUP_ID, "<strong>" . $mygroup . "</strong>");
                    }
                    unset($bperm);
                }
            }
            unset($blocks);
            unset($groups);

            // execute module specific install script if any
            // If pre install function is defined, execute
            $func = "xoops_module_install_{$mod}";
            if (function_exists($func)) {
                $result = $func($module);
                if (!$result) {
                    $this->trace[] = sprintf(XoopsLocale::EF_NOT_EXECUTED, $func);
                    $this->trace = array_merge($this->trace, $module->getErrors());
                } else {
                    $this->trace[] = sprintf(XoopsLocale::SF_EXECUTED, "<strong>{$func}</strong>");
                    $this->trace = array_merge($this->trace, $module->getMessages());
                }
            }

            $this->trace[] = sprintf(
                XoopsLocale::SF_INSTALLED,
                '<strong>' . $module->getVar('name', 's') . '</strong>'
            );
            unset($blocks);

            $xoops->db()->commit();

            XoopsPreload::getInstance()->triggerEvent('onModuleInstall', array(&$module, &$this));
            return $module;
        }

        return false;

    }

}

/**
 * This class allows to store all events generated during installation or uninstallation of modules
 * Class RMModules_Logger
 */
class RMModules_Logger
{
    use RMSingleton;

    /**
     * @var array
     */
    private $log = array();

    /**
     * Add an item to log with all required data
     * @param string $text <p>The text of the item</p>
     * @param string $type <p>Type of this item. Can be: info, warning, danger, success or empty.</p>
     * @param string $icon <p>The icon for this item. Can be a class for a font icon or an url.</p>
     */
    public function add_item( $text, $type = 'info', $icon = '' ){

        $this->log[] = array(
            'text' => $text,
            'type' => $info,
            'icon' => $icon
        );

    }

    /**
     * Count the elements of a certain type
     * @param string $type <p>Type of elements to count. Can be info, warning, danger, success or empty.</p>
     * @return int
     */
    public function count( $type = 'danger' ){

        $count = 0;

        foreach( $this->log as $item )
            if ( $item['type'] == $type )
                $count++;

        return $count;

    }

}