<?php

/**
 * Recaptcha for Common Utilities
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
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
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      recaptcha
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */
class RecaptchaService extends \Common\Core\Helpers\ServiceAbstract implements \Common\Core\Helpers\ServiceInterface
{
    public $siteKey = '';
    private static $instances = 1;
    private $settings;

    public function __construct()
    {
        global $common, $xoopsUser;

        $this->settings = RMSettings::plugin_settings('recaptcha', true);

        if ($this->settings->admins == 0) {
            return true;
        }

        $common->template()->add_inline_script("var reCounter = 1;\nvar reParams = {siteKey: '" . $this->settings->siteKey . "',theme:'" . $this->settings->theme . "',type:'" . $this->settings->type . "',size:'" . $this->settings->size . "'};");

        if ('' == $this->settings->siteKey && $xoopsUser && $xoopsUser->isAdmin()) {
            showMessage(__('reCaptcha site key has not been specified in reCaptcha plugin. Please correct this.', 'recaptcha'), RMMSG_WARN);
        }

    }

    public function render()
    {
        global $common;

        if ($this->settings->admins == 0) {
            return null;
        }

        $common->template()->add_script('precaptcha.min.js', 'rmcommon', ['directory' => 'plugins/recaptcha', 'id' => 'recaptcha-js', 'footer' => 1]);
        $common->template()->add_script('https://www.google.com/recaptcha/api.js?onload=reCaptchaCallback&render=explicit', '', ['id' => 'precaptcha-js', 'footer' => 1]);
        $common->template()->add_inline_script('reCounter++;');
        $this::$instances++;

        return '<div id="recaptcha-' . ($this::$instances - 1) . '"></div>';

    }

    public function verify()
    {
        global $common;

        $plugin = $common->plugins()->load('recaptcha');
        return $plugin->verify();
    }

    /**
     * Singleton method
     */
    static function getInstance()
    {
        static $instance;

        if (isset($instance))
            return $instance;

        $instance = new RecaptchaService();

        return $instance;
    }
}