<?php
// $Id: commentuser.php 902 2012-01-03 07:09:16Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMCommentUser extends RMObject
{
    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix('mod_rmcommon_comments_assignations');
        $this->setNew();
        $this->initVarsFromTable();
        if (null === $id) {
            return;
        }

        if ($this->loadValues($id)) {
            $this->unsetNew();

            return;
        }

        $this->primary = 'email';
        if ($this->loadValues($id)) {
            $this->unsetNew();
            $this->primary = 'id_user';

            return;
        }

        $this->primary = 'id_user';
    }

    public function id()
    {
        return $this->getVar('id_user');
    }

    public function save()
    {
        if ($this->isNew()) {
            return $this->saveToTable();
        }

        return $this->updateTable();
    }
}
