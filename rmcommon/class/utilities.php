<?php
// $Id: utilities.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------
 
class RMUtilities
{
	/**
	 * Obtiene una ?nica instancia de esta clase
	 */
	function get(){
		static $instance;
		if (!isset($instance)) {
			$instance = new RMUtilities();
		}
		return $instance;
	}

	/**
	 * Elimina un archivo existente del servidor
	 * @param string $filepath Ruta completa al archivo
	 * @return bool
	 */
	public function delete_file($filepath){
		if ($filepath == '') return false;
		
		if (!file_exists($filepath)) return true;
		
		return unlink($filepath);
		
	}
	/**
	 * Comprueba si existe un elemento en una tabla expec?fica
	 * @param string $table Nombre de la tabla
	 * @param string $cond Condici?n de b?squeda
	 * @return bool
	 */
	public function get_count($table, $cond=''){
		$db =& EXMDatabase::get();
		$sql = "SELECT COUNT(*) FROM $table";
		if ($cond!='') $sql .= " WHERE $cond";
		list($num) = $db->fetchRow($db->query($sql));
		return $num;
    }

    /**
    * Determina el color rgb a partir de una cadena HEX
    */
    private function hexToRGB($color){
        // Transformamos el color hex a rgb
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                                     $color[2].$color[3],
                                     $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            list($r,$g,$b) = array("FF", "FF", "FF");

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
        return array('r'=>$r,'g'=>$g,'b'=>$b);
    }
	
	/**
	 * Genera una cadena aleatoria en base a par?metros especificados
	 */
	public function randomString($size, $digit = true, $special = false, $upper = false, $alpha = true){
		$aM = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$am = "abcdefghijklmnopqrstuvwxyz";
		$d = "0123456789";
		$s = "?@#\$%&()=???!.:,;-_+*[]{}";
		
		$que = array();
		if ($alpha) $que[] = 'alpha';
		if ($digit) $que[] = 'digit';
		if ($special) $que[] = 'special';
		
		$rtn = '';
		
		for ($i=1;$i<=$size;$i++){
			$op = $que[rand(0, count($que) - 1)];
			switch($op){
				case 'alpha':
					$what = $upper ? $aM : (rand(0, 1)==0 ? $aM : $am);
					$rtn .= substr($what, rand(0, strlen($what)-1), 1);
					break;
				case 'digit':
					$rtn .= substr($d, rand(0, strlen($d)-1), 1);
					break;
				case 'special':
					$rtn .= substr($s, rand(0, strlen($s)-1), 1);
					break;
			}
		}
		
		return $rtn;
		
	}
	
	/**
	* Add a slash (/) to the end of string
	*/
	public function add_slash($string){
		$string = rtrim($string, "/");
		return $string.'/';
	}
	
	/**
	 * Format bytes to MB, GB, KB, etc
	 * @param int $size Tamaño de bytes
	 * @return string
	 */
	public function formatBytesSize($size){

		return RMFormat::bytes_format( $size, 'bytes' );
		
	}
	
	/**
	 * Elimina directorios y todos los archivos contenidos
	 * @param string $path Ruta del directorio
	 * @return bool
	 */
	function delete_directory($path){
		$path = str_replace('\\', '/', $path);
		if (substr($path, 0, strlen($path) - 1)!='/'){
			$path .= '/';
		}
		$dir = opendir($path);
		while (($file = readdir($dir)) !== false){
			if ($file == '.' || $file=='..') continue;
			if (is_dir($path . $file)){
				self::delete_directory($path . $file);
			} else {
				@unlink($path . $file);
			}
		}
		closedir($dir);
		@rmdir($path);
	}

    /**
     * Muestra los controles para lanzar el administrador de imágenes
     * desde cualqueir punto
     * @param string $name Element name for inputs
     * @param string $id ID for this element
     * @param string $default Default value for field
     * @param array $data Array of data that will be inserted as data-{key} in HTML code
     * @return string
     */
    public function image_manager($name, $id='', $default='', $data = array()){
        
        $id = $id=='' ? $name : $id;
        
        if($default!=''){
            $img = new RMImage();
            $img->load_from_params($default);
        }

        $ret = '<div id="'.$id.'-container" class="rmimage_container"';
        foreach( $data as $key => $value ){
            $ret .= ' data-' . $key . '="' . $value . '"';
        }
        $ret .= '>';
        $ret .= '<div class="thumbnail">';
        if($default!='' && !$img->isNew()){
            $ret .= '<a href="'.$img->url().'" target="_blank"><img src="'.$img->get_smallest().'" /></a>';
            $ret .= '<input type="hidden" name="'.$name.'" id="'.$id.'" value="'.$default.'" />';
            $ret .= '<br /><a href="#" class="removeButton removeButton-'.$id.'">'.__('Remove Image','rmcommon').'</a>';
        } else {
            $ret .= '<input type="hidden" name="'.$name.'" id="'.$id.'" value="" />';
        }
        $ret .= '</div>';
        $ret .= '<span class="image_manager_launcher btn btn-success">'.__('Image manager...','rmcommon').'</span>';
        $ret .= '</div>';

        $tpl = RMTemplate::get();

        $tpl->add_head_script('var imgmgr_title = "'.__('Image Manager','rmcommon').'"'."\n".'var mgrURL = "'.RMCURL.'/include/tiny-images.php";');
        $tpl->add_script( 'image_mgr_launcher.js', 'rmcommon', array('directory' => 'include') );

        return $ret;
    }

    /* DEPRECATED METHODS
    ========================================= */

    /**
     * Get the version for a module
     * Use RMModule::get_module_version() instead.
     *
     * @deprecated
     * @param bool $includename
     * @param string $module
     * @param int $type
     * @return array|string
     */
    public function getVersion($includename = true, $module='', $type=0){

        trigger_error( sprintf( __('Method %s is deprecated. Use %s::%s instead.', 'rmcommon' ), __METHOD__, 'RMModules', 'get_module_version' ));
        return RMModules::get_module_version($module, $includename, $type == 0 ? 'verbose' : 'raw');

    }

    /**
     * Format a module version.
     * Use RMModules::format_module_version() instead
     * @deprecated
     * @param $version
     * @param bool $name
     * @return string
     */
    public function format_version($version, $name = false){

        trigger_error( sprintf( __('Method %s is deprecated. Use %s::%s instead.', 'rmcommon' ), __METHOD__, 'RMModules', 'format_module_version' ), E_USER_DEPRECATED );
        return RMModules::format_module_version($version, $name);

    }

    /**
     * @deprecated
     * Retrieves the configuration for a given module.
     *
     * This function is deprecated, use RMSettings::settings->module_settings() instead
     *
     * @param string $directory Nombre del M?dulo
     * @param string $option Nombre de la opci?n de configuraci?n
     * @return string o array
     */
    public function module_config($directory, $option=''){

        trigger_error( sprintf( __('Method %s is deprecated. Use %s::%s instead.', 'rmcommon' ), __METHOD__, 'RMSettings', 'module_settings()' ), E_USER_DEPRECATED );
        $settings = RMSettings::module_settings($directory, $option);

        if( is_object( $settings ) )
            return (array) $settings;
        else
            return $settings;

    }
		
}
