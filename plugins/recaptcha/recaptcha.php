<?php

/**
 * Recaptcha plugin for Common Utilities
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
class RecaptchaCUPlugin extends RMIPlugin
{

    public function __construct()
    {

        // Load language
        load_plugin_locale('recaptcha', '', 'rmcommon');

        $this->info = array(
            'name' => __('reCaptcha for Xoops', 'recaptcha'),
            'description' => __('reCaptcha service for Common Utilities and Xoops', 'recaptcha'),
            'version' => array('major' => 0, 'minor' => 0, 'revision' => 5, 'stage' => -2, 'name' => 'reCaptcha for Xoops'),
            'author' => 'Eduardo Cortés',
            'email' => 'i.bitcero@gmail.com',
            'web' => 'http://eduardocortes.mx',
            'dir' => 'recaptcha',
            //'updateurl' => 'https://www.xoopsmexico.net/modules/vcontrol/'
        );

    }

    public function on_install()
    {
        return true;
    }

    public function on_uninstall()
    {
        return true;
    }

    public function on_update()
    {
        return true;
    }

    public function on_activate($q)
    {
        return true;
    }

    public function options()
    {

        $options['siteKey'] = array(
            'caption' => __('reCaptcha site key', 'recaptcha'),
            'desc' => sprintf(__('Get the site key from you <a href="%s" target="_blank">reCpatcha control panel</a>.', 'recaptcha'), 'https://www.google.com/recaptcha/admin#list'),
            'fieldtype' => 'textbox',
            'valuetype' => 'text',
            'value' => ''
        );

        $options['secret'] = array(
            'caption' => __('reCaptcha secret key', 'recaptcha'),
            'desc' => sprintf(__('Get the secret key from you <a href="%s" target="_blank">reCpatcha control panel</a>.', 'recaptcha'), 'https://www.google.com/recaptcha/admin#list'),
            'fieldtype' => 'textbox',
            'valuetype' => 'text',
            'value' => ''
        );

        $options['theme'] = array(
            'caption' => __('Color theme for widget', 'recaptcha'),
            'desc' => '',
            'fieldtype' => 'radio',
            'valuetype' => 'text',
            'value' => 'light',
            'options' => [
                __('Light', 'recaptcha') => 'light',
                __('Dark', 'recaptcha') => 'dark'
            ]
        );

        $options['type'] = array(
            'caption' => __('Type of CAPTCHA to serve', 'recaptcha'),
            'desc' => '',
            'fieldtype' => 'radio',
            'valuetype' => 'text',
            'value' => 'image',
            'options' => [
                __('Image', 'recaptcha') => 'image',
                __('Audio', 'recaptcha') => 'audio'
            ]
        );

        $options['size'] = array(
            'caption' => __('Size of the widget', 'recaptcha'),
            'desc' => '',
            'fieldtype' => 'radio',
            'valuetype' => 'text',
            'value' => 'normal',
            'options' => [
                __('Compact', 'recaptcha') => 'compact',
                __('Normal', 'recaptcha') => 'normal'
            ]
        );

        $options['admins'] = array(
            'caption' => __('Show CAPTCHA to site admins?', 'recaptcha'),
            'desc' => '',
            'fieldtype' => 'yesno',
            'valuetype' => 'int',
            'value' => '1'
        );

        return $options;

    }

    public function verify()
    {
        global $common, $xoopsUser;

        if($xoopsUser->isAdmin() && $this->settings('admins') == 0){
            return true;
        }

        $remote_url = trim('https://www.google.com/recaptcha/api/siteverify');

        $is_https = (substr($remote_url, 0, 5) == 'https');

        $secret = $this->settings('secret');
        $response = $common->httpRequest()->post('g-recaptcha-response', 'string', '');
        $ip = $_SERVER['REMOTE_ADDR'];

        if('' == $secret || '' == $response){

            if($xoopsUser->isAdmin())
            {
                showMessage(__('Secret key or response captcha has not been provided', 'recaptcha'));
            }
            return false;
        }

        $fields_string = http_build_query([
            'secret' => $secret,
            'response' => $response,
            'remoteip' => $ip
        ]);

        // Run this code if you have cURL enabled
        if (function_exists('curl_init')) {

            // create a new cURL resource
            $ch = curl_init();

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, $remote_url);

            if ($is_https && extension_loaded('openssl')) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HEADER, false);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // grab URL and pass it to the browser
            $response = curl_exec($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);

            // No cURL? Use an alternative code
        } else {

            $context_options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                        "Content-Length: " . strlen($fields_string) . "\r\n",
                    'content' => $fields_string
                )
            );

            $context = stream_context_create($context_options);
            $fp = fopen($remote_url, 'r', false, $context);

            if (!$fp) {
                throw new Exception("Problem with $remote_url, $php_errormsg");
            }

            $response = @stream_get_contents($fp);

            if ($response === false) {
                throw new Exception("Problem reading data from $remote_url, $php_errormsg");
            }
        }

        $response = json_decode($response, true);

        return $response['success'];

    }

    static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new RecaptchaCUPlugin();
        }

        return $instance;
    }

}
