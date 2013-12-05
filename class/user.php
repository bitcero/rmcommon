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
    
    public function __construct($id=''){
        
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("users");
        $this->setNew();
        $this->initVarsFromTable();

        // Customers data
        $this->initVar( 'id_user', 'int' );
        $this->initVar( 'email2', 'varchar', '', 100 );
        $this->initVar( 'homephone', 'varchar', '', 20 );
        $this->initVar( 'cellphone', 'varchar', '', 20 );
        $this->initVar( 'state', 'varchar', '', 30 );
        $this->initVar( 'city', 'varchar', '', 50 );
        $this->initVar( 'address', 'text', '' );
        $this->initVar( 'comments', 'text' );
        $this->initVar( 'commlevel', 'varchar', 'default' );

        if ($id != '' && $this->loadValues($id))
            $this->unsetNew();
        elseif ($id!='') {
            $this->primary = 'uname';
            if($this->loadValues($id))
                $this->unsetNew ();
            $this->primary = 'uid';
        }

        // Si es un cliente, cargamos los datos
        if ( $this->getVar("type") == 'user' || $this->isNew() )
            return;

        $sql = "SELECT * FROM " . $this->db->prefix("mod_customers_data") . " WHERE id_user=" . $this->id();
        $result = $this->db->query( $sql );

        while ( $row = $this->db->fetchArray( $result ) ){
            $this->assignVars( $row );
        }

    }
    
    function setGroups($groupsArr){
        $this->groups = array();
	if (is_array($groupsArr))
            $this->groups =& $groupsArr;
    }
    
    public function &getGroups(){
        
        if (!empty($this->groups)) return $this->groups;
        
        $sql = 'SELECT groupid FROM '.$this->db->prefix('groups_users_link').' WHERE uid='.intval($this->getVar('uid'));
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
    	while ($row = $this->db->fetchArray($result)){
			$groups[] = $row;
    	}
    	return $groups;
    }
    
    function save(){
        $ret = true;
        $status = $this->isNew();
	    /**
        * Guardmaos los datos del usuarios
        */
        if ($this->isNew()){
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
        if (!empty($this->groups)){
            if (!$this->isNew())
                $this->db->queryF("DELETE FROM ".$this->db->prefix("groups_users_link")." WHERE uid='".$this->getVar('uid')."'");
		
            $sql = "INSERT INTO ".$this->db->prefix("groups_users_link")." (`groupid`,`uid`) VALUES ";
            foreach ($this->groups as $k){
                $sql .= "('$k','".$this->getVar('uid')."'),";
            }

            $sql = substr($sql, 0, strlen($sql) - 1);

            $this->db->queryF($sql);
        }

        // Datos de cliente
        if ( $this->getVar('type') == 'user' )
            return $ret;

        $sql = "SELECT COUNT(*) FROM " . $this->db->prefix("mod_customers_data") . " WHERE id_user = " . $this->id();
        list( $num ) = $this->db->fetchRow( $this->db->query( $sql ) );

        // El usuario ya existe
        if ( $num > 0 )
            $sql = "UPDATE " . $this->db->prefix("mod_customers_data") . " SET
                    email2='". $this->getVar('email2')."',
                    homephone='" . $this->getVar('homephone') . "',
                    cellphone='" . $this->getVar('cellphone') . "',
                    state='" . $this->getVar('state') . "',
                    city='" . $this->getVar('city') . "',
                    address='" . $this->getVar('address', 'e') . "',
                    `comments`='" . $this->getVar('comments', 'e') . "',
                    commlevel='" . $this->getVar('commlevel') . "'
                    WHERE id_user = " . $this->id();
        else
            $sql = "INSERT INTO " . $this->db->prefix("mod_customers_data") . " (`id_user`,`email2`,`homephone`,`cellphone`,`state`,`city`,`address`,`individual`,`comments`,`commlevel`)
                    VALUES ('" . $this->id() . "', '" . $this->getVar('RFC') . "',
                    '" . $this->getVar('email2') . "',
                    '" . $this->getVar('homephone') . "',
                    '" . $this->getVar('cellphone') . "',
                    '" . $this->getVar('state') . "',
                    '" . $this->getVar('city') . "',
                    '" . $this->getVar('address', 'e') . "',
                    '" . $this->getVar("comments",'e') ."',
                    '" . $this->getVar('commlevel') . "')";

        $ret = $this->db->queryF( $sql );

        if( !$ret )
            $this->addError( $this->db->error() );
            
        return $ret;
		
    }

    public function delete(){

        $this->deleteFromTable();

    }
    
}