<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2015 Red Mexico http://www.redmexico.com.mx
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Red Mexico (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

class Services
{
    private $services = array();

    public function __construct()
    {

        /**
         * Load all services provided for modules, plugins and themes.
         * The component must return a service identificator and the
         * class name to run this service.
         */
        $services = array();
        $services = \RMEvents::get()->run_event('rmcommon.get.services', $services);

        /**
         * Due to nature of services, the last added service will have
         * priority over other components for same service.
         */
        foreach ($services as $service) {

            if (!file_exists($service['file'])) {
                continue;
            }

            $this->services[$service['service']] = array(
                'id' => $service['id'],
                'file' => $service['file'],
                'class' => $service['class']
            );
        }

    }

    /**
     * Loads an specific service controller
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function service($name)
    {
        if ('' == $name) {
            throw new \Exception(__('Service name could not be empty!', 'rmcommon'));
        }

        if (!array_key_exists($name, $this->services)) {
            trigger_error(sprintf(__('There are not any service installed for %s!', 'rmcommon'), '<strong>' . $name . '</strong>'));
            return false;
        }

        if (!file_exists($this->services[$name]['file'])) {
            throw new \Exception(sprintf(__('File for service %s does not exists!', 'rmcommon'), '<strong>' . $name . '</strong>'));
        }

        include_once $this->services[$name]['file'];

        if (!class_exists($this->services[$name]['class'])) {
            throw new \Exception(sprintf(__('The class %s for service %s does not exists!', 'rmcommon'), '<strong>' . $this->service[$name]['class'] . '</strong>', '<strong>' . $name . '</strong>'));
        }

        $class = $this->services[$name]['class'];
        $service = $class::getInstance();

        return $service;

    }

    public function __get($name)
    {

        if ('' == $name) {
            throw new \Exception(__('Service name could not be empty!', 'rmcommon'));
        }

        if(false !== $service =  $this->service($name)){
            return $service;
        }

        $service = new ServiceFallback();
        return $service;

    }

    public function getInstance()
    {
        static $instance;

        if (isset($instance))
            return $instance;

        $instance = new Services();

        return $instance;
    }
}

/**
 * Interfase for services providers
 */
Interface ServiceInterface
{
    /**
     * Singleton method for providers
     */
    public static function getInstance();
}

abstract class ServiceAbstract
{
    public function __call($name, $arguments){
        trigger_error(sprintf(__('There are not service using %s method'), $name));
        return null;
    }

    public static function __callStatic($name, $arguments){
        trigger_error(sprintf(__('There are not service using %s method'), $name));
        return null;
    }
}

class ServiceFallback extends ServiceAbstract
{

}