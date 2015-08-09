<?php
// $Id$
// --------------------------------------------------------------
// Schooler Pro
// A module for management of scores and notes for students
// Author: Eduardo Cortes
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// URI: http://www.redmexico.com.mx
// --------------------------------------------------------------

abstract class RMController
{
    use RMProperties;
    /**
     * Controller data
     */
    private $data = array();
    /**
     * @var string Default action to use in this method. Can be changed in Controller::__construct method.
     */
    public $default = 'index';
    /**
     * @var The parent class for this module. Must be the module controller class.
     */
    public $parent;

    protected $model;

    protected $tpl;

    protected function __construct(){

        $this->tpl = RMTemplate::get();

    }

    protected function model(){

        $class = ucfirst($this->parent->directory) . '_' . ucfirst( $this->parent->controller ) . '_' . ( defined("XOOPS_CPFUNC_LOADED") ? 'Admin_' : '' ) . 'Model';

        if ( is_a( $this->model, $class ))
            return $this->model;

        $file = XOOPS_ROOT_PATH . '/modules/' . $this->parent->directory . ( defined("XOOPS_CPFUNC_LOADED") ? '/admin' : '' ) . '/models/' . strtolower( $this->parent->controller ) . '-model.php';

        if ( !file_exists( $file ) )
            return false;

        include_once $file;

        $this->model = new $class();
        $this->model->controller = $this;
        $this->model->module = $this->module;

        return $this->model;

    }

    /**
     * This function allows to format the URL for controller accourding to given parameters
     * Example:
     * <pre>
     * $this->url( 'delete', array( 'id' => 1 ) );
     * </pre>
     *
     * The result of this instruction will be:
     * <pre>
     * http://sitio.com/controller/delete/id/1/ // When url rewiting is enabled
     *
     * http://sitio.com/modules/module/?s=controller/delete&amp;id=1 // For normal URLs
     * </pre>
     *
     * @param string $action Optional action to call
     * @param array $parameters Parameters as associative array
     * @return string
     */
    public function url( $action = '', $parameters = array() ){

        $url = $this->parent->url . '/' . $this->controller . '/' . $action;
        $url = trim($url, "/");
        $query = '';

        foreach ( $parameters as $var => $value ){
            if ( $this->parent->settings->permalinks )
                $query .= '/' . $var . '/' . $value;
            else
                $query .= '&amp;' . $var . '=' . $value;
        }

        return $url . $query;

    }

    protected function get_parameters(){

        return $this->parent->parameters;

    }

}



