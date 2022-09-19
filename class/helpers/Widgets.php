<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2015 Red Mexico https://bitcero.dev
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
 * @copyright    Red Mexico (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

class Widgets
{
    private $widgetsProviders = [];

    public function __construct()
    {
        $this->widgetsProviders['rmcommon'] = [
            'path' => RMCPATH . '/widgets',
            'namespace' => 'Common\Widgets',
        ];
        $this->loadProviders();
    }

    /**
     * Loads new SVG icons provider
     *
     * Components must use evenRmcommonRegisterIconProvider event and return an array
     * with keys 'id' and 'directory'.
     *
     * 'id' key must have an unique identifier to load icons from 'directory'.
     *
     * @throws \Exception
     * @return bool
     */
    private function loadProviders()
    {
        $providers = [];
        $providers = \RMEvents::get()->trigger('rmcommon.register.widgets.provider', $providers);

        if (empty($providers)) {
            return true;
        }

        foreach ($providers as $provider) {
            if ('' == $provider['id']) {
                continue;
            }

            if (!is_dir($provider['path'])) {
                continue;
            }

            if ('rmcommon' == $provider['id']) {
                throw new \Exception(__('Illegal attempt to replace "Common Utilities" widgets provider!', 'rmcommon'));

                return false;
            }

            $this->widgetsProviders[$provider['id']] = [
                'path' => $provider['path'],
                'namespace' => rtrim($provider['namespace'], '\\'),
            ];
        }
    }

    public function getWidgetsList($providers = [])
    {
        /**
         * @todo: Make routines to request a widgets list and information from module
         */
    }

    /**
     * Instantiate a new widget object from a specif provider
     * @param string $provider Provider ID (e.g. rmcommon)
     * @param string $widgetName Widget name. This name must correspond with file name (e.g. 'Counter')
     * @return bool|string
     */
    public function load($provider, $widgetName)
    {
        if ('' == $widgetName || '' == $provider) {
            return false;
        }

        if (false === array_key_exists($provider, $this->widgetsProviders)) {
            trigger_error(sprintf(__('Attempt to load a widget from a non existent provider: %s', 'rmcommon'), $provider));

            return false;
        }

        // File path
        $path = $this->widgetsProviders[$provider]['path'] . '/' . $widgetName . '.php';
        if (false === file_exists($path)) {
            trigger_error(sprintf(__('Attempt to load a non existent widget: %s', 'rmcommon'), $widgetName));

            return false;
        }

        require_once $path;

        if (false === class_exists($this->widgetsProviders[$provider]['namespace'] . '\\' . $widgetName)) {
            trigger_error(sprintf(__('Attempt to load a non existent widget: %s', 'rmcommon'), $widgetName));

            return false;
        }

        $widgetClass = $this->widgetsProviders[$provider]['namespace'] . '\\' . $widgetName;
        $widget = new $widgetClass();
        if (false === is_subclass_of($widget, 'Common\Core\Helpers\WidgetAbstract')) {
            trigger_error(sprintf(__('Attempt to load a non valid widget: %s. Widgets must be extended from WidgetAbstract.', 'rmcommon'), $widgetName));

            return false;
        }

        return $widget;
    }

    public static function getInstance()
    {
        static $instance;

        if (isset($instance)) {
            return $instance;
        }

        $instance = new self();

        return $instance;
    }
}
