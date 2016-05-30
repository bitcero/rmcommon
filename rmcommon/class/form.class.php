<?php
// $Id: form.class.php 1016 2012-08-26 23:28:48Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


global $xoopsModule, $xoopsConfig, $rmEvents;

if ($dir = opendir(RMCPATH . '/class/fields')) {
    while (false !== ($file = readdir($dir))) {
        if ($file == '.' || $file == '..' || substr($file, -4) != '.php') {
            continue;
        }

        include_once RMCPATH . '/class/fields/' . $file;
    }
}

include_once RMCPATH . '/api/editors/tinymce/tinyeditor.php';

$rmEvents->run_event("rmcommon.form.loader");

/**
 * @desc Controlador del editor TinyMCE
 */
$tiny = TinyEditor::getInstance();
$tiny->configuration = array('mode' => 'exact',
    'theme' => 'advanced',
    'skin' => "exm_theme",
    'inlinepopups_skin' => 'exm',
    //'plugins'=>"inlinepopups,spellchecker,media,fullscreen,exmsystem",
    'plugins' => $rmEvents->run_event('rmcommon.tiny.plugins.editor', "exmsystem,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template"),
    'theme_advanced_buttons1' => RMEvents::get()->run_event('rmcommon.tinybuttons.toolbar1', "bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,image,fullscreen,|,exm_more,exm_adv"),
    'theme_advanced_buttons2' => RMEvents::get()->run_event('rmcommon.tinybuttons.toolbar2', "underline,justifyfull,forecolor,|,removeformat,|,media,charmap,|,outdent,indent,|,undo,redo,|,exm_page,exm_img,exm_icons"),
    'theme_advanced_buttons3' => RMEvents::get()->run_event('rmcommon.tinybuttons.toolbar3', "styleselect,formatselect,insertimage,|,tablecontrols"),
    'theme_advanced_buttons4' => RMEvents::get()->run_event('rmcommon.tinybuttons.toolbar4', ""),
    'spellchecker_languages' => "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",
    'theme_advanced_toolbar_location' => "top",
    'theme_advanced_toolbar_align' => "left",
    'theme_advanced_statusbar_location' => "bottom",
    'theme_advanced_resizing' => "1",
    'theme_advanced_resize_horizontal' => "",
    'dialog_type' => "modal",
    'relative_urls' => "",
    'remove_script_host' => "",
    'convert_urls' => "",
    'apply_source_formatting' => "",
    'remove_linebreaks' => "1",
    'paste_convert_middot_lists' => "1",
    'paste_remove_spans' => "1",
    'paste_remove_styles' => "1",
    'gecko_spellcheck' => "1",
    'entities' => "38,amp,60,lt,62,gt",
    'accessibility_focus' => "1",
    'tab_focus' => "'=>prev,'=>next",
    'save_callback' => "switchEditors.saveCallback",
    'content_css' => RMTemplate::get()->generate_url('editor.css', 'rmcommon', 'css'));

/**
 * Esta clase controla la generación de formularios automáticamente.<br />
 * Esta clase es un sustituto par ala clase XoopsForm
 */
class RMForm extends \Common\Core\Helpers\Attributes
{
    protected $suppressList = ['title', 'addtoken'];
    /**
     * Sets the default class for new fields
     * @var string
     */
    public $fieldClass = '';

    private $_fields = array();
    protected $_name = '';
    protected $_action = '';
    protected $_extra = '';
    protected $_method = '';
    protected $_title = '';

    private $_othervalidates = '';
    private $_alertColor = '#FF0000';
    private $_okColor = '#000';

    private $editores = ''; // LIsta de editores Tiny
    private $_tinytheme = 'advanced';
    private $_tinycss = '';
    private $tiny_valid_tags = 'a[name|href|target|title|onclick],code[class,id],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|longdesc|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]';

    private $row_extras = array();

    /**
     * @param string $title Titulo que se desplegar en la tabla del formulario
     * @param string $name Nombre del formulario
     * @param string $action Post o Get (Default post)
     * @param bool $addtoken Crea el cdigo de seguridad de la sesin con el formulario (default true)
     */
    function __construct($title, $name = '', $action = '', $method = 'post', $addtoken = true)
    {
        global $xoopsSecurity, $xoops;

        if (is_array($title)) {

            parent::__construct($title);

        } else {

            parent::__construct([]);
            $this->set('title', $title);
            $this->set('name', $name);
            $this->set('action', $action);
            $this->set('method', $method);
            $this->set('addtoken', $addtoken);
            $this->set('class', 'form-horizontal');

        }

        if (!$this->has('action')) {
            $this->set('method', 'post');
        }

        if(!$this->has('class')){
            $this->set('class', 'form-horizontal');
        }

        RMTemplate::getInstance()->add_style('forms.min.css', 'rmcommon', ['id' => 'forms-css']);
        //RMTemplate::getInstance()->add_style('js-widgets.css', 'rmcommon');
        RMTemplate::getInstance()->add_jquery(true);
        RMTemplate::getInstance()->add_script('jquery.validate.min.js', 'rmcommon', array('footer' => 1));
        RMTemplate::getInstance()->add_script('forms.js', 'rmcommon', array('footer' => 1));

        if ($addtoken) $this->addElement(new RMFormHidden('XOOPS_TOKEN_REQUEST', $xoopsSecurity->createToken()));

    }

