<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class Rmcommon_Group extends RMObject
{
    use RMProperties;

    public function __construct( $id = null ){

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("groups");
        $this->setNew();
        $this->initVarsFromTable();

        if ( null == $id || 0 >= $id )
            return null;

        if (!$this->loadValues( $id ))
            return false;

        $this->unsetNew();

        return null;
    }

    public function save(){

        if ( $this->isNew() )
            return $this->saveToTable();
        else
            return $this->updateTable();

    }

    public function set_admin_permissions( $perms ){

        if ( $this->isNew() || empty( $perms ) )
            return true;

        // Eliminamos los permisos actuales
        $sql = "DELETE FROM " . $this->db->prefix("group_permission") . " WHERE gperm_groupid = " . $this->id() . " AND gperm_name = 'module_admin'";
        $this->db->queryF( $sql );

        //Almacenamos los nuevos permisos
        $sql = "INSERT INTO " . $this->db->prefix("group_permission") . " (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ";

        foreach ( $perms as $item )
            $sql .= "(" . $this->id() . ", $item, 1, 'module_admin'),";

        $result = $this->db->queryF( rtrim($sql,',') );

        if ( !$result )
            $this->addError( $this->db->error() );

        return $result;

    }

    public function set_read_permissions( $perms ){

        if ( $this->isNew() || empty( $perms ) )
            return true;

        // Eliminamos los permisos actuales
        $sql = "DELETE FROM " . $this->db->prefix("group_permission") . " WHERE gperm_groupid = " . $this->id() . " AND gperm_name = 'module_read'";
        $this->db->queryF( $sql );

        //Almacenamos los nuevos permisos
        $sql = "INSERT INTO " . $this->db->prefix("group_permission") . " (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES ";

        foreach ( $perms as $item )
            $sql .= "(" . $this->id() . ", $item, 1, 'module_read'),";

        $result = $this->db->queryF( rtrim($sql, ",") );

        if ( !$result )
            $this->addError( $this->db->error() );

        return $result;

    }

    public function set_specific_permissions( $perms ){

        if ( $this->isNew() || empty( $perms ) )
            return true;

        // Eliminamos los permisos actuales
        $sql = "DELETE FROM " . $this->db->prefix("mod_rmcommon_permissions") . " WHERE `group` = '" . $this->id() . "'";
        $this->db->queryF( $sql );

        $sql = "INSERT INTO " . $this->db->prefix("mod_rmcommon_permissions") . " (`group`, `element`, `key`) VALUES ";

        foreach ( $perms as $element => $perm ){
            foreach ( $perm as $key ){
                $sql .= "(" . $this->id() . ", '" . $element . "', '$key'),";
            }
        }

        $result = $this->db->queryF( rtrim($sql, ",") );

        if ( !$result )
            $this->addError( $this->db->error() );

        return $result;

    }

    public function load_permissions( $type = 'specific' ){

        if ($type=='specific'){

            if ( !empty($this->specific_perms) )
                return $this->specific_perms;

            $sql = "SELECT * FROM " . $this->db->prefix("mod_users_permissions") . " WHERE `group` = " . $this->id();
            $return = array();
            $result = $this->db->query($sql);
            while( $row = $this->db->fetchArray( $result ) ){
                $return[] = array( $row['element'], $row['key'] );
            }

            $this->specific_perms = $return;
            return $return;

        } elseif ( $type== 'module_admin ') {

            if ( !empty($this->admin_perms) )
                return $this->admin_perms;

        } elseif ( $type == 'module_read' ){

            if ( !empty($this->read_perms) )
                return $this->read_perms;

        }

        $sql = "SELECT * FROM " . $this->db->prefix("group_permission") . " WHERE gperm_groupid = " . $this->id();
        $return = array();
        $result = $this->db->query($sql);
        while( $row = $this->db->fetchArray( $result ) ){
            $return[$row['gperm_name']][$row['gperm_itemid']] = $row['gperm_itemid'];
        }

        $this->admin_perms = isset($return['module_admin']) ? $return['module_admin'] : array();
        $this->read_perms = isset($return['module_read']) ? $return['module_read'] :  array();

        if ($type == 'module_admin')
            return $this->admin_perms;
        else
            return $this->read_perms;

    }
}
