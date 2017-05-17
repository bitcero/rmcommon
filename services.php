<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.redmexico.com.mx
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

include_once '../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/rmcommon/admin-loader.php';
$common->location = 'services';

/**
 * Show all registered services
 */
function services_manager_list(){
    global $common, $xoops;

    $common->breadcrumb()->add_crumb(__('Services Manager', 'rmcommon'));

    $common->template()->assign('allServices', $common->services()->registeredServices());
    $common->template()->assign('enabledProviders', $common->services()->enabledProviders());
    $common->template()->add_body_class('dashboard');

    // INclude JS
    $common->template()->add_script('cu-services.min.js', 'rmcommon', ['footer' => 1]);

    $common->template()->header();

    $common->template()->display('rmc-services.php');

    $common->template()->footer();

}

/**
 * Write to file a new provider for specific service
 */
function services_assign_provider(){
    global $xoopsSecurity, $common;

    $common->ajax()->prepare();

    if(!$xoopsSecurity->check(true, false, 'CUTOKEN')){
        $common->ajax()->response(
            __('Not authorized!', 'rmcommon'), 1, 0, ['reload' => true]
        );
    }

    $service = $common->httpRequest()->post('service', 'string', '');
    $provider = $common->httpRequest()->post('provider', 'string', '');
    $name = $common->httpRequest()->post('name', 'string', '');

    if('' == $service || '' == $provider){
        $common->ajax()->response(
            __('Service name or provider name are not valid!', 'rmcommon'), 1, 1, [
                'notify' => [
                    'title' => __('Services', 'rmcommon'),
                    'type' => 'alert-danger',
                    'icon' => 'svg-rmcommon-error',
                ]
            ]
        );
    }

    $common->services()->registerProvider($service, $provider);

    $common->ajax()->response(
        sprintf(__('Provider %s for service %s registered successfully', 'rmcommon'), '<strong>' . $name . '</strong>', '<strong>' . strtoupper($service) . '</strong>'), 0, 1, [
            'notify' => [
                'title' => __('Services', 'rmcommon'),
                'type' => 'alert-success',
                'icon' => 'svg-rmcommon-ok-circle',
            ]
        ]
    );

}

$action = RMHttpRequest::request('action', 'string', '');

switch($action){
    case 'assign-provider':
        services_assign_provider();
        break;
    default:
        services_manager_list();
        break;
}