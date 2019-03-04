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
     * Gets a singleton
     */
    public static function get()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Deletes an existing file
     * @param string $filepath Ruta completa al archivo
     * @return bool
     */
    public function delete_file($filepath)
    {
        if ('' == $filepath) {
            return false;
        }

        if (!file_exists($filepath)) {
            return true;
        }

        return unlink($filepath);
    }

    /**
     * Determina el color rgb a partir de una cadena HEX
     * @param mixed $color
     */
    private function hexToRGB($color)
    {
        // Transformamos el color hex a rgb
        if ('#' == $color[0]) {
            $color = mb_substr($color, 1);
        }

        if (6 == mb_strlen($color)) {
            list($r, $g, $b) = [$color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5], ];
        } elseif (3 == mb_strlen($color)) {
            list($r, $g, $b) = [$color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]];
        } else {
            list($r, $g, $b) = ['FF', 'FF', 'FF'];
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }

    /**
     * Generates a random string with a given length. Note that, by default, this
     * method could generate strings that can cause conflicts with HTML. In order to
     * prevent these issues, and if you need to show the result in a form field
     * or directly in the page, then you can use special chars to show correctly.
     *
     * @param $options_or_size
     * @param bool $useDigits
     * @param bool $useSpecial
     * @param bool $useUpper
     * @param bool $useAlpha
     * @param mixed $onlyUpper
     * @return string
     */
    public static function randomString($options_or_size, $useDigits = true, $useSpecial = true, $onlyUpper = false, $useAlpha = true)
    {
        $upperLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowerLetters = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $specialChars = '?@#$%&()=/\\~!.:,;-_+*[]{}';

        if (is_array($options_or_size)) {
            if (array_key_exists('upperLetters', $options_or_size)) {
                $upperLetters = $options_or_size['upperLetters'];
            }

            if (array_key_exists('lowerLetters', $options_or_size)) {
                $lowerLetters = $options_or_size['lowerLetters'];
            }

            if (array_key_exists('digits', $options_or_size)) {
                $digits = $options_or_size['digits'];
            }

            if (array_key_exists('specialChars', $options_or_size)) {
                $specialChars = $options_or_size['specialChars'];
            }

            $size = array_key_exists('size', $options_or_size) ? $options_or_size['size'] : 40;
            $useDigits = array_key_exists('digit', $options_or_size) ? $options_or_size['digit'] : true;
            $useSpecial = array_key_exists('special', $options_or_size) ? $options_or_size['special'] : true;
            $useAlpha = array_key_exists('alpha', $options_or_size) ? $options_or_size['alpha'] : true;
            $onlyUpper = array_key_exists('upper', $options_or_size) ? $options_or_size['upper'] : false;
        } else {
            $size = (int) $options_or_size;
        }

        $que = [];
        if ($useAlpha) {
            $que[] = 'alpha';
        }
        if ($useDigits) {
            $que[] = 'digit';
        }
        if ($useSpecial) {
            $que[] = 'special';
        }

        $rtn = '';

        for ($i = 1; $i <= $size; $i++) {
            $op = $que[random_int(0, count($que) - 1)];
            switch ($op) {
                case 'alpha':
                    $what = $onlyUpper ? $upperLetters : (0 == random_int(0, 1) ? $upperLetters : $lowerLetters);
                    $rtn .= mb_substr($what, random_int(0, mb_strlen($what) - 1), 1);
                    break;
                case 'digit':
                    $rtn .= mb_substr($digits, random_int(0, mb_strlen($digits) - 1), 1);
                    break;
                case 'special':
                    $rtn .= mb_substr($specialChars, random_int(0, mb_strlen($specialChars) - 1), 1);
                    break;
            }
        }

        return $rtn;
    }

    /**
     * Add a slash (/) to the end of string
     * @param mixed $string
     */
    public function add_slash($string)
    {
        $string = rtrim($string, '/');

        return $string . '/';
    }

    /**
     * Format bytes to MB, GB, KB, etc
     * @param int $size Tamaño de bytes
     * @return string
     */
    public function formatBytesSize($size)
    {
        return RMFormat::bytes_format($size, 'bytes');
    }

    /**
     * Elimina directorios y todos los archivos contenidos
     * @param string $path Ruta del directorio
     * @param bool $root Specify if the folder root must be deleted too
     * @param array Path of excluded files or folders
     * @param mixed $exclude
     * @return bool
     */
    public static function delete_directory($path, $root = true, $exclude = [])
    {
        $path = str_replace('\\', '/', $path);
        if ('/' != mb_substr($path, 0, mb_strlen($path) - 1)) {
            $path .= '/';
        }
        $dir = opendir($path);
        while (false !== ($file = readdir($dir))) {
            if ('.' == $file || '..' == $file) {
                continue;
            }

            if (in_array($path . $file, $exclude, true)) {
                continue;
            }

            if (is_dir($path . $file)) {
                self::delete_directory($path . $file);
            } else {
                @unlink($path . $file);
            }
        }
        closedir($dir);
        if ($root) {
            @rmdir($path);
        }
    }

    public static function copy_directory($source, $target)
    {
        $source = str_replace('\\', '/', $source);
        $target = str_replace('\\', '/', $target);

        $source = rtrim($source, '/');
        $target = rtrim($target, '/');

        if (!is_dir($source) || !is_dir($target)) {
            return false;
        }

        $dir = opendir($source);

        while (false !== ($file = readdir($dir))) {
            if ('.' == $file || '..' == $file) {
                continue;
            }

            if (is_dir($source . '/' . $file)) {
                self::copy_directory($source . '/' . $file, $target . '/' . $file);
            } else {
                copy($source . '/' . $file, $target . '/' . $file);
            }
        }
        closedir($dir);

        return true;
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
    public function image_manager($name, $id = '', $default = '', $data = [])
    {
        global $common;

        $common->template()->add_style('pop-images-manager.min.css', 'rmcommon', ['id' => 'images-manager-css']);

        $id = '' == $id ? $name : $id;

        if ('' != $default) {
            $img = new RMImage();
            $img->load_from_params($default);
        }

        $ret = '<div id="' . $id . '-container" class="rmimage_container"';
        foreach ($data as $key => $value) {
            $ret .= ' data-' . $key . '="' . $value . '"';
        }
        $ret .= '>';
        $ret .= '<div class="thumbnail">';
        if ('' != $default && !$img->isNew()) {
            $ret .= '<a href="' . $img->url() . '" target="_blank"><img src="' . $img->get_by_size(300) . '"></a>';
            $ret .= '<input type="hidden" name="' . $name . '" id="' . $id . '" value="' . $default . '">';
            $ret .= '<br><a href="#" class="removeButton removeButton-' . $id . '">' . __('Remove Image', 'rmcommon') . '</a>';
        } else {
            $ret .= '<input type="hidden" name="' . $name . '" id="' . $id . '" value="">';
        }
        $ret .= '</div>';
        $ret .= '<span class="image_manager_launcher btn btn-success">' . __('Select...', 'rmcommon') . '</span>';
        $ret .= '</div>';

        $tpl = RMTemplate::get();

        $tpl->add_head_script('var imgmgr_title = "' . __('Image Manager', 'rmcommon') . '"' . "\n" . 'var mgrURL = "' . RMCURL . '/include/tiny-images.php";');
        $tpl->add_script('cu-image-mgr.js', 'rmcommon');

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
    public function getVersion($includename = true, $module = '', $type = 0)
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMModules', 'get_module_version'));

        return RMModules::get_module_version($module, $includename, 0 == $type ? 'verbose' : 'raw');
    }

    /**
     * Format a module version.
     * Use RMModules::format_module_version() instead
     * @deprecated
     * @param $version
     * @param bool $name
     * @return string
     */
    public function format_version($version, $name = false)
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMModules', 'format_module_version'), E_USER_DEPRECATED);

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
    public function module_config($directory, $option = '')
    {
        trigger_error(sprintf(__('Method %s is deprecated. Use %s::%s instead.', 'rmcommon'), __METHOD__, 'RMSettings', 'module_settings()'), E_USER_DEPRECATED);
        $settings = RMSettings::module_settings($directory, $option);

        if (is_object($settings)) {
            return (array)$settings;
        }

        return $settings;
    }

    /**
     * Creates a new system message that will be shown on next page load
     * @param $message
     * @param int $level
     * @param string $icon
     */
    public function showMessage($message, $level = 0, $icon = '')
    {
        $i = isset($_SESSION['cu_redirect_messages']) ? count($_SESSION['cu_redirect_messages']) + 1 : 0;
        $_SESSION['cu_redirect_messages'][$i]['text'] = htmlentities($message);
        $_SESSION['cu_redirect_messages'][$i]['level'] = $level;
        $_SESSION['cu_redirect_messages'][$i]['icon'] = $icon;
    }
}
