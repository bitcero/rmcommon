<?php
// $Id: blocks.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMBlockPosition extends RMObject
{
    
    public function __construct($id=''){
        
        $this->db =& XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("mod_rmcommon_blocks_positions");
        $this->setNew();
        $this->initVarsFromTable();
        
        if ($id=='') return;
        
        if ($this->loadValues($id)){
            $this->unsetNew();
            return;
        }
        
        $this->primary = 'tag';
        if($this->loadValues($id))
            $this->unsetNew();
            
        $this->primary = 'id_position';
        
    }
    
    /**
     * Almacena la infromación de ls posición.
     * Si la posición ya existe actualiza los datos, en caso contrario
     * crea un nuevo registro en la tabla.
     * 
     * @return bool
     */
    public function save(){
        
        if($this->isNew())
            return $this->saveToTable();
        else
            return $this->updateTable();
        
    }
    
    /**
     * Elimina toda la ifnromación de la posición incluyendo los bloques existetentes.
     * Use este método con cuidado ya que no permite recuperar datos.
     * 
     * @return bool
     */
    public function delete(){
        
        $result = $this->db->query("SELECT bid FROM ".$this->db->prefix("mod_rmcommon_blocks")." WHERE canvas=".$this->id());
        $ids = array();
        while($row = $this->db->fetchArray($result)){
            $ids[] = $row['bid'];
        }

        // If there exists blocks assigned to positions
        // then delete them
        if(!empty($ids)){

            // Delete associations
            if (!$this->db->queryF("DELETE FROM ".$this->db->prefix("mod_rmcommon_blocks_assignations")." WHERE bid IN(".  implode(',', $ids) .")")){
                $this->addError($this->db->error());
                return false;
            }

            // Delete permissions
            if (!$this->db->queryF("DELETE FROM ".$this->db->prefix("group_permission")." WHERE gperm_itemid IN (".implode(',',$ids).") AND gperm_name='block_read'")){
                $this->addError($this->db->error());
                return false;
            }

            // Delete blocks
            if (!$this->db->queryF("DELETE FROM ".$this->db->prefix("mod_rmcommon_blocks")." WHERE bid IN (".implode(',',$ids).")")){
                $this->addError($this->db->error());
                return false;
            }

        }
        
        return $this->deleteFromTable();
        
    }
    
}
