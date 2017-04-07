<?php
/**
 * reCaptcha plugin for Common Utilities
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
 * @copyright    Eduardo Cortés (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      recaptcha
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

class RecaptchaPluginRmcommonPreload
{

    /**
     * For new RMCommon service component
     * @param array $services All added services
     * @return array
     */
    public function eventRmcommonGetServices( $services ){

        $services[] = array(
            'id'            => 'recaptcha', // provider id
            'name'          => 'reCaptcha for Xoops', // Provider name
            'description'   => __('reCatpcha service for Common Utilities', 'recaptcha'),
            'service'       => 'captcha', // Service to provide
            'file'          => RMCPATH . '/plugins/recaptcha/class/RecaptchaService.php',
            'class'         => 'RecaptchaService'
        );

        return $services;

    }
    
}
