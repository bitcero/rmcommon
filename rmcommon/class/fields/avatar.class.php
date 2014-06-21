<?php
// $Id: avatar.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// RMSOFT Common Utilities
// Utilidades comunes para módulos de Red México
// CopyRight © 2005 - 2006. Red México
// Autor: BitC3R0
// http://www.redmexico.com.mx
// http://www.exmsystem.net
// --------------------------------------------
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License as
// published by the Free Software Foundation; either version 2 of
// the License, or (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public
// License along with this program; if not, write to the Free
// Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,
// MA 02111-1307 USA
// --------------------------------------------------------------
// @copyright: 2007 - 2008. Red México
// @author: BitC3R0

/**
 * Clase para la generación de campos SELECT
 */
class RMFormAvatarField extends RMFormElement
{
	private $_multi = 0;
	private $_size = 5;
	private $_select = array();
	private $_showtype = 0;
	private $_showdesc = 0;
	private $_cols = 2;
    /**
    * @desc Esta variable es utilizada para indicar si se deben cargar
    *       solo los avatares cargados por el usuario
    */
    private $_onlyuser = 0;
	/**
	 * Constructor de la clase
	 * @param string $caption Texto de la etiqueta
	 * @param string $name Nombre del elemento
	 * @param int $multi Seleccion múltiple (0 = Inactivo, 1 = Activo)
	 */
	function __construct($caption, $name, $multi=0, $type=0, $cols=2, $select=array(), $user=0){
		$this->setCaption($caption);
		$this->setName($name);
		if (isset($_REQUEST[$name])) $this->_select = $_REQUEST[$name];
		$this->_multi = $multi;
		$this->_showtype = $type;
		$this->_cols = $cols;
        $this->_onlyuser = $user;
		
		if (isset($_REQUEST[$this->getName()])){
			$this->_select = $_REQUEST[$this->getName()];
		} else {		
			$this->_select = $select;
		}
	}
	/**
	 * Establece el comportamiento de seleccion del campo avatares.
	 * Si $_multi = 0 entonces olo se puede seleccionar un avatar a la vez. En caso contrario
	 * el campo permite la selección de múltiples avatares
	 * @param int $value 1 o 2
	 */
	public function setMulti($value){
		if ($value==0 || $value==1){
			$this->_multi = $value;
		}
	}
	/**
	 * @return int
	 */
	public function getMulti(){
		return $this->_multi;
	}
	/**
	 * Indica los elementos seleccionados por defecto.
	 * Este valor debe ser pasado como un array conteniendo los ideneitificadores
	 * de los avatares (ej. array(0,1,2,3)) o bien como una lista delimitada por comas
	 * conteniendo tambien los identificadores de avatares (ej, 1,2,3,4)
	 * @param array $value Identificadores de los avatares
	 * @param string $value Lista delimitada por comas con identificadores de los avatares
	 */
	public function setSelect($value){
		if (is_array($value)){
			$this->_select = $value;
		} else {
			$this->_select = explode(',',$value);
		}
	}
	/**
	 * Devuelve el array con los identificadores de los avatares
	 * seleccionado por defecto.
	 * @return array
	 */
	public function getSelect(){
		return $this->_select;
	}
	/**
	 * Establece la forma en que se mostrarán los avatares.
	 * Esto puede ser en forma de lista o en forma de menu
	 * @param int $value 0 ó 1
	 */
	public function setShowType($value){
		if ($value==0 || $value==1) $this->_showtype = $value;
	}
	/**
	 * Devuelve el identificador de la forma en que se muestran los elementos
	 * @return int
	 */ 
	public function getShowType(){
		return $this->_showtype;
	}
	/**
	 * Establece el número de columnas para el menu.
	 * Cuando los grupos se mostrarán en forma de menú esta opción 
	 * permite especificar el número de columnas en las que se ordenarán.
	 * @param int $value Número de columnas
	 */
	public function setCols($value){
		if ($value>0) $this->_cols = $value;
	}
	/**
	 * Devuelve el número de columnas del menú.
	 * @return int
	 */
	public function getCols(){
		return $this->_cols;
	}
	/**
	 * Genera el código HTML para este elemento.
	 * @return string
	 */
	public function render(){
		$db = EXMDatabase::get();
        $sql = "SELECT * FROM ".$db->prefix("avatar");
        if ($this->_onlyuser){
            global $exmUser;
            if ($exmUser){
                $useravatar = $exmUser->getVar('user_avatar');
                $sql .= " WHERE avatar_display='1' AND (avatar_type='0' OR (avatar_type='1' AND avatar_file='$useravatar'))";
                unset($useravatar);
            }
        }
        $sql .= " ORDER BY avatar_name";
		$result = $db->query($sql);
		$rtn = '';
		$col = 1;
		
		$typeinput = $this->_multi ? 'checkbox' : 'radio';
		$name = $this->_multi ? $this->getName().'[]' : $this->getName();

		if ($this->_showtype){
			$rtn = "<div style='height: 200px; overflow: auto;'><table cellspacing='2' cellpadding='3' border='0'>";
			$rtn .= "<tr>";
			$rtn .= "<td align='left'><label><input type='$typeinput' name='$name' id='$name' value=''";
			if (empty($this->_select)) $rtn .= " checked='checked'";
			$rtn .= ">"._RMS_CF_NOAVATAR."</label></td>";
			$col++;
			while ($row = $db->fetchArray($result)){
				
				if ($col>$this->_cols){
					$rtn .= "</tr><tr>";
					$col = 1;
				}
				
				$rtn .= "<td align='left'><label><img src='".ABSURL."/uploads/avatars/$row[avatar_file]' align='absmiddle' /> <input type='$typeinput' name='$name' id='$name' value='$row[avatar_file]'";
				if (is_array($this->_select)){
					if (in_array($row['avatar_file'], $this->_select)){
						$rtn .= " checked='checked'";
					}
				}
				$rtn .= ">$row[avatar_name]</label>";
				
				$rtn .= "</td>";
				
				$col++;
				
			}
			$rtn .= "</tr>";
			$rtn .= "</table></div>";
		} else {
			$rtn = "<script type='text/javascript'>
						function showImageAvatar(){
							sel = $('$name');
							var img = sel.options[sel.selectedIndex].value;
							div = $('avatarimg');
							div.innerHTML = \"<img src='".ABSURL."/uploads/avatars/\"+img+\"' />\";
						}
						
						
					</script>";
			$rtn .= "<div id='avatarimg' style='float: right;'>";
			if (count($this->_select)>0){
				$rtn .= "<img src='".ABSURL."/uploads/avatars/".$this->_select[0]."' border='0' alt='' />";
			}
			$rtn .= "</div>
					<select name='$name' id='".$this->id()."' onchange='showImageAvatar();'";
			$rtn .= $this->_multi ? " multiple='multiple' size='5'" : "";
			$rtn .= "><option value='0'";
			if (is_array($this->_select)){
				if (in_array(0, $this->_select)){
					$rtn .= " selected='selected'";
				}
			} else {
				$rtn .= " selected='selected'";
			}
			
			$rtn .= ">"._RMS_CF_ALL."</option>";
			
			while ($row = $db->fetchArray($result)){
				$rtn .= "<option value='$row[avatar_file]'";
				if (is_array($this->_select)){
					if (in_array($row['avatar_file'], $this->_select)){
						$rtn .= " selected='selected'";
					}
				}
				$rtn .= ">".$row['avatar_name']."</option>";
			}
			
			$rtn .= "</select>";
		}
		
		return $rtn;
	}
}

?>