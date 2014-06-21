<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Rmcommon_Config_Item extends RMObject
{

    /**
     * Loads the specified item
     * @param string $name <p>Name of the configuration option</p>
     * @param int $mod <p>Identifier of the module</p>
     */
    public function __construct( $name = '', $mod = 0 ){

        $this->db =& XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("config");
        $this->setNew();
        $this->initVarsFromTable();

        if ($name == '' || $mod <= 0 ) return;

        $name = mysql_real_escape_string($name);

        $sql = "SELECT * FROM $this->_dbtable WHERE `conf_name`='$name' AND `conf_modid`=$mod";
        $result = $this->db->query($sql);
        if ($this->db->getRowsNum($result)<=0) return;

        $row = $this->db->fetchArray($result);
        foreach ($row as $k => $v){
            $this->setVar($k, $v);
        }

        $this->unsetNew();

    }

    /**
     * Set a config value
     *
     * @param mixed $value Value
     * @param string $type_Value type
     */
    function set_value( $value, $type )
    {
        switch ( $type ) {
            case 'array':
                if (!is_array($value)) {
                    $value = explode('|', trim($value));
                }
                $this->setVar('conf_value', serialize($value) );
                break;
            case 'text':
                $this->setVar('conf_value', trim($value) );
                break;
            default:
                $this->setVar('conf_value', $value );
                break;
        }
    }

    public function save(){

        if ( $this->isNew() )
            return $this->saveToTable();
        else
            return $this->updateTable();

    }

    public function delete(){

        $sql = "DELETE FROM $this->_dbtable WHERE conf_id=" . $this->id();
        $this->db->queryF( $sql );
        return $this->deleteFromTable();

    }

}