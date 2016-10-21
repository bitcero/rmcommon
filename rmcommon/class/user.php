<?php
// $Id: image.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMUser extends RMObject
{
    private $groups = array();

    public function __construct($id='', $use_email = false, $pass = '' ){

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("users");
        $this->setNew();
        $this->initVarsFromTable();

        /**
         * Find user using the email
         */
        if ( $use_email ){

            if ( '' == $id )
                return null;

            $this->primary = 'email';
            if($this->loadValues($id))
                $this->unsetNew ();
            $this->primary = 'uid';
            return true;

        }

        if ($id != '' && is_numeric($id) && $this->loadValues((int)$id))
            $this->unsetNew();
        elseif ($id!='') {
            $this->primary = 'uname';
            if($this->loadValues($id))
                $this->unsetNew ();
            $this->primary = 'uid';
        }

    }

    function setGroups($groupsArr){
        $this->groups = array();
        if (is_array($groupsArr))
                $this->groups =& $groupsArr;
    }

    public function &getGroups(){

        if (!empty($this->groups)) return $this->groups;

        $sql = 'SELECT groupid FROM '.$this->db->prefix('groups_users_link').' WHERE uid=' . (int)$this->getVar('uid');
        $result = $this->db->query($sql);

        if (!$result) {
            return false;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $this->groups[] = $myrow['groupid'];
        }

        return $this->groups;
    }

    function groups($data=false, $fields='groupid'){
        $groups =& $this->getGroups();

        if (!$data || $fields=='') return $groups;

        // Gets all groups based in their id
        $sql = "SELECT ".($fields!='' ? "$fields" : '')." FROM ".$this->db->prefix("groups")." WHERE groupid IN(".implode(',',$groups).")";
        $result = $this->db->query($sql);
        $groups = array();
        while ($row = $this->db->fetchArray($result)) {
            $groups[] = $row;
        }

        return $groups;
    }

    function isAdmin($module_id = null)
    {
        if (is_null($module_id)) {
            $module_id = isset($GLOBALS['xoopsModule']) ? $GLOBALS['xoopsModule']->getVar('mid', 'n') : 1;
        } elseif ((int)$module_id < 1) {
            $module_id = 0;
        }
        $moduleperm_handler = xoops_getHandler('groupperm');

        return $moduleperm_handler->checkRight('module_admin', $module_id, $this->getGroups());
    }

    function save(){
        $ret = true;
        $status = $this->isNew();
        /**
        * Guardmaos los datos del usuarios
        */
        if ($this->isNew()) {
                $ret = $this->saveToTable();
        } else {
                $ret = $this->updateTable();
        }
        /**
        * Si ocurrió un error al guardar los datos
        * entonces salimos del método. No se pueden
        * guardar los grupos hasta que esto se haya realizado
        */
        if (!$ret) return $ret;
        /**
        * Asignamos los grupos
        */
        if (!empty($this->groups)) {
            if (!$this->isNew())
                $this->db->queryF("DELETE FROM ".$this->db->prefix("groups_users_link")." WHERE uid='".$this->getVar('uid')."'");

            $sql = "INSERT INTO ".$this->db->prefix("groups_users_link")." (`groupid`,`uid`) VALUES ";
            foreach ($this->groups as $k) {
                $sql .= "('$k','".$this->getVar('uid')."'),";
            }

            $sql = substr($sql, 0, strlen($sql) - 1);

            $this->db->queryF($sql);
        }

        return $ret;

    }

    public function delete(){

        $this->deleteFromTable();

    }

}
