<?php
// $Id: image.php 1020 2012-09-04 16:15:09Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* Class to handle images created from Image Manager
*/

class RMImage extends RMObject
{
    use RMSingleton;
    /**
     * Used when a default size is specified
     * @var int
     */
    private $selected_size = '';
    /**
     * Stores all sizes for category
     * @param array
     */
    private $sizes = array();

	public function __construct($id=null){
		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("mod_rmcommon_images");
        $this->setNew();
        $this->initVarsFromTable();
        if ($id==null){
            return;
        }
        
        if ($this->loadValues($id)){
            $this->unsetNew();
        }
	}
	
	public function id(){
		return $this->getVar('id_img');
	}

    /**
     * Load data from database table according to given parameters
     * @param string $params must be passed as string separated by ":" (e.g. 1:thumbnail:url:title) where second parameter is selected size if any
     * @return bool|string
     */
    public function load_from_params($params){

        if($params=='') return false;
        $p = explode(":", $params);

        if((int)$p[0] <= 0) return false;

        if($this->loadValues((int)$p[0])) $this->unsetNew();
        $this->selected_size = (int)$p[1];
        
        $p[2] = $p[2]!='' ? urldecode($p[2]) : '';
        $p[3] = $p[3]!='' ? urldecode($p[3]) : '';

        return $p[2];

    }

    /**
     * Get all image sizes
     * @return array
     */
    public function get_sizes_data(){
        if(empty($this->sizes)){
            $cat = new RMImageCategory($this->getVar('cat'));
            if($cat->isNew()) return false;
            // Get sizes
            $this->sizes = $cat->getVar('sizes');
        }

        return $this->sizes;
    }

    /**
     * Get the url where files will be stored
     * @return string
     */
    public function get_files_url(){

        $url = XOOPS_UPLOAD_URL.'/'.date('Y', $this->getVar('date')).'/'.date('m',$this->getVar('date'));
        return $url;

    }

    /**
     * Get the path where files will be stored
     * @return string
     */
    public function get_files_path(){

        $path = XOOPS_UPLOAD_PATH.'/'.date('Y', $this->getVar('date')).'/'.date('m',$this->getVar('date'));
        return $path;

    }

    /**
     * Constructs the URL for image according to defined size
     * @param int Specific size to construct the url
     * @return string
     */
    public function url($size = ''){

        if($size != '' && $this->selected_size != '')
            $size = $this->selected_size;

        if($this->isNew()) return false;

        $this->get_sizes_data();
        
        $url = $this->get_files_url();
        $info = pathinfo($this->getVar('file'));

        foreach( $this->sizes as $item ){

            if ( $item['name'] == $size ){
                $url .= '/sizes/'.$info['filename'].'-'.$item['name'];
                $url .= '.'.$info['extension'];
                return $url;
            }

        }

        $url .= '/' . $this->getVar('file');


        return $url;

    }

    public function get_smallest(){

        if($this->isNew()) return false;

        $this->get_sizes_data();
        $ps = 0; // Previous size
        $small = 0;
        
        foreach($this->sizes as $k => $size){
            $ps = $ps==0?$size['width']:$ps;
            if($size['width']<$ps){
                $ps = $size['width'];
                $small = $k;
            }

        }

        return $this->url($small);

    }
    
    /**
    * Get all image versions with url
    * @return array
    */
    public function get_all_versions(){
        
        if($this->isNew()) return false;
        
        $this->get_sizes_data();
        $ret = array();
        foreach($this->sizes as $k => $size){
            $ret[$size['name']] = $this->url($k);
        }
        
        return $ret;
    }
    
    public function get_version($name){
        
        if($this->isNew()) return false;
        
        $this->get_sizes_data();
        $ret = array();
        foreach($this->sizes as $k => $size){
            if($size['name'] == $name)
                return $this->url($name);
        }
        
        return '';
        
    }

    public function get_by_size( $width ){

        $sizes = $this->get_sizes_data();
        $sorted = array();
        foreach ( $sizes as $i => $size ){

            $sorted[ $i ] = $size['width'];

        }

        asort( $sorted );
        foreach( $sorted as $id => $size ){
            if ( $size >= $width )
                return $this->url( $sizes[$id]['name'] );

        }

        return $this->getOriginal();

    }
    
    public function getOriginal(){
        $url = $this->get_files_url();
        $url .= '/' . $this->getVar('file');
        return $url;
    }
    
    public function save(){
        
        if ($this->isNew()){
            return $this->saveToTable();
        } else {
            return $this->updateTable();
        }
        
    }
    
    public function delete(){
        $path = XOOPS_UPLOAD_PATH.'/'.date('Y', $this->getVar('date')).'/'.date('m',$this->getVar('date')).'/';
        $sizes = $this->get_sizes_data();
        
        $info = pathinfo($this->getVar('file'));
        foreach($sizes as $size){
            unlink($path.'sizes/'.$info['filename'].'-'.$this->sizes[$size]['name'].'.'.$info['extension']);
        }
        unlink($path.$this->getVar('file'));
        
		return $this->deleteFromTable();
    }
	
}
