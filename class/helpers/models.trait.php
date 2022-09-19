<?php
/**
----------------------------------------
Smart-B ERP
@package:   Sistema Base
@author     Red México
@author     https://bitcero.dev
@author     Eduardo Cortés
@copyright  2013 Red México
@version    $Id$
----------------------------------------
**/
trait RMModels
{
    /**
     * Carga un modulo específico independientemente del controlador
     * para operaciones en otros controladores o modelos
     *
     * @param  string              $module Module name
     * @param  string              $model  Model name
     * @param  bool             $cp     Determina si es un modelo para el panel de control (true) o para la sección frontal
     * @return bool|RMActiveRecord
     */
    public function load_model($module, $model, $cp = false)
    {
        if ('' == trim($module) || '' == trim($model)) {
            return false;
        }

        $path = XOOPS_ROOT_PATH . '/modules/' . $module . ($cp ? '/admin' : '') . '/models';
        $class = ucfirst($module) . '_' . ucfirst($model) . ($cp ? '_Admin_Model' : '_Model');

        if (is_file($path . '/' . mb_strtolower($model) . '.php')) {
            require_once  $path . '/' . mb_strtolower($model) . '.php';
            $model = new $class();
        } else {
            return false;
        }

        return $model;
    }
}
