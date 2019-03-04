<?php
/**
 * Common Utilities Framework for XOOPS
 *
 * Copyright © 2017 Eduardo Cortés http://www.eduardocortes.mx
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
class crypt
{
    private $method;
    private $key;
    private $allMethods;

    public function __construct($method = null, $key = null)
    {
        global $common;

        if (null === $method) {
            $this->method = $this->getMethod();
        } else {
            $this->method = in_array($method, $this->allMethods(), true) ? $method : $this->getMethod();
        }

        if (null === $key) {
            $this->key = $common->settings->secretkey;
        } else {
            $this->key = $key;
        }
    }

    private function allMethods()
    {
        if (false === empty($this->allMethods)) {
            return $this->allMethods;
        }

        $this->allMethods = openssl_get_cipher_methods();

        return $this->allMethods;
    }

    private function getMethod()
    {
        return in_array('aes-256-cbc', $this->allMethods(), true) ? 'aes-256-cbc' : 'aes-128-cbc';
    }

    public function setKey($key = null)
    {
        global $common;

        if (null === $key) {
            $this->key = $common->settings->secretkey;
        } else {
            $this->key = $key;
        }
    }

    public function setMethod($method = null)
    {
        if (null === $method) {
            $this->method = $this->getMethod();
        } else {
            $this->method = in_array($method, $this->allMethods(), true) ? $method : $this->getMethod();
        }
    }

    public function encrypt($string)
    {
        if ('' == trim($string)) {
            throw new RMException(__('Nothing to encrypt', 'rmcommon'));
        }

        $vector = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));

        $data = openssl_encrypt($string, $this->method, $this->key, 0, $vector);

        return base64_encode($data . '::' . $vector);
    }

    public function decrypt($string)
    {
        if ('' == trim($string)) {
            throw new RMException(__('Nothing to decrypt', 'rmcommon'));
        }

        $string = base64_decode($string, true);
        list($data, $vector) = explode('::', $string, 2);

        return openssl_decrypt($data, $this->method, $this->key, 0, $vector);
    }
}
