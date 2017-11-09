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
     *
     * If you wish to exclude system from modules list then you need to pass the
     * parameter 'system' as null or false to avoid it.
     *
     * @param mixed $caption
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
    function __construct($caption, $name = '', $multi = 0, $type = 0, $selected = null, $cols = 2, $insert = null, $dirnames = true, $subpages = 0)
    {
        global $common;

        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            if ($multi) {
                $this->set('multiple', null);
            }

            if (is_array($selected)) {
                $this->set('selected', $selected);
            }

            if (null != $insert) {
                $this->set('insert', $insert);
            }

            $this->setWithDefaults('dirnames', $dirnames, true);

            if ($subpages) {
                $this->set('subpages', null);
            }
        }

        if($this->has('selected')){
            $this->set('value', $this->get('selected'));
        }

        $this->setIfNotSet('type', $type ? 'radio' : 'select');
        $this->setIfNotSet('value', []);

        if(false == is_array($this->get('value'))){
            $this->set('value', [$this->get('value')]);
        }

        $this->setIfNotSet('dirnames', true);

        $this->suppressList[] = 'insert';
        $this->suppressList[] = 'dirnames';
        $this->suppressList[] = 'subpages';
        $this->suppressList[] = 'selectedSubs';
        $this->suppressList[] = 'value';
        $this->suppressList[] = 'selected';
        $this->suppressList[] = 'name';
        $this->suppressList[] = 'system';

        !defined('RM_FRAME_APPS_CREATED') ? define('RM_FRAME_APPS_CREATED', 1) : '';

        // Add rmcommon form scripts
        $common->template()->add_script('forms.js', 'rmcommon', ['footer' => 1]);
    }

    public function multi()
    {
        return $this->multi;
    }

    public function setMulti($value)
    {
        if ($value == 0 || $value == 1) {
            //$this->setName($value ? str_replace('[]','',$this->getName()).'[]' : str_replace('[]','',$this->getName()));
            $this->multi = $value;
        }
    }

    public function type()
    {
        return $this->type;
    }

    public function setType($value)
    {
        return $this->type = $value;
    }

    public function selected()
    {
        return $this->selected;
    }

    public function setSelected($value)
    {
        return $this->selected = $value;
    }

    public function sizeOrCols()
    {
        return $this->cols;
    }

    public function setSizeOrCols($value)
    {
        return $this->cols = $value;
    }

    public function inserted()
    {
        return $this->inserted;
    }

    /**
     * Inserta nuevas opciones en el campo
     * @param array $value Array con valor=>caption para las opciones a insertar
     */
    public function setInserted($value)
    {
        $this->inserted = array();
        $this->inserted = $value;
    }

    public function dirnames()
    {
        return $this->dirnames;
    }

    /**
     * Establece si se devuelven los valores con
     * el nombre del directorio del módulo o con
     * el identificador del módulo
     * @param bool $value
     */
    public function setDirNames($value = true)
    {
        $this->dirnames = $value;
    }

    /**
     * @desc Establece las subpáginas seleccionadas por defecto
     * @param array Subpáginas seleccionadas
     */
    public function subpages($subs)
    {
        $this->set('selectedSubs', $subs);
    }

    function render()
    {
        $module_handler = xoops_gethandler('module');
        $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
        $criteria->add(new Criteria('isactive', 1));
        if ($this->get('subpages')) $criteria->add(new Criteria('dirname', 'system'), 'OR');
        $modules = array();

        if($this->get('type') != 'radio'){
            $modules[-1] = __('All', 'rmcommon');
        }

        if (is_array($this->get('insert'))) {
            $modules = $this->get('insert');
        }

        $modulesList = $module_handler->getList($criteria, $this->get('dirnames'));

        // Add system module if not excluded
        if(!$this->has('system') || !$this->get('system') == false){
            $modules[1] = __('System', 'rmcommon');
        }

        foreach ($modulesList as $k => $v) {
            $modules[$k] = $v;
        }

        $type = $this->get('type');
        $selected = $this->get('value');

        if ($type == 'radio' || $type == 'checkbox') {
            // Add js script
            // RMTemplate::getInstance()->add_script('modules_field.js', 'rmcommon', array('directory' => 'include'));

            $pagesOptions = array();
            $attributes = $this->renderAttributeString();

            if ($type == 'checkbox') {
                $name = $this->get('name') . '[%s]';
            }

            $rtn = '<div class="modules-field" id="modules-field-' . $this->get('id') . '">
		            <div>
		                <h4>' . __('Available Modules', 'rmcommon') . '</h4>
		            <ul>';

            $i = 1;
            foreach ($modules as $k => $v) {
                $app = RMModules::load_module($k);
                if($app)
                    $subpages = $app->getInfo('subpages');

                $rtn .= "<li>";
                $rtn .= "<input $attributes name=\"".sprintf($name, $k)."\"
                        value='$k'" .
                        ($k == -1 ? " data-all" : " data-module=\"$k\"") .
			            " id='" . $this->get('id') . "-$k'" .
                            (is_array($selected) && in_array($k, $selected) ? " checked" : '') . "> ";

                if (1 == $k || (false == empty($subpages) && $this->has('subpages') && $k > -1))
                    $rtn .= '<a href="#">' . $v . '</a>';
                else
                    $rtn .= $v;

                /**
                 * Mostramos las subpáginas
                 */
                if ($this->has('subpages') && ($k != '' && $k != -1)) {
                    if ($app->dirname() == 'system') {
                        $subpages = array(
                            'home-page' => __('Home Page', 'rmcommon'),
                            'user' => __('User page', 'dtransport'),
                            'profile' => __('User profile page', 'rmcommon'),
                            'register' => __('Users registration', 'rmcommon'),
                            'edit-user' => __('Edit user', 'rmcommon'),
                            'readpm' => __('Read PM', 'rmcommon'),
                            'pm' => __('Private messages', 'rmcomon')
                        );
                    } else {
                        $subpages =& $app->getInfo('subpages');
                    }
                    if (!empty($subpages)) {
                        $selectedSubs = $this->has('selectedSubs') ? $this->get('selectedSubs') : [];
                        $cr = 0;

                        $rtns = "<ul class=\"subpages-container subpages-" . $k . "\" data-module=\"" . $k . "\">";
                        $j = 2;
                        $cr = 2;
                        if (!is_array($subpages)) $subpages = array();

                        foreach ($subpages as $page => $caption) {
                            $rtns .= "<li class='checkbox'>
                                        <label>
                                            <input type='checkbox' data-parent='" . $k . "' name='" . sprintf($name, $k) . "[subpages][$page]' id='subpages-$k-$page' value='$page'" . (is_array($subpages) && @in_array($page, $selectedSubs[$k]) ? " checked='checked'" : '') . " /> $caption</label></li>";
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

            if ($this->has('subpages')) {

                $rtn .= '<div><h4>' . __('Inner Pages', 'rmcommon') . '</h4>';

                foreach ($pagesOptions as $page) {
                    $rtn .= $page;
                }

                $rtn .= '</div>';

            }

            $rtn .= "</div>";
        } else {
            if ($this->has('multiple')) {
                $this->set('name', $this->get('name') . '[' . $k . ']');
            }
            $this->setIfNotSet('class', 'form-control');
            $attributes = $this->renderAttributeString();

            $rtn = "<select $attributes>";
            foreach ($modules as $k => $v) {
                $rtn .= "<option value='$k'" . (is_array($selected) ? (in_array($k, $selected) ? " value" : '') : '') . ">$v</option>";
            }
            $rtn .= "</select>";

        }

        return $rtn;

    }
}
