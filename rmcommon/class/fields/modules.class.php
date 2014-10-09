<?php
// $Id: modules.class.php 1064 2012-09-17 16:46:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


class RMFormModules extends RMFormElement
{
	private $multi = 0;
	private $type = 0;
	private $selected = null;
	private $cols = 2;
	private $inserted = array();
	private $dirnames = true;
	private $subpages = 0;
	private $selectedSubPages = array();
	
	/**
	 * Constructor
	 * @param string $caption
	 * @param string $name Nombre del campo
	 * @param int $multi Selecciona multiple activada o desactivada
	 * @param int $type 0 = Select, 1 = Tabla
	 * @param array $selected Valor seleccionado por defecto
	 * @param array $selected Grupo de vlores seleccionado por defecto
	 * @param int $cols Numero de columnas para la tabla o filas para un campo select multi
	 * @param array $insert Array con valores para agregar a la lista
	 * @param bool $dirnames Devolver nombres de directorios (true) o ids (false)
	 * @param int Mostrar Subpáginas
	 */
	function __construct($caption, $name, $multi = 0, $type = 0, $selected = null, $cols = 2, $insert = null, $dirnames = true, $subpages = 0){
		$this->setName($multi ? str_replace('[]', '', $name) : $name);
		$this->setCaption($caption);
		$this->multi = $multi;
		$this->type = $type;
		$this->cols = $cols;
		$this->selected = isset($_REQUEST[$name]) ? $_REQUEST[$name] : $selected;
		$this->inserted = $insert;
		$this->dirnames = $dirnames;
		$this->subpages = $subpages;
		!defined('RM_FRAME_APPS_CREATED') ? define('RM_FRAME_APPS_CREATED', 1) : '';
	}
	public function multi(){
		return $this->multi;
	}
	public function setMulti($value){
		if ($value==0 || $value==1){
			//$this->setName($value ? str_replace('[]','',$this->getName()).'[]' : str_replace('[]','',$this->getName()));
			$this->multi = $value;
		}
	}
	public function type(){
		return $this->type;
	}
	public function setType($value){
		return $this->type = $value;
	}
	public function selected(){
		return $this->selected;
	}
	public function setSelected($value){
		return $this->selected = $value;
	}
	public function sizeOrCols(){
		return $this->cols;
	}
	public function setSizeOrCols($value){
		return $this->cols = $value;
	}
	public function inserted(){
		return $this->inserted;
	}
	/**
	 * Inserta nuevas opciones en el campo
	 * @param array $value Array con valor=>caption para las opciones a insertar
	 */
	public function setInserted($value){
		$this->inserted = array();
		$this->inserted = $value;
	}
	
	public function dirnames(){
		return $this->dirnames;	
	}
	/**
	 * Establece si se devuelven los valores con 
	 * el nombre del directorio del módulo o con 
	 * el identificador del módulo
	 * @param bool $value
	 */
	public function setDirNames($value = true){
		$this->dirnames = $value;
	}
	/**
	* @desc Establece las subpáginas seleccionadas por defecto
	* @param array Subpáginas seleccionadas
	*/
	public function subpages($subs){
		$this->selectedSubPages = $subs;
	}
	
	function render(){
        $module_handler =& xoops_gethandler('module');
        $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        if($this->subpages) $criteria->add(new Criteria('dirname', 'system'), 'OR');
        $modules = array();
        $modules[-1] = __('All','rmcommon');
        if (is_array($this->inserted)) $modules = $this->inserted;
        foreach ($module_handler->getList($criteria, $this->dirnames) as $k => $v){
        	$modules[$k] = $v;
        }

        if ($this->type){
            // Add js script
            RMTemplate::get()->add_script( 'modules_field.js', 'rmcommon', array('directory' => 'include') );

        $pagesOptions = array();

		$rtn = '<div class="modules-field" id="modules-field-'.$this->getName().'">
		            <div>
		                <h4>'.__('Available Modules','rmcommon').'</h4>
		            <ul>';
            
		$i = 1;
		foreach ($modules as $k => $v){
            $app = RMModules::load_module($k);

            $rtn .= "<li>";
            $name = $this->multi ? $this->getName()."[$k]" : $this->getName();
            if ($this->multi){
			    $rtn .= "<input type='checkbox' value='$k'".($k == 0 ? "
			    data-checkbox='module-item'" : " data-oncheck='module-item'")."
			    name='".$name."'
			    id='".$this->id()."-$k'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " checked='checked'" : '') : '').( $k != -1 ? " data-checkbox='module-item-".$k."'" : '' )."> ";
                if($this->subpages)
                    $rtn .= '<a href="#">'.$v.'</a>';
                else
                    $rtn .= $v;
            } else {
			    $rtn .= "<input type='radio' value='$k' name='".$this->getName()."' id='".$this->id()."-$k'".(!empty($this->selected) ? ($k == $this->selected ? " checked='checked'" : '') : '')." /> $v";
            }

            /**
            * Mostramos las subpáginas
            */
            if ($this->subpages && $k>0){
                if($app->dirname()=='system'){
                    $subpages = array(
                        'home-page'     => __('Home Page','rmcommon'),
                        'user'          => __('User page','dtransport'),
                        'profile'       => __('User profile page','rmcommon'),
                        'register'      => __('Users registration','rmcommon'),
                        'edit-user'     => __('Edit user','rmcommon'),
                        'readpm'        => __('Read PM','rmcommon'),
                        'pm'            => __('Private messages','rmcomon')
                    );
                } else {
			        $subpages = $app->getInfo('subpages');
                }
                if(!empty($subpages)){
                    $selected = $this->selectedSubPages;
                    $cr = 0;

                    $rtns ="<ul class=\"subpages-container subpages-".$k."\" data-module=\"".$k."\">";
                    $j = 2;
                    $cr = 2;
                    if (!is_array($subpages)) $subpages = array();

                    foreach ($subpages as $page=>$caption){
                        $rtns .= "<li><input type='checkbox' data-oncheck='module-item-".$k."' name='".$name."[subpages][$page]' id='subpages[$k][$page]' value='$page'".(is_array($subpages) && @in_array($page, $selected[$k]) ? " checked='checked'" : '')." /> $caption</li>";
                        $j++;
                        $cr++;
                    }
                    $rtns .= '</ul>';

                    $pagesOptions[] = $rtns;
                    $rtns = '';
                }
			   
            }
                    
            $rtn .= "</li>";
            $i++;
		}

		$rtn .= "</ul>
		            </div>";

        if($this->subpages){

            $rtn .= '<div><h4>'.__('Inner Pages','rmcommon').'</h4>';

            foreach($pagesOptions as $page){
                $rtn .= $page;
            }

            $rtn .= '</div>';

        }

		$rtn .= "</div>";
            } else {
		if ($this->multi){
                    $name = $this->getName()."[$k]";
                    $rtn = "<select name='".$name."' id='".$this->id()."' size='$this->cols' multiple='multiple'>";
                    foreach ($modules as $k => $v){
                        $rtn .= "<option value='$k'".(is_array($this->selected) ? (in_array($k, $this->selected) ? " selected='selected'" : '') : '').">$v</option>";
                    }
                    $rtn .= "</select>";
		} else {
                    $rtn = "<select name='".$this->getName()."' id='".$this->getName()."'>";
                    foreach ($modules as $k => $v){
                        $rtn .= "<option value='$k'".(!empty($this->selected) ? ($k==$this->selected ? " selected='selected'" : '') : '').">$v</option>";
                    }
                    $rtn .= "</select>";
		}
            }
		
            return $rtn;
		
	}
}
