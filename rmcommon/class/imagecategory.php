<?php
// $Id: imagecategory.php 902 2012-01-03 07:09:16Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Class for manage categories from images manager
*/
class RMImageCategory extends RMObject
{
    public function __construct($id=null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("mod_rmcommon_images_categories");
        $this->setNew();
        $this->initVarsFromTable();
        $this->setVarType('groups', XOBJ_DTYPE_ARRAY);
        $this->setVarType('sizes', XOBJ_DTYPE_ARRAY);
        if ($id==null) {
            return;
        }
        
        if ($this->loadValues($id)) {
            $this->unsetNew();
        }
    }
    
    public function id()
    {
        return $this->getVar('id_cat');
    }
    
    public function save()
    {
        if ($this->isNew()) {
            return $this->saveToTable();
        } else {
            return $this->updateTable();
        }
    }
    
    public function max_file_size()
    {
        $size = $this->getVar('filesize') * $this->getVar('sizeunit');
        return $size;
    }
    
    /**
    * Check if given user is allowed
    * @param object XoopsUser object
    * @return bool
    */
    public function user_allowed_toupload(XoopsUser $user)
    {
        $groups =& $user->getGroups();
        $allowed = $this->getVar('groups');
        
        foreach ($groups as $id) {
            if (in_array($id, $allowed['write'])) {
                return true;
            }
        }
        
        return false;
    }
    
    public function delete()
    {
        return $this->deleteFromTable();
    }
}