    /**
     * render attributes as a string to include in HTML output
     *
     * @return string
     */
    public function renderAttributeString()
    {
        $this->suppressRender($this->suppressList);

        // generate id from name if not already set
        if (!$this->has('id')) {
            $id = $this->get('name');
            $this->set('id', $id);
        }
        return parent::renderAttributeString();
    }


    /**
     * Establece o modifica el ttulo del formulario
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->_title = $value;
    }

    /**
     * Obtiene el ttulo del formulario
     * @return string "titulo del formulario"
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Establece la informacin adicional del formulario.
     * Mediante esta funcin se puede pasar informacin de estilos
     * tipos enctype u otra informacin adicional que se desee incluir
     * dentro del tag <form ..>
     * @param string $extra
     */
    public function setExtra($extra)
    {
        $this->_extra = $extra;
    }

    /**
     * Devuelve el contenido extra del tag FORM
     * @return string
     */
    public function getExtra()
    {
        return $this->_extra;
    }

    /**
     * Establece el mtodo de envo del formulario
     * el cual puede ser 'POST' o 'GET'
     * @param string $method (post o get)
     */
    public function setMethod($method)
    {
        if ($method == 'post' || $method == 'get') {
            $this->_method = $method;
        }
    }

    public function method()
    {
        return $this->_method;
    }

    /**
     * Establece el nombre del formulario
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = trim($name);
    }

    /**
     * Recupera el nombre del formulario
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Establece el script donde se procesar el formulario
     * @param string $action Url del documento destino
     */
    public function setAction($action)
    {
        $this->_action = $action;
    }

    /**
     * Recupera el destion del formulario
     * @return string URL del documento
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Return the path for default style sheet
     */
    public function cssfile()
    {
        return 'forms.min.css';
    }

    /**
     * Limpiamos el array de elementos creados con la funcin {@link addElement()}
     * @return void
     */
    public function clear($field = '')
    {
        if ($field != '') {
            if (isset($this->_fields[$field])) unset($this->_fields[$field]);
            return;
        }
        $this->_fields = array();
        $this->_name = '';
        $this->_action = '';
        $this->_extra = '';
        $this->_method = '';
        $this->_title = '';
    }

    /**
     * Agregamos nuevos elementos
     * Estos elementos son instanacias de algun elemento de formulario
     * @param RMFormElement $element
     * @param bool $required true = Elemento requerido
     * @param string $css_type Content Type: email,url, etc.
     * @return object
     */
    public function addElement(&$element, $required = false, $css_type = '')
    {
        $element->setForm($this->_name);
        $ret['field'] = $element;
        $ret['class'] = get_class($element);
        if (get_class($element) == 'RMFormEditor') {
            if ($element->getType() == 'tiny') $this->editores[] = $element->getName();
        }

        if ($required)
            $element->addClass('required');

        if ($css_type != '')
            $element->addClass($css_type);

        if ($element->getName() != '') {
            $this->_fields[$element->getName()] = $ret;
        } else {
            $this->_fields[] = $ret;
        }

        //$formControls = ['RMFormText', 'RMFormSelect', 'RMTextArea', 'RMFormFile'];

        if ('' != $this->fieldClass) {
            $element->add('class', $this->fieldClass);
        }

        return $element;

    }

    public function elements()
    {
        return $this->_fields;
    }

    public function &element($name)
    {
        if (isset($this->_fields[$name])) {
            return $this->_fields[$name]['field'];
        }
        return null;
    }

    /**
     * Agrega una cadena para comprobar un campo
     */
    public function addValidateField($name, $type = '', $required = 0, $text = '')
    {
        if ($name == '') return;
        if ($text == '') return;

        $this->_othervalidates = ($this->_othervalidates == '' ? "" : ",") . "$name|$type|$required|$text";

    }

