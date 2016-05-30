<?php
// $Id: formuser.class.php 934 2012-02-17 16:35:33Z i.bitcero $
// --------------------------------------------------------------
// EXM System
// Content Management System
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* @desc Clase para el manejo de campos de formulario de tipo usuarios
*/
class RMFormUser extends RMFormElement
{
	// Eventos
	private $_onchange = '';
	
	/**
	* @param string Texto del campo
	* @param string Nombre del Campo
	* @param bool Seleccion múltiple
	* @param array Valores seleccionados por defecto
	* @param int Limite de resultados por página de usuarios
	* @param int Ancho de la ventana
	* @param int Alto de la ventana
	*/
	public function __construct($caption, $name, $multi = false, $select=array(), $limit=20, $width=600,$height=300, $showall = 0, $enable=true){

        if(is_array($caption)){
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('limit', $limit, 20);
            $this->setWithDefaults('multi', $multi, false);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('showall', $showall, 0);
            $this->setWithDefaults('can_change', $enable, true);
            $this->setWithDefaults('selected', $select, array());
        }

        $this->add('class', 'form_users_container');
        if ($this->get('multi')){
            $this->add('class', 'checkbox');
        } else {
            $this->add('class', 'radio');
        }

        $this->setIfNotSet('selected', array());
        $this->setIfNotSet('id', $this->get('name'));

        $this->suppressList = array_merge($this->suppressList, ['limit', 'multi', 'showall', 'can_change', 'selected']);

        !defined('RM_FRAME_USERS_CREATED') ? define('RM_FRAME_USERS_CREATED', 1) : '';
	}
	
	public function button($enable){
		$this->can_change = $enable;
	}
	
	/**
	* @desc Crea un manejador para el evento onchange
	*/
	public function onChange($action){
		$this->_onchange = base64_encode(addslashes($action));
	}
	
	/**
	* Show the Users field
	* This field needs that form.css, jquery.css and forms.js would be included.
	*/
	public function render(){
		
		RMTemplate::getInstance()->add_script('forms.js', 'rmcommon', array('footer' => 1));
		RMTemplate::getInstance()->add_script('jquery.validate.min.js', 'rmcommon',  array('directory' => 'include', 'footer' => 1));
		RMTemplate::getInstance()->add_style('forms.min.css','rmcommon', ['id' => 'forms-css']);
        
		if (function_exists("xoops_cp_header")){
        	RMTemplate::get()->add_style('jquery.css','rmcommon');
		} else {
			RMTemplate::get()->add_style('jquery.css','rmcommon');
		}

        $attributes = $this->renderAttributeString();
		
		$rtn = '<div id="'.$this->id().'-users-container" ' . $attributes . '>
				<ul id="'.$this->id().'-users-list">';
		$db = XoopsDatabaseFactory::getDatabaseConnection();
		
		if ($this->get('showall') && in_array(0, $this->get('selected'))){
			$rtn .= "<li id='".$this->id()."-exmuser-0'>\n
                        <label>
                        <a href='javascript:;' onclick=\"users_field_name='".$this->id()."'; usersField.remove(0);\"><span>delete</span></a>
                        <input type='".($this->get('multi') ? 'checkbox' : 'radio')."' name='".($this->get('multi') ? $this->get('name').'[]' : $this->get('name'))."' id='".$this->id()."-0'
				 		value='0' checked='checked' /> ".__('All Users','rmcommon')."
                        </label></li>";
		}
        
        $selected = $this->get('selected');
		
		if (is_array($selected) && !empty($selected) && !(count($selected)==1 && $selected[0]==0)){
			$sql = "SELECT uid,uname FROM ".$db->prefix("users")." WHERE ";
			$sql1 = '';
			if ($this->multi){
				foreach ($selected as $id){
					if ($id!=0) $sql1 .= $sql1 == '' ? "uid='$id'" : " OR uid='$id'";
				}
			} else {
				if ($selected[0]!=0) $sql1 = "uid='".$selected[0]."'";
			}
			$result = $db->query($sql.$sql1);
			$selected = '';
			while ($row = $db->fetchArray($result)){
				$rtn .= "<li id='".$this->id()."-exmuser-$row[uid]'>\n
						<label>";
                $rtn .= $this->get('can_change') ? " <a href='#' onclick=\"users_field_name='".$this->id()."'; usersField.remove($row[uid]);\"><span>delete</span></a>" : '';
                $rtn .= "<input type='".($this->get('multi') ? 'checkbox' : 'radio')."' name='".($this->get('multi') ? $this->get('name').'[]' : $this->get('name'))."' id='".$this->id()."-".$row['uid']."'
				 		value='$row[uid]' checked='checked' /> 
                        $row[uname] ";
                $rtn .= "</label></li>";
			}
		}
		
		$rtn .= "</ul><br />";
		if ($this->get('can_change')){
			$rtn .= "<button type='button' class='btn btn-info btn-sm' onclick=\"usersField.form_search_users('".$this->id()."',".$this->get('limit').",".intval($this->get('multi')).",'".XOOPS_URL."');\">".__('Users...','rmcommon')."</button>";
		    $rtn .= '<div class="modal fade smartb-form-dialog users-form-selector" id="'.$this->id().'-dialog-search">
					    <div class="modal-dialog">
					        <div class="modal-content">
					            <div class="modal-header">
					                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					                <h4 class="modal-title">'.__('Select users', 'rmcommon').'</h4>
					            </div>
					            <div class="modal-body">

					            </div>
					        </div>
					    </div>
					</div>';
		}
		$rtn .= '</div>';
		return $rtn;
	}
}

class RMFormFormUserSelect extends RMFormElement
{
    private $selected = array();
    private $limit = 100;
    private $width = 600;
    private $height = 300;
    private $showall = 0;
    
    // Eventos
    private $_onchange = '';
    
    /**
    * @param string Texto del campo
    * @param string Nombre del Campo
    * @param bool Seleccion múltiple
    * @param array Valores seleccionados por defecto
    * @param int Limite de resultados por página de usuarios
    * @param int Ancho de la ventana
    * @param int Alto de la ventana
    */
    public function __construct($caption, $name, $select=array(), $limit=36, $width=600,$height=300, $showall = 0){
        $this->selected = $select;
        $this->limit = $limit;
        $this->setCaption($caption);
        $this->setName($name);
        $this->width = $width<=0 ? 600 : $width;
        $this->height = $height<=0 ? 300 : $height;
        $this->showall = $showall;
        !defined('RM_FRAME_USERS_CREATED') ? define('RM_FRAME_USERS_CREATED', 1) : '';
    }
    
    public function render(){
        
    }
}
