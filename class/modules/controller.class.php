<?php
// $Id$
// --------------------------------------------------------------
// Schooler Pro
// A module for management of scores and notes for students
// Author: Eduardo Cortes
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// URI: https://bitcero.dev
// --------------------------------------------------------------

abstract class RMController
{
    use RMProperties;
    /**
     * Controller data
     */
    private $data = [];
    /**
     * @var string Default action to use in this method. Can be changed in Controller::__construct method.
     */
    public $default = 'index';
    /**
     * @var The parent class for this module. Must be the module controller class.
     */
    public $parent;

    /**
     * @var Name of the current action
     */
    public $action;

    public $parameters;

    protected $model = [];

    protected $tpl;

    protected function __construct()
    {
        $this->tpl = RMTemplate::getInstance();
        $this->tpl->assign('controller', $this);
    }

    /**
     * Loads a model defined by $model parameter.
     * If $model is empty then model with current controler name will be loaded.
     * If $model has been provided, then this model will be loaded, but this model
     * will be searched inside "model" folder belonged to current module.
     *
     * If you with to load a third module model then use method load_model():
     * @param string $model
     * @return bool
     */
    protected function model($model = '')
    {
        if ('' == $model) {
            $model = $this->parent->controller;
            $class = ucfirst($this->parent->directory) . '_' . ucfirst($this->parent->controller) . '_' . (defined('XOOPS_CPFUNC_LOADED') ? 'Admin_' : '') . 'Model';
        } else {
            $class = ucfirst($this->parent->directory) . '_' . ucfirst($model) . '_' . (defined('XOOPS_CPFUNC_LOADED') ? 'Admin_' : '') . 'Model';
        }

        if (is_a($this->model[$model], $class)) {
            return $this->model[$model];
        }

        $file = XOOPS_ROOT_PATH . '/modules/' . $this->parent->directory . (defined('XOOPS_CPFUNC_LOADED') ? '/admin' : '') . '/models/' . mb_strtolower($model) . '.php';

        if (!file_exists($file)) {
            throw new RMException(sprintf(__('The model "%s" does not exists!', 'rmcommon'), $model));
        }

        require_once $file;

        $this->model[$model] = new $class();
        $this->model[$model]->controller = $this;
        $this->model[$model]->module = $this->module;

        return $this->model[$model];
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
     * http://sitio.com/modules/module/index.php/controller/delete/id/1/ // For normal URLs
     * </pre>
     *
     * @param string $action Optional action to call
     * @param array $parameters Parameters as associative array
     * @return string
     */
    public function url($action = '', $parameters = [])
    {
        $url = $this->parent->url . '/' . $this->controller . '/' . $action;
        $url = trim($url, '/');
        $query = '';

        foreach ($parameters as $var => $value) {
            $query .= '/' . $var . '/' . $value;
        }

        return $url . $query;
    }

    protected function acceptMethod($acceptedMethods)
    {
        if (empty($acceptedMethods)) {
            throw new RMException(__('You must provide a valid request method name to be accepted!', 'rmcommon'));
        }

        $acceptableMethods = ['GET', 'POST', 'PUT', 'DELETE'];

        if (is_string($acceptedMethods)) {
            if (!in_array($acceptedMethods, $acceptableMethods, true)) {
                throw new RMException(__('Provided request method is not acceptable!', 'rmcommon'));
            }

            $acceptedMethods = [$acceptedMethods];
        } elseif (is_array($acceptedMethods)) {
            $acceptedMethods = array_intersect($acceptableMethods, $acceptedMethods);

            if (empty($acceptedMethods)) {
                throw new RMException(__('You must provide a valid request method name to be accepted!', 'rmcommon'));
            }
        } else {
            throw new RMException(__('You must provide a valid request method name to be accepted!', 'rmcommon'));
        }

        $currentMethod = $_SERVER['REQUEST_METHOD'];

        return in_array($currentMethod, $acceptedMethods, true);
    }

    protected function getParameter($name, $type = 'string', $default = '')
    {
        if (isset($this->parameters[$name])) {
            return RMHttpRequest::array_value($name, $this->parameters, $type, $default);
        }

        return RMHttpRequest::request($name, $type, $default);
    }

    protected function display()
    {
        $this->parent->display();
    }
}