    /**
     * Set de funciones útiles únicamente con el editor TinyMCE
     */
    public function tinyCSS($url)
    {
        $tiny = TinyEditor::getInstance();
        $tiny->add_config('content_css', $url);
    }

    public function getTinyCSS()
    {
        $tiny = TinyEditor::getInstance();
        return $tiny->configuration['content_css'];
    }

    /**
     * Establece información extra para las diferentes filas de la tabla
     * generada por la clase. Estas deben llamarse por medio de su id.
     * Mucho cuidado con la utilización de este método pues no se hace nignuna
     * comprobación especial de los parámetros pasados.
     * @param string Datos extra
     * @param string Id de la fila. Debe iniciar con row_
     */
    public function setRowExtras($extra, $id)
    {
        $this->row_extras[$id] = $extra;
    }

    /**
     * Generamos el cdigo HTML del formulario.
     * Esta funcin automticamente llama a la funcin {@link render()} de los
     * elementos del formulario (EXMFormElement) para generar a su vez
     * su propia salida HTML
     * @return string Todo el cdigo HTML del formulario
     */
    public function render($form_tag = true)
    {
        $attributes = $this->renderAttributeString();
        /**
         * Generamos el cdigo JavaScript para comprobación del formulario
         */
        $form =& $this;
        ob_start();
        include RMTemplate::getInstance()->path('rmc-forms.php', 'module', 'rmcommon');
        return ob_get_clean();

    }

    /**
     * Crea el formulario y lo asigna a una matriz
     * para un mayor control sobre la presentación de los campos
     * @param string Nombre de la variable smarty
     * @param string Plantilla que se utilizará
     * @param bool Incluir javascript
     * @return string
     */
    public function renderForTemplate()
    {

        $form = array();
        $attributes = $this->renderAttributeString();

        $req = '';
        $callmethod = '';
        foreach ($this->_fields as $field) {

            $element = $field['field'];
            $form['fields'][] = array(
                'type' => get_class($element),
                'content' => $element->render(),
                'caption' => $element->getCaption(),
                'desc' => $element->getDescription(),
                'name' => $element->getName()
            );

            if (is_a($element, 'RMFormEditor')) {
                if ($element->getType() == 'tiny') {
                    $callmethod = 'tinyMCE.triggerSave(); ';
                }
            }

        }

        if ($this->_addtoken) {
            $form['fields'][] = array('type' => 'RMFormHidden', 'content' => $GLOBALS['xoopsSecurity']->getTokenHTML(),
                'caption' => '', 'desc' => '');
        }

        $req .= $req == '' ? ($this->_othervalidates != '' ? $this->_othervalidates : '') : ($this->_othervalidates != '' ? ',' . $this->_othervalidates : '');

        $rtn = "<form $attributes";
        if ($req != '') {
            $rtn .= " onsubmit=\"" . $callmethod . "rmValidateForm(this, '$req');return document.rmValidateReturnValue;\"";
        }
        $rtn .= ">";
        $form['title'] = $this->get('title');
        $form['tag'] = $rtn;
        $form['lang_req'] = __('Fields marked with (*) are required.', 'rmcommon');

        return $form;

    }

    /**
     * Funcin para devolver el tipo correcto de
     * campo para la validacin del Formulario
     * Esta funcin acepta como parmetro uno de los siguientes valores:
     * Email, Num, Range y Select.
     * Un rango debe proporcionarse con el formato RangeX,Y
     * @param string $type
     * @return string
     */
    private function getType($type)
    {
        if ($type == 'Email') {
            return "email";
        } elseif ($type == 'Num') {
            return "num";
        } elseif (substr($type, 0, 5) == 'Range') {
            return "range" . str_replace("Range", "", $type);
        } elseif (substr($type, 0, 6) == 'Select') {
            return "difto" . str_replace("Select", "", $type);
        } else {
            return $type;
        };
    }

    /**
     * Escribe directamente el conetnido HTML con la funcin echo
     */
    public function display($js = true)
    {
        $attributes = $this->renderAttributeString();
        $form =& $this;
        include RMTemplate::getInstance()->path('rmc-forms.php', 'module', 'rmcommon');
        //echo $this->render($js);
    }

    /**
     * @desc Establece las etiquetas HTML válidas para Tiny
     * @param string $tags
     */
    public function setTinyTags($tags)
    {
        $tiny = TinyEditor::getInstance();
        $tiny->configuration['extended_valid_elements'] = $tags;
    }

    public function tinyTags()
    {
        $tiny = TinyEditor::getInstance();
        return $tiny->configuration['extended_valid_elements'];
    }
    /**
     * @desc Imprime el código javascript para Tiny
     * @param array $editores Array con los nombres de los campos editores
     */

}
