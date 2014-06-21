<?php
// $Id: object.php 877 2011-12-25 02:42:16Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


/**
 * Basado en el archivo kernel/object.php de XOOPS
 * creado por Kazumi Ono (AKA onokazu)
 */


/**
 * Base class for all objects in the Xoops kernel (and beyond) 
 **/
class RMObject
{

    /**
     * holds all variables(properties) of an object
     * 
     * @var array
     * @access protected
     **/
    protected $vars = array();

    /**
    * variables cleaned for store in DB
    * 
    * @var array
    * @access protected
    */
    public $cleanVars = array();

    /**
    * is it a newly created object?
    * 
    * @var bool
    * @access private
    */
    private $_isNew = false;

    /**
    * has any of the values been modified?
    * 
    * @var bool
    * @access private
    */
    private $_isDirty = false;

    /**
    * errors
    * 
    * @var array
    * @access private
    */
    private $_errors = array();

    /**
    * additional filters registered dynamically by a child class object
    * 
    * @access private
    */
    private $_filters = array();
	/**
	 * Almacena el nombre de la clave primaria en la base de datos (columna)
	 * esta por defecto es 'id'
	 */
	protected $primary = 'id';
	protected $db = null;
	protected $_log = array();
	protected $_dbtable = '';
	private $_tblcolumns = array();
	private $_uniquefield = '';
	
	/**
	 * Almacena las columnas (variables) de un objeto
	 */
	private $objectColumns = array();
	private $primaryCols = array();
    
    public function id(){
        return $this->getVar($this->primary);
    }

    public function __set( $name, $value ){

        // Verificamos columnas
        if ( isset( $this->_tblcolumns[$name] ) )
            return $this->setVar($name, $value);

    }

    public function __get( $name ){
        // Verificamos columnas
        if ( isset( $this->_tblcolumns[$name] ) )
            return $this->getVar( $name );
    }

    /**#@+
    * used for new/clone objects
    * 
    * @access public
    */
    function setNew()
    {
        $this->_isNew = true;
    }
    function unsetNew()
    {
        $this->_isNew = false;
    }
    function isNew()
    {
        return $this->_isNew;
    }
    /**#@-*/

    /**#@+
    * mark modified objects as dirty
    * 
    * used for modified objects only
    * @access public
    */
    function setDirty()
    {
        $this->_isDirty = true;
    }
    function unsetDirty()
    {
        $this->_isDirty = false;
    }
    function isDirty()
    {
        return $this->_isDirty;
    }
    /**#@-*/

    /**
    * initialize variables for the object
    * 
    * @access public
    * @param string $key
    * @param int $data_type  set to one of XOBJ_DTYPE_XXX constants (set to XOBJ_DTYPE_OTHER if no data type ckecking nor text sanitizing is required)
    * @param mixed
    * @param bool $required  require html form input?
    * @param int $maxlength  for XOBJ_DTYPE_TXTBOX type only
    * @param string $option  does this data have any select options?
    */
    function initVar($key, $data_type, $value = null, $required = false, $maxlength = null, $options = '')
    {
    	if (isset($this->vars[$key])) return;
        $this->vars[$key] = array('value' => $value, 'required' => $required, 'data_type' => $data_type, 'maxlength' => $maxlength, 'changed' => false, 'options' => $options);
    }
	/**
	 * Establece el tipo de dato para una variable
	 * @param string $var Nombre de la variable
	 * @param int $type Tipo de Dato
	 */
	 function setVarType($var, $type){
	 	if (!isset($this->vars[$var])) return false;
		return $this->vars[$var]['data_type'] = $type;
	 }
	/**
	 * Establece la longitud de un campo
	 * @param string $var Nombre de la variable
	 * @param int $len Longitud del campo o null
	 */
	function setVarLen($var, $len=null){
		if (!isset($this->vars[$var])) return false;
		return $this->vars[$var]['maxlength'] = $len;
	}
	/**
	 * Establece la obligatoriedad de una variable
	 * @param string $var Nombre de la variable
	 * @param bool $required
	 */
	function setVarRequired($var, $required){
		if (!isset($this->vars[$var])) return false;
		return $this->vars[$var]['required'] = $required;
	}
    /**
    * assign a value to a variable
    * 
    * @access public
    * @param string $key name of the variable to assign
    * @param mixed $value value to assign
    */
    function assignVar($key, $value)
    {
        if (isset($value) && isset($this->vars[$key])) {
            $this->vars[$key]['value'] =& $value;
        }
    }

