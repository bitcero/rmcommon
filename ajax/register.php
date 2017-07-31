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

//$common->checkToken();

$api = $common->httpRequest()::get('api', 'string', '');
$email = $common->httpRequest()::get('email', 'string', '');
$key = $common->httpRequest()::get('key', 'string', '');
$url = $common->httpRequest()::get('url', 'string', '');
$type = $common->httpRequest()::get('type', 'string', '');
$dir = $common->httpRequest()::get('dir', 'string', '');

if('' == $api || '' == $email || '' == $key || '' == $type || '' == $dir){
    $common->ajax()->notifyError(
        __('Please provide all data', 'dtransport')
    );
}

if(false == checkEmail($email)){
    $common->ajax()->notifyError(__('Please provide a valid email.', 'rmcommon'));
}

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
$key = urlencode($key);

$query = "action=license&site=$siteId&id=$dir&type=$type&api=$api&email=$email&key=$key";
$response = json_decode($common->httpRequest()::load_url($url, $query, true), true);

if(null == $response || $response['type'] == 'error'){
    if(null == $response){
        $common->ajax()->notifyError(__('No response from licensing server', 'vcontrol'));
    }

    $common->ajax()->notifyError($response['message'], 1);
}

$license->element = $dir;
$license->type = $type;
$license->data = $response['licenseData'];
$license->date = strtotime($response['date']);
$license->expiration = strtotime($response['expiration']);
$license->identifier = $identifier;

if($license->save()){
    $common->ajax()->response(
        __('Activation has been completed!', 'rmcommon'), 0, 1, [
            'notify' => [
                'type' => 'alert-successfull',
                'icon' => 'svg-rmcommon-key'
            ]
        ]
    );
}

$common->ajax()->notifyError(__('Activation has been completed but data could not be saved. Please try again.', 'rmcommon'));

