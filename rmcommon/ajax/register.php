<?php
/**
 * Common Utilities Framework
 * More info at Eduardo Cortés Website (www.eduardocortes.mx)
 *
 * Copyright © 2017 Eduardo Cortés (http://www.eduardocortes.mx)
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

include '../../../include/cp_header.php';

$common->ajax()->prepare();

$common->checkToken();

class RegisterHelper
{
    /**
     * Checks if a item can be registered and returns basic data
     * @param $item
     * @param $type
     * @return array|bool
     */
    function isValid($item, $type)
    {
        global $common;

        switch($type){
            case 'module':
                $module = $common->modules()::load($item);
                if($module->isNew()){
                    return false;
                }
                $url = $module->getInfo('updateurl');
                $name = $module->getInfo('name');
                break;
            case 'plugin':
                $plugin = $common->plugins()->load($item);
                $url = $plugin->get_info('updateurl');
                $name = $plugin->get_info('name');
                break;
            case 'theme':
                $xtAssember = new XtAssembler($item);
                if(false == $common->nativeTheme){
                    return false;
                }
                $url = $xtAssember->theme()->getInfo('updateurl');
                $name = $xtAssember->theme()->getInfo('name');
                break;
        }

        if('' == $url || $url == null){
            return false;
        }

        return [
            'name' => $name,
            'url' => $url
        ];
    }

    /**
     * Performs registration operations
     */
    function registerItem($reactivate = false)
    {
        global $common;

        $api = urldecode($common->httpRequest()::post('api', 'string', ''));
        $email = urldecode($common->httpRequest()::post('email', 'string', ''));
        $type = $common->httpRequest()::post('type', 'string', '');
        $dir = $common->httpRequest()::post('dir', 'string', '');

        if($reactivate){
            $licenseKey = $common->httpRequest()::post('license', 'string', '');
            $activationKey = $common->httpRequest()::post('key', 'string', '');
        } else {
            $licenseKey = $common->httpRequest()::post('key', 'string', '');
        }

        if('' == $api || '' == $email || '' == $licenseKey || '' == $type || '' == $dir || ($reactivate && '' == $activationKey)){
            $common->ajax()->notifyError(
                __('Please provide all data', 'dtransport')
            );
        }

        $data = $this->isValid($dir, $type);

        if(false == $data){
            $common->ajax()->notifyError(__('Specified item has not registration capabilities!', 'rmcommon'));
        }

        if(false == checkEmail($email)){
            $common->ajax()->notifyError(__('Please provide a valid email.', 'rmcommon'));
        }

        $url = $data['url'];

        if('' == $url || false == filter_var($url, FILTER_VALIDATE_URL)){
            $common->ajax()->notifyError(__('Provided URL is not valid!', 'rmcommon'));
        }

        if('' == $common->settings->siteId && 32 != strlen($common->settings->siteId)){
            $siteId = urlencode(md5(crypt(XOOPS_LICENSE_KEY . XOOPS_URL . time(), $common->settings->secretkey)));
            $common->settings()->setValue('rmcommon', 'siteId', $siteId);
        } else {
            $siteId = $common->settings->siteId;
        }

        $identifier = md5($type.'-'.$dir);
        $license = new \Common\Core\License($identifier);

        $api = urlencode($api);
        $email = urlencode($email);
        $licenseKey = urlencode($licenseKey);
        if($reactivate){
            $activationKey = urlencode($activationKey);
        }

        $query = "action=" . ($reactivate ? 'reactivate' : 'license') . "&site=$siteId&id=$dir&type=$type&api=$api&email=$email&key=$licenseKey";
        if($reactivate){
            $query .= "&activation=$activationKey";
        }
        $response = json_decode($common->httpRequest()::load_url($url, $query, true), true);

        if(null == $response || $response['type'] == 'error'){
            if(null == $response){
                $common->ajax()->notifyError(__('No response from licensing server', 'vcontrol'));
            }

            if(isset($response['code'])){

                $common->template()->assign('name', $data['name']);
                $common->template()->assign('type', $type);
                $common->template()->assign('api', $api);
                $common->template()->assign('email', $email);
                $common->template()->assign('license', $licenseKey);
                $common->template()->assign('item', $dir);

                $common->ajax()->response(
                    $response['message'],1, 1, [
                        'code' => $response['code'],
                        'notify' => $response['notify'],
                        'form' => $common->template()->render('ajax/rmc-reactivate.php')
                    ]
                );

            } else {
                $common->ajax()->notifyError($response['message'], 1);
            }
        }

        $license->element = $dir;
        $license->type = $type;
        $license->data = $response['licenseData'];
        $license->date = strtotime($response['date']);
        $license->expiration = strtotime($response['expiration']);
        $license->identifier = $identifier;

        if($license->save()){

            $message = '<h3>' . __('Thank you!', 'rmcommon') . '</h3> ';
            $message .= '<p>' . sprintf(__('%s has been activated with next key:', 'rmcommon'), '<strong>' . $data['name'] . '</strong>') . '</p>';
            $message .= '<code>' . base64_decode($response['chain']) . '</code>';

            $common->ajax()->response(
                __('Activation has been completed!', 'rmcommon'), 0, 1, [
                    'notify' => [
                        'type' => 'alert-success',
                        'icon' => 'svg-rmcommon-key'
                    ],
                    'activation' =>  $message
                ]
            );
        }

        $common->ajax()->notifyError(__('Activation has been completed but data could not be saved. Please try again.', 'rmcommon'));
    }

    public function form()
    {
        global $common;

        $type = $common->httpRequest()::post('type', 'string', '');
        $dir = $common->httpRequest()::post('dir', 'string', '');

        if('' == $type || '' == $dir){
            $common->ajax()->notifyError(
                __('Provided data is not valid!', 'dtransport')
            );
        }

        $data = $this->isValid($dir, $type);

        if(false == $data){
            $common->ajax()->notifyError(__('Specified item has not registration capabilities!', 'rmcommon'));
        }

        $common->template()->assign('name', $data['name']);
        $common->template()->assign('type', $type);
        $common->template()->assign('item', $dir);

        $common->ajax()->response(
            'Registrando', 0, 1, [
                'form' => $common->template()->render('ajax/rmc-regform.php')
            ]
        );
    }

}

$action = $common->httpRequest()::post('action', 'string', '');
$registerHelper = new RegisterHelper();
switch($action){
    case 'form':
        $registerHelper->form();
        break;
    case 'register':
        $registerHelper->registerItem();
        break;
    case 'reactivate':
        $registerHelper->registerItem(true);
        break;
}