    /**
    * assign values to multiple variables in a batch
    * 
    * @access private
    * @param array $var_array associative array of values to assign
    */
    function assignVars($var_arr)
    {
    	if (empty($var_arr)) return;
        foreach ($var_arr as $key => $value) {
            $this->assignVar($key, stripslashes($value));
        }
        $this->unsetNew();
    }

    /**
    * assign a value to a variable
    * 
    * @access public
    * @param string $key name of the variable to assign
    * @param mixed $value value to assign
    * @param bool $not_gpc
    */
    function setVar($key, $value, $not_gpc = false)
    {
        if (!empty($key) && isset($value) && isset($this->vars[$key])) {
            $this->vars[$key]['value'] =& $value;
            $this->vars[$key]['not_gpc'] = $not_gpc;
            $this->vars[$key]['changed'] = true;
            $this->setDirty();
        }
    }

    /**
    * assign values to multiple variables in a batch
    * 
    * @access private
    * @param array $var_arr associative array of values to assign
    * @param bool $not_gpc
    */
    function setVars($var_arr, $not_gpc = false)
    {
        foreach ($var_arr as $key => $value) {
            $this->setVar($key, $value, $not_gpc);
        }
    }

	/**
	* Assign values to multiple variables in a batch
	*
	* Meant for a CGI contenxt:
	* - prefixed CGI args are considered save
	* - avoids polluting of namespace with CGI args
	*
	* @access private
	* @param array $var_arr associative array of values to assign
	* @param string $pref prefix (only keys starting with the prefix will be set)
	*/
	function setFormVars($var_arr=null, $pref='xo_', $not_gpc=false) {
		$len = strlen($pref);
		foreach ($var_arr as $key => $value) {
			if ($pref == substr($key,0,$len)) {
				$this->setVar(substr($key,$len), $value, $not_gpc);
			}
		}
	}


