<?php
// $Id: plugin.php 902 2012-01-03 07:09:16Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Class to manage plugins objects
*/

class RMPlugin extends RMObject
{
    private $dir = '';
    private $settings = array();
    /**
    * The plugin object
    */
    private $plugin;

	public function __construct($id=null){

		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("mod_rmcommon_plugins");
        $this->setNew();
        $this->initVarsFromTable();

        if ($id==null){
            return null;
        }

        // If provided id is numeric
        if (is_numeric($id) && $this->loadValues($id)){
			$this->unsetNew();
            $this->load_from_dir($this->getVar('dir'));
			return true;
        }

        // If id is a directory name
        $this->primary = 'dir';
        if ($this->loadValues($id)){
			$this->unsetNew();
            $this->load_from_dir($this->getVar('dir'));
        }

        $this->primary = 'id_plugin';

        return null;
    }

    /**
    * This method must be called before to access the plugin methods
    * @param string Directory name
    */
    public function load_from_dir($dir){

        if ($dir == '') return false;

        $path = RMCPATH.'/plugins/'.$dir;

        if (!is_file($path.'/'.strtolower($dir).'-plugin.php')) return false;

        include_once $path.'/'.strtolower($dir).'-plugin.php';
        $class = ucfirst($dir).'CUPlugin';

        if (!class_exists($class)) return false;

        $this->plugin = new $class();
        $this->setVar('dir', $dir);

        foreach ($this->plugin->info() as $k => $v){
			$this->setVar($k, $v);
        }

        return true;
    }

	public function id(){
		return $this->getVar('id_plugin');
	}

	public function plugin($dir = ''){

		$dir = $dir=='' ? $this->getVar('dir') : $dir;
		$class = ucfirst($dir).'CUPlugin';

		if (is_a($this->plugin, $class))
			return $this->plugin;

		if (!class_exists($class))
			include_once RMCPATH.'/plugins/'.$dir.'/'.strtolower($dir).'-plugin.php';

		$plugin = new $class();
		return $plugin;

	}

	public function get_info($name){
		return $this->plugin()->get_info($name);
	}

    public function on_install(){
        if ( false == $this->plugin->on_install() ) {
            $this->addError($this->plugin->errors());
            return false;
        }

        return true;
    }

    public function on_update(){
        if ( !$this->plugin->on_update() ) {
            $this->addError($this->plugin->errors());
            return false;
        }

        return true;
    }

    public function on_uninstall(){
        if ( !$this->plugin->on_uninstall() ) {
            $this->addError($this->plugin->errors());
            return false;
        }

        return true;
    }

    public function on_activate($q){
        if ( !$this->plugin->on_activate() ) {
            $this->addError($this->plugin->errors());
            return false;
        }

        return true;
    }

    public function options(){
        return $this->plugin->options();
    }

    private function insert_configs(){

        $dir = $this->plugin()->get_info('dir');
        $pre_options = $this->plugin->options();

        if (empty($pre_options)) return null;

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $c_options = RMFunctions::plugin_settings($dir);

        if (empty($c_options)){

            $sql = '';
            foreach ($pre_options as $name => $option){
                $sql .= $sql==''?'':',';
                $sql .= "('$dir','$name','plugin','$option[value]','$option[valuetype]')";
            }

            $sql = "INSERT INTO ".$db->prefix("mod_rmcommon_settings")." (`element`,`name`,`type`,`value`,`valuetype`) VALUES ".$sql;

            if(!$db->queryF($sql)){
                $this->addError($this->db->error());
                return false;
            } else {
                return true;
            }

        } else {

            $sql = '';
            foreach ($pre_options as $name => $option){

                if (isset($c_options[$name])){
                    $option['value'] = $c_options[$name]['value'];
                    $sql = "UPDATE ".$db->prefix("mod_rmcommon_settings")." SET value='$option[value]' WHERE element='$dir' AND type='plugin' AND name='$name'";
                    $db->queryF($sql);
                } else {
                    $sql = "INSERT INTO ".$db->prefix("mod_rmcommon_settings")." (`element`,`name`,`type`,`value`,`valuetype`) VALUES
                            ('$dir','$name','plugin','$option[value]','$option[valuetype]')";
                    $db->queryF($sql);
                }
            }

        }

        return true;
    }

    public function save(){

        $this->insert_configs();

        if ($this->isNew()){
            return $this->saveToTable();
        } else {
            return $this->updateTable();
        }
    }

    public function delete(){

        $dir = $this->plugin()->get_info('dir');
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "DELETE FROM ".$db->prefix("mod_rmcommon_settings")." WHERE element='$dir' AND type='plugin'";
        if(!$db->queryF($sql)){
            $this->addError($db->error());
            return false;
        }

        return $this->deleteFromTable();
    }

}
