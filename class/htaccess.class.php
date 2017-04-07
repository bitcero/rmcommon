<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * This class allow to write htaccess file when it's possible
 */
class RMHtaccess
{
    private $rules = '';
    private $module = '';
    private $file = '';
    private $content = '';
    private $bCode = '';
    private $base = '/';
    private $apache = true;
    private $rewrite = true;

    function __construct($module){

        if(trim($module)=='')
            return false;

        $this->module = $module;
        $this->file = XOOPS_ROOT_PATH.'/.htaccess';

        $this->content = file_get_contents($this->file);

        $this->checkHealth();

        return null;
    }

	/**
	 * Verify if file is writable
	 * @return bool
	 */
	public function canWrite(){
		return is_writable($this->file);
	}

	/**
	 * Verify if mod_rewrite enabled
	 * @return bool
	 */
	public function isCapable(){
		if(function_exists('apache_get_modules')){
			$mods = apache_get_modules();
			if(!in_array('mod_rewrite', $mods))
				return false;
		}

		return true;
	}

	/**
     * Verify basic rules for rmcommon
     */
    private function checkHealth(){

        if( strpos($_SERVER['SERVER_SOFTWARE'], 'Apache')===FALSE && strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed')===FALSE )
            $this->apache = false;

        if(function_exists('apache_get_modules')){
            $mods = apache_get_modules();
            if(!in_array('mod_rewrite', $mods))
                $this->rewrite = false;
        }

        if(!$this->apache || !$this->rewrite){
            showMessage(__('URL rewriting requires an Apache Server and mod_rewrite enabled!','dtransport'), RMMSG_WARN);
            return false;
        }

        if(!preg_match("/RewriteEngine\s{1,}On/", $this->content))
            $this->content .= "\nRewriteEngine On\n";

        $base = parse_url(XOOPS_URL.'/');
        $this->base = isset($base['path']) ? rtrim($base['path'], '/').'/' : '/';
        $rb = "RewriteBase ".$this->base."\n";

        if(strpos($this->content, $rb)===false){
            if(preg_match("/RewriteBase/", $this->content))
                preg_replace("/RewriteBase\s{1,}(.*)\n/",$rb, $this->content);
            else
                $this->content .= $rb."\n";
        }

        if(!preg_match("/RewriteCond\s{1,}\%\{REQUEST_URI\}\s{1,}\!\/\[A\-Z\]\+\-/", $this->content))
            $this->content .= "RewriteCond %{REQUEST_URI} !/[A-Z]+-\n";

        if(!preg_match("/RewriteCond\s{1,}\%\{REQUEST_FILENAME\}\s{1,}\!\-f/", $this->content))
            $this->content .= "RewriteCond %{REQUEST_FILENAME} !-f\n";

        if(!preg_match("/RewriteCond\s{1,}\%\{REQUEST_FILENAME\}\s{1,}\!\-d/", $this->content))
            $this->content .= "RewriteCond %{REQUEST_FILENAME} !-d\n";



        return null;
    }

    public function verifyCode($code = ''){

        if($code!=''){
            if(strpos($this->content, $code)!==FALSE)
                return true;
            else
                return false;

        }

        if( preg_match_all("/# begin rmcommon\n(.*)\n# end rmcommon$/is", $this->content, $match) === false )
            print_r($match);
        else
            print_r($match);

        return null;
    }

	public function removeRule(){
		/*$count = 0;
		$replace = str_replace( "# begin $this->module\n$rule\n# end $this->module\n", '', $this->content, $count );*/

        $this->content = preg_replace("/\# begin " . $this->module . ".*\# end " . $this->module . "\n?/sm", '', $this->content );
        return true;

        /*$initial = strpos( $this->content, "# begin " . $this->module );
        if (false === $initial )
            return true;

        $final = strpos( $this->content, "# end " . $this->module . "\n" );

        $replace = substr( $this->content, 0, $initial );
        if ( false !== $final )
            $replace .= substr( $this->content, $final + strlen( "# end $this->module") + 1 );

		$this->content = $replace;
		return true;*/

	}

    private function makeCode(){

        $code = "# begin $this->module\n$this->rules\n# end $this->module\n";
        return $code;

    }

    public function write($rules = ''){

        if($rules==''){
            return file_put_contents($this->file, $this->content);
        }

        if(!$this->apache || !$this->rewrite)
            return null;

        $this->rules = $rules;
        $code = $this->makeCode($rules);

        $this->removeRule();

        // Verificamos si existe el código generado
        $oldExists = false;

        if(!is_writable($this->file))
            return $this->bCode.$code;

        $this->content .= "$code";

        if(file_put_contents($this->file, $this->content))
            return true;
        else
            return $code;

    }

}