    /**
    * returns all variables for the object
    * 
    * @param bool Return formated vars?
    * @param string Format type (s,e,p,f)
    * @return array associative array of key->value pairs
    */
    public function getVars($formated = false, $format = 's')
    {
        if (!$formated){
			return $this->vars;
        }
        
        $ret = array();
        foreach ($this->vars as $key => $var){
			$ret[$key] = $this->getVar($key, $format);
        }
        
        return $ret;
        
    }
	/**
	* Returns the values of the specified variables
	*
	* @param mixed $keys An array containing the names of the keys to retrieve, or null to get all of them
	* @param string $format Format to use (see getVar)
	* @param int $maxDepth Maximum level of recursion to use if some vars are objects themselves
	* @return array associative array of key->value pairs
	*/
	function getValues( $keys = null, $format = 's', $maxDepth = 1 ) {
    	if ( !isset( $keys ) ) {
    		$keys = array_keys( $this->vars );
    	}
    	$vars = array();
    	foreach ( $keys as $key ) {
    		if ( isset( $this->vars[$key] ) ) {
    			if ( is_object( $this->vars[$key] ) && is_a( $this->vars[$key], 'RMObject' ) ) {
					if ( $maxDepth ) {
    					$vars[$key] = $this->vars[$key]->getValues( null, $format, $maxDepth - 1 );
					}
    			} else {
    				$vars[$key] = $this->getVar( $key, $format );
    			}
    		}
    	}
    	return $vars;
    }
    /**
    * returns a specific variable for the object in a proper format
    * 
    * @access public
    * @param string $key key of the object's variable to be returned
    * @param string $format format to use for the output
    * @return mixed formatted value of the variable
    */
    function getVar($key, $format = 's')
    {
        $ret = $this->vars[$key]['value'];

        switch ($this->vars[$key]['data_type']) {

        case XOBJ_DTYPE_TXTBOX:
            switch (strtolower($format)) {
            case 's':
            case 'show':
            case 'e':
            case 'edit':
                $ts = TextCleaner::getInstance();
                return $ts->specialchars($ret);
                break;
            case 'p':
            case 'preview':
            case 'f':
            case 'formpreview':
                $ts =& TextCleaner::getInstance();
                return $ts->specialchars($ts->stripSlashesGPC($ret));
                break 1;
            case 'n':
            case 'none':
            default:
                break 1;
            }
            break;
        case XOBJ_DTYPE_TXTAREA:
            switch (strtolower($format)) {
            case 's':
            case 'show':
                return TextCleaner::getInstance()->to_display($ret);
                break 1;
            case 'e':
            case 'edit':
            	$ts = TextCleaner::getInstance();
            	return $ts->specialchars($ts->stripslashes($ret));
                break;
            case 'p':
            case 'preview':
                $ts =& TextCleaner::getInstance();
                $html = !empty($this->vars['dohtml']['value']) ? 1 : 0;
                $xcode = (!isset($this->vars['doxcode']['value']) || $this->vars['doxcode']['value'] == 1) ? 1 : 0;
                $smiley = (!isset($this->vars['dosmiley']['value']) || $this->vars['dosmiley']['value'] == 1) ? 1 : 0;
                $image = (!isset($this->vars['doimage']['value']) || $this->vars['doimage']['value'] == 1) ? 1 : 0;
                $br = (!isset($this->vars['dobr']['value']) || $this->vars['dobr']['value'] == 1) ? 1 : 0;
                return $ts->previewTarea($ret, $html, $smiley, $xcode, $image, $br);
                break 1;
            case 'f':
            case 'formpreview':
                $ts =& TextCleaner::getInstance();
                return htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                break 1;
            case 'n':
            case 'none':
            default:
            	return $ret;
                break 1;
            }
            break;
        case XOBJ_DTYPE_ARRAY:
        	if (!is_array($ret) && trim($ret)!=''){
            	$ret =& unserialize($ret);
            } else {
				$ret = $ret;
			}
            break;
        case XOBJ_DTYPE_SOURCE:
            switch (strtolower($format)) {
            case 's':
            case 'show':
                break 1;
            case 'e':
            case 'edit':
                return htmlspecialchars($ret, ENT_QUOTES);
                break 1;
            case 'p':
            case 'preview':
                $ts =& TextCleaner::getInstance();
                return $ts->stripSlashesGPC($ret);
                break 1;
            case 'f':
            case 'formpreview':
                $ts =& TextCleaner::getInstance();
                return htmlspecialchars($ts->stripSlashesGPC($ret), ENT_QUOTES);
                break 1;
            case 'n':
            case 'none':
            default:
                break 1;
            }
            break;
        default:
            if ($this->vars[$key]['options'] != '' && $ret != '') {
                switch (strtolower($format)) {
                case 's':
                case 'show':
					$selected = explode('|', $ret);
                    $options = explode('|', $this->vars[$key]['options']);
                    $i = 1;
                    $ret = array();
                    foreach ($options as $op) {
                        if (in_array($i, $selected)) {
                            $ret[] = $op;
                        }
                        $i++;
                    }
                    return implode(', ', $ret);
                case 'e':
                case 'edit':
                    $ret = explode('|', $ret);
                    break 1;
                default:
                    break 1;
                }

            }
            break;
        }
        return $ret;
    }

    /**
     * clean values of all variables of the object for storage. 
     * also add slashes whereever needed
     * 
     * @return bool true if successful
     * @access public
     */
    public function cleanVars()
    {
        $ts =& TextCleaner::getInstance();
        $existing_errors = $this->getErrors();
        $this->_errors = array();
        foreach ($this->vars as $k => $v) {
			$cleanv = $v['value'];
            if (!$v['changed']) {
            } else {
                $cleanv = is_string($cleanv) ? trim($cleanv) : $cleanv;
                switch ($v['data_type']) {
                case XOBJ_DTYPE_TXTBOX:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $this->setErrors( sprintf( _XOBJ_ERR_REQUIRED, $k ) );
                        continue;
                    }
                    if (isset($v['maxlength']) && strlen($cleanv) > intval($v['maxlength'])) {
                        $this->setErrors( sprintf( _XOBJ_ERR_SHORTERTHAN, $k, intval( $v['maxlength'] ) ) );
                        continue;
                    }
                    $cleanv = TextCleaner::stripslashes($cleanv);
                    break;
                case XOBJ_DTYPE_TXTAREA:
                    if ($v['required'] && $cleanv != '0' && $cleanv == '') {
                        $this->setErrors( sprintf( _XOBJ_ERR_REQUIRED, $k ) );
                        continue;
                    }
                    
                    $cleanv = TextCleaner::stripslashes($cleanv);
                    break;
                case XOBJ_DTYPE_SOURCE:
                    $cleanv = TextCleaner::stripslashes($cleanv);
                    break;
                case XOBJ_DTYPE_INT:
                    $cleanv = intval($cleanv);
                    break;
                case XOBJ_DTYPE_EMAIL:
                    if ($v['required'] && $cleanv == '') {
                        $this->setErrors( sprintf( _XOBJ_ERR_REQUIRED, $k ) );
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i",$cleanv)) {
                        $this->setErrors("Invalid Email");
                        continue;
                    }
                    $cleanv = TextCleaner::stripslashes($cleanv);
                    break;
                case XOBJ_DTYPE_URL:
                    if ($v['required'] && $cleanv == '') {
                        $this->setErrors( sprintf( _XOBJ_ERR_REQUIRED, $k ) );
                        continue;
                    }
                    if ($cleanv != '' && !preg_match("/^http[s]*:\/\//i", $cleanv)) {
                        $cleanv = 'http://' . $cleanv;
                    }
                    $cleanv = TextCleaner::stripslashes($cleanv);
                    break;
                case XOBJ_DTYPE_ARRAY:
                    $cleanv = !empty($cleanv) && is_array($cleanv) ? serialize($cleanv) : $cleanv;
                    break;
                case XOBJ_DTYPE_STIME:
                case XOBJ_DTYPE_MTIME:
                case XOBJ_DTYPE_LTIME:
                    $cleanv = !is_string($cleanv) ? intval($cleanv) : strtotime($cleanv);
                    break;
                default:
                    break;
                }
            }
            $this->cleanVars[$k] =& $cleanv;
            unset($cleanv);
        }
        
