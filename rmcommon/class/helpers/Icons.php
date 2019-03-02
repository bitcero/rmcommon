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

class Icons extends Attributes
{
    private $iconsProviders = array();
    private $noIcon = '';

    public function __construct()
    {
        $this->iconsProviders['rmcommon'] = RMCPATH . '/icons';
        $this->noIcon = '<span class="cu-icon">' . file_get_contents(RMCPATH . '/icons/noicon.svg') . '</span>';
        $this->loadProviders();

        // Add javascript support
        $jsProviders = array();
        foreach ($this->iconsProviders as $provider => $path) {
            $jsProviders[$provider] = \RMUris::relative_url(str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $path));
        }
        \RMTemplate::get()->add_inline_script('var iconsProviders = ' . json_encode($jsProviders) . ';', 0);
    }

    /**
     * Loads new SVG icons provider
     *
     * Components must use evenRmcommonRegisterIconProvider event and return an array
     * with keys 'id' and 'directory'.
     *
     * 'id' key must have an unique identifier to load icons from 'directory'.
     *
     * @return bool
     * @throws \Exception
     */
    private function loadProviders()
    {
        $providers = array();
        $providers = \RMEvents::get()->trigger('rmcommon.register.icon.provider', $providers);

        if (empty($providers)) {
            return true;
        }

        foreach ($providers as $provider) {
            if ($provider['id'] == '') {
                continue;
            }

            if (!is_dir($provider['directory'])) {
                continue;
            }

            if ($provider['id'] == 'rmcommon') {
                throw new \Exception(__('Illegal attempt to replace "Common Utilities" icons provider!', 'rmcommon'));
                return false;
            }

            $this->iconsProviders[$provider['id']] = $provider['directory'];
        }
    }

    public function getIconsList($providers = [])
    {
        if (empty($providers)) {
            $providers = array_keys($this->iconsProviders);
        }

        $list = [];

        foreach ($providers as $id) {
            if (!array_key_exists($id, $this->iconsProviders)) {
                continue;
            }

            if (!is_dir($this->iconsProviders[$id])) {
                continue;
            }

            $dir = opendir($this->iconsProviders[$id]);
            while (false !== ($file = readdir($dir))) {
                if ($file == '.' || $file == '..' || substr($file, -4) != '.svg' || substr($file, 0, 1) == '.') {
                    continue;
                }

                $list[] = 'svg-' . $id . '-' . substr($file, 0, strlen($file) - 4);
            }
        }

        return $list;
    }

    /**
     * Gets the content of a icon in SVG format.
     *
     * <p>
     * The parameter $icon must have the next format:
     * </p>
     *
     * <pre>
     * svg-{provider}-{file name}
     * </pre>
     *
     * Example:
     *
     * <pre>
     * $icon = $this->svgIcon('svg-rmcommon-rmcommon');
     * </pre>
     *
     * Previous exmaple will return the file content (SVG code) from an icon located in
     * /modules/rmcommon/icons/rmcommon.svg
     *
     *
     * @param $icon
     * @return bool|string
     */
    private function providerIcon($icon)
    {
        $data = explode("-", $icon);

        if ('svg' != $data[0]) {
            return '';
        }

        if (!array_key_exists($data[1], $this->iconsProviders)) {
            return $this->noIcon;
        }

        if (!is_dir($this->iconsProviders[$data[1]])) {
            return $this->noIcon;
        }

        $fileName = substr($icon, strlen($data[0] . '-' . $data[1] . '-'));
        $filePath = $this->iconsProviders[$data[1]] . '/' . $fileName . '.svg';

        if (!file_exists($filePath)) {
            return $this->noIcon;
        }

        return file_get_contents($filePath);
    }

    /**
     * Get an icon SVG, font icon or bitmap
     * @param string $icon
     * @return string
     */
    public function getIcon($icon, $attributes = [])
    {
        parent::__construct($attributes);
        $this->add('class', 'cu-icon');

        /**
         * Check if this is a SVG icon
         */
        if ('svg-' == substr($icon, 0, 4)) {

            /**
             * The icon has additional css classes?
             * If yes, then the classes must be separated by a blank space
             * (e.g. svg-rmcommon-rmcommon text-blue)
             */
            $iconExploded = explode(" ", trim($icon));

            $this->add('class', str_replace($iconExploded[0], ' ', $icon));
            $renderedAttrs = $this->renderAttributeString();

            // Index 0 has the SVG icon
            $iconSVG = $this->providerIcon($iconExploded[0]);
            return '<span ' . $renderedAttrs . '>' . $iconSVG . '</span>';
        }

        $renderedAttrs = $this->renderAttributeString();

        // Relative or absolute url?
        $matches = array();
        $absolute = preg_match("/^(http:\/\/|https:\/\/|ftp:\/\/|\/\/)/m", $icon, $matches, PREG_OFFSET_CAPTURE);
        $is_svg = substr($icon, -4) == '.svg';

        // Icon with absolute path
        if ($absolute) {
            if ($is_svg) {
                return '<span '.$renderedAttrs.'>' . file_get_contents($icon) . '</span>'; //returns SVG code
            } else {
                return '<span '.$renderedAttrs.'><img src="' . $icon . '"></span>'; // returns image URL
            }
        }

        // Relative image url?
        $imageFormats = array('.jpg', '.gif', '.png', 'jpeg');
        if (in_array(substr($icon, -4), $imageFormats)) {
            return '<span '.$renderedAttrs.'><img src="' . $icon . '"></span>';
        }

        // Last option: icon font
        return '<span '.$renderedAttrs.'><span class="' . $icon . '"></span></span>';
    }

    public static function getInstance()
    {
        static $instance;

        if (isset($instance)) {
            return $instance;
        }

        $instance = new Icons();

        return $instance;
    }
}
