<?php
// $Id: comment.php 902 2012-01-03 07:09:16Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMComment extends RMObject
{
    /**
    * User owner
    */
    private $user = array();

    public function __construct($id=null){

        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("mod_rmcommon_comments");
        $this->setNew();
        $this->initVarsFromTable();

        if ($id==null){
            return null;
        }

        if (!$this->loadValues($id)) return false;

        $this->unsetNew();

        return null;
    }

    public function id(){
        return $this->getVar('id_com');
    }

    private function load_user(){

        $db = $this->db;

        $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_comments_assignations")." WHERE id_user=".$this->getVar('user');
        $result = $db->query($sql);
        if ($db->getRowsNum($resul)<=0) return;

        $row = $db->fetchArray($result);
        $this->user = $row;

    }

    /**
    * Save comment
    */
    public function save(){

        // To check or modify data before save this
        $ret = RMEvents::get()->run_event('rmcommon.saving.comment', $this);

        if ($this->isNew()){
            $sr = $this->saveToTable();
        } else {
            $sr = $this->updateTable();
        }

        return $ret && $sr;

    }

    /**
    * Delete Comment
    */
    public function delete(){

		if (!$this->deleteFromTable()) return false;

		// Update comments parent
		$sql = "UPDATE ".$this->db->prefix("mod_rmcommon_comments")." SET parent=".$this->getVar('parent')." WHERE parent=".$this->id();
		if (!$this->db->queryF($sql)) $this->addError($this->db->error());

		// Reduce user posts number
		$user = new RMCommentUser($this->getVar('user'));
		if ($user->isNew()) return true;

		if($user->getVar('xuid')<=0) return true;

		$sql = "UPDATE ".$this->db->prefix("users")." SET posts=posts-1 WHERE uid=".$user->getVar('xuid');
		if(!$this->db->queryF($sql)){
			$this->addError($this->db->error());
			return false;
		}

		return true;

    }

}