        if (count($this->_errors) > 0) {
	        $this->_errors = array_merge($existing_errors, $this->_errors);
            return false;
        }
	    $this->_errors = array_merge($existing_errors, $this->_errors);
        $this->unsetDirty();
        return true;
    }
    
    /**
    * Clear all vars to start a new object
    */
    protected function clear_vars(){
		foreach ($this->vars as $var){
			$var['value'] = '';
		}
		$this->_isNew = true;
		$this->_errors = array();
		$this->_filters = array();
    }

    /**
     * dynamically register additional filter for the object
     * 
     * @param string $filtername name of the filter
     * @access public
     */
    function registerFilter($filtername)
    {
        $this->_filters[] = $filtername;
    }

    /**
     * load all additional filters that have been registered to the object
     * 
     * @access private
     */
    function _loadFilters()
    {
        //include_once ABSPATH.'/class/filters/filter.php';
        //foreach ($this->_filters as $f) {
        //    include_once ABSPATH.'/class/filters/'.strtolower($f).'php';
        //}
    }

    /**
     * create a clone(copy) of the current object
     * 
     * @access public
     * @return object clone
     */
    function rmClone()
    {
        $class = get_class($this);
        $clone = new $class();
        foreach ($this->vars as $k => $v) {
            $clone->assignVar($k, $v['value']);
        }
        // need this to notify the handler class that this is a newly created object
        $clone->setNew();
        return $clone;
    }

    /**
     * add an error 
     * 
     * @param string $value error to add
     * @access public
     */
    function setErrors($err_str)
    {
        $this->_errors[] = trim($err_str);
    }

    /**
     * return the errors for this object as an array
     * 
     * @return array an array of errors
     * @access public
     */
    function getErrors()
    {
        return $this->errors(false);
    }

    /**
     * return the errors for this object as html
     * 
     * @return string html listing the errors
     * @access public
     */
    function getHtmlErrors()
    {
        return $this->errores(true);
    }
	/**
	 * Agrega una entrada a nuestro array de errores.
	 * @param string $text Descripcin del error
	 */
	protected function addError($text){
		$this->setErrors($text);
	}
	/**
	 * Obtenemos los errores almacenados.
	 * Estos pueden obtenerse de dos maneras dependiendo del parmetro $html.
	 * false Nos devuelve un array y true nos devuelve una cadena HTML frmateada.
	 * @param bol $html Devolver cadena HTML o array
	 * @return array
	 * @return string
	 */
	public function errors($html=true){
		$ret = '';
		
		if (count($this->_errors)<=0){ return $html ? '' : array(); }
		
		if ($html){

			foreach ($this->_errors as $k){
				$ret .= "$k<br>";
			}
		
		} else {
		
			return $this->_errors;
		
		}
		
		return $ret;
	}
	/**
	 * Comrpobamos si ha sido inicializada una variable.
	 * @param string $var Nombre de la variable
	 */
	protected function varIsset($var){
		if (isset($this->vars[$var])){
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Funcin para agregar una entrada al log de eventos
	 * @param string $event Texto del evento
	 * @param string $style Estilo css del evento
	 */
	protected function logger($event,$style=''){
		$rtn = array();
		$rtn['event'] = $event;
		$rtn['style'] = $style;
		$this->_log[] = $rtn;
	}
	/**
	 * Limpiamos el log de eventos
	 */
	protected function clearLogger(){
		$this->_log = array();
	}
	/**
	 * Recuperamos el log de elementos.
	 * Si es pasado como parmetro "false" devuelve un array.
	 * Si se pasa el valor "true" devuelve HTML
	 * @param bol $ashtml True o False
	 * @return array
	 * @return string
	 */
	public function getLogger($ashtml = true){
		if (!$ashtml){
			return $this->_log;
		}
		
		$rtn = '';
		foreach ($this->_log as $k){		
			$rtn .= "<div style='padding: 2px;";
			if ($k['style']!=''){
				if (stripos($k['style'],'text-align:')==''){
					$rtn .= ' text-align: left;';	
				}
				$rtn .= ' ' . $k['style'];
			}
			$rtn .= "'>".(trim($k['event'])=='' ? '&nbsp;' : $k['event'])."</div>\n";
		}
		
		return $rtn;
	}
	/**
	 * Función para obtener todas las columnas de una tabla de la base de datos
	 * la variable {@link $_dbtable} debe ser inicializada
	 * Devuelve un array con los nombres y datos de las columnas
	 * @return array
	 */
	protected function getColumns(){
		
		static $objectColumns;
		static $primaryCols;
		if (!empty($objectColumns[get_class($this)])){
			$this->primary = $primaryCols[get_class($this)];
			$this->_tblcolumns = $objectColumns[get_class($this)];
			return $objectColumns[get_class($this)];
		} else {
			if (empty($this->_tblcolumns)){
				$result = $this->db->queryF("SHOW COLUMNS IN ".$this->_dbtable);
				while ($row = $this->db->fetchArray($result)){
					if ($row['Extra'] == 'auto_increment'){
						$this->primary = $row['Field'];
						$primaryCols[get_class($this)] = $row['Field'];
					}
					$this->_tblcolumns[$row['Field']] = $row;
				}
			}
			$objectColumns[get_class($this)] = $this->_tblcolumns;
			return $objectColumns[get_class($this)];
		}
	}
	/**
	 * Funcin para inicializar las variables
	 * a partir de las columnas de una tabla
	 */
	protected function initVarsFromTable(){
        
		foreach ($this->getColumns() as $k => $v){
			$efes = array();
			preg_match("/(.+)(\(([,0-9]+)\))/", $v['Type'], $efes);
			if (!isset($efes[1])){
				$efes[1] = $v['Type'];
			}

			switch ($efes[1]){
				case 'mediumint':
				case 'int':
				case 'tinyint':
				case 'smallint':
				case 'bigint':
				case 'timestamp':
				case 'year':
				case 'bool':
					$type = XOBJ_DTYPE_INT;
					$lon = null;
					break;
				case 'float':
				case 'double':
					$type = XOBJ_DTYPE_FLOAT;
					break;
				case 'decimal':
					$type = XOBJ_DTYPE_TXTBOX;
					$lon = null;
					break;
				case 'time':
					$type = XOBJ_DTYPE_TXTBOX;
					$lon = 8;
					break;
				case 'datetime':
					$type = XOBJ_DTYPE_TXTBOX;
					$lon = 19;
					break;
				case 'date':
					$type = XOBJ_DTYPE_TXTBOX;
					$lon = 10;
					break;
				case 'char':
				case 'tinyblob':
				case 'tinytext':
				case 'enum':
				case 'set':
                case 'varchar':
					$type = XOBJ_DTYPE_TXTBOX;
					$lon = isset($len[3]) ? $len[3] : null;
					break;
				case 'text':
				case 'blob':
				case 'mediumblob':
				case 'mediumtext':
				case 'longblob':
				case 'longtext':
					$type = XOBJ_DTYPE_TXTAREA;
					$lon = null;
					break;
				default:
					$type = XOBJ_DTYPE_OTHER;
					$lon = null;
					break;
								
				
			}
			
			$this->initVar($v['Field'], $type, $v['Default'], false, $lon);
		}
	}
	
	/**
	 * Cargaa los valores de un objeto desde la base de datos
	 * en base a su clave primaria
	 * @param mixed $id Valor a buscar en la clave primaria
	 * @return bool
	 */
	protected function loadValues($id){
		
		if (get_magic_quotes_gpc())
        	$id = stripslashes($id);
			
		$id = mysql_real_escape_string($id);
		
		$sql = "SELECT * FROM $this->_dbtable WHERE `$this->primary`='$id'";
		$result = $this->db->query($sql);
		if ($this->db->getRowsNum($result)<=0) return false;
		
		$row = $this->db->fetchArray($result);
		foreach ($row as $k => $v){
			$this->setVar($k, $v);
		}
		
		return true;
	}
	
	/**
	* Load the object values from database with a filtered query
	*/
	protected function loadValuesFiltered($filter=''){
		
		if (get_magic_quotes_gpc())
        	$filter = stripslashes($filter);
		
		$sql = "SELECT * FROM $this->_dbtable WHERE $filter";
		$result = $this->db->query($sql);
		if ($this->db->getRowsNum($result)<=0) return false;
		
		$row = $this->db->fetchArray($result);
		$this->assignVars($row);
		
		return true;
		
	}
	
	/**
	 * Carga valores de la base de datos
	 * @param array $values Array de valores
	 */
	protected function loadValuesArray($values){
		if (!is_array($values) || empty($values)){
			return false;
		}
		/**
		 * limpiamos los valores
		 */
		$query = '';
		foreach ($values as $k => $v){
			if (get_magic_quotes_gpc())
        		$v = stripslashes($v);
			$values[$k] = mysql_real_escape_string($v);
			$query .= $query=='' ? "`$k`='$v'" : " AND `$k`='$v'";
		}
		
		$sql = "SELECT * FROM $this->_dbtable WHERE $query";
		$result = $this->db->queryF($sql);
		if ($this->db->getRowsNum($result)<=0) return false;
		
		$row = $this->db->fetchArray($result);
		$myts =& TextCleaner::getInstance();
		foreach ($row as $k => $v){
			$this->setVar($k, $myts->stripslashes($v));
		}
		
		return true;
		
	}
	/**
	 * Almacena los valores como un registro nuevo 
	 * en una tabla
	 */
	protected function saveToTable(){
		$myts =& TextCleaner::getInstance();
		$this->cleanVars();
		$sql = "INSERT INTO $this->_dbtable (";
		$fields = '';
		$values = '';
		foreach ($this->_tblcolumns as $k){
			if ($k['Extra'] == 'auto_increment') continue;
			$fields .= ($fields == '') ? "`$k[Field]`" : ", `$k[Field]`";
			$values .= ($values=='') ? "'".addslashes($this->cleanVars[$k['Field']])."'" : ", '".addslashes($this->cleanVars[$k['Field']])."'";
		}
		
		$sql .= $fields .") VALUES (". $values .")";
		
		if (!$this->db->queryF($sql)){
			$this->addError($this->db->error());
			return false;
		} else {
			$this->setVar($this->primary, $this->db->getInsertId());
			$this->unsetNew();
			return true;
		}
		
	}
	/**
	 * Almacena las modificaciones hechas a un registro de una tabla
	 */
	protected function updateTable(){
		if (empty($this->_tblcolumns)) $this->getColumns();
		
		$myts =& TextCleaner::getInstance();
		$sql = "UPDATE $this->_dbtable SET ";
		$fields = '';
		
		$this->cleanVars();
		
		foreach ($this->_tblcolumns as $k){
			if ($k['Extra'] == 'auto_increment') continue;
			$fields .= $fields == '' ? "`$k[Field]`='".addslashes($this->cleanVars[$k['Field']])."'" : ", `$k[Field]`='".addslashes($this->cleanVars[$k['Field']])."'";
		}
		
		$sql .= $fields . " WHERE `$this->primary`='".$this->getVar($this->primary)."'";
        
		$this->db->queryF($sql);
		if ($this->db->error()!=''){
			$this->addError($this->db->error());
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Elimina un registro de la base de datos
	 */
	protected function deleteFromTable(){
		
		$sql = "DELETE FROM $this->_dbtable WHERE `$this->primary`='".$this->getVar($this->primary)."'";
		$this->db->queryF($sql);
		if ($this->db->error()!=''){
			$this->addError($this->db->error());
			return false;
		} else {
			return true;
		}
		
	}
}
