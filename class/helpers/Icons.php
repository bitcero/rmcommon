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

class Icons extends Attributes
{
  private $iconsProviders = [];
  private $providersDetails = [];
  private $noIcon = '';

  public function __construct()
  {
    $this->iconsProviders['rmcommon'] = RMCPATH . '/icons';
    $this->noIcon = '<span class="cu-icon">' . file_get_contents(RMCPATH . '/icons/noicon.svg') . '</span>';
    $this->loadProviders();

    // Add javascript support
    $jsProviders = [];
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
    $providers = [];
    $providers = \RMEvents::get()->trigger('rmcommon.register.icon.provider', $providers);

    if (empty($providers)) {
      return true;
    }

    foreach ($providers as $provider) {
      if ('' == $provider['id']) {
        continue;
      }

      if (!is_dir($provider['directory'])) {
        continue;
      }

      if ('rmcommon' == $provider['id']) {
        throw new \Exception(__('Illegal attempt to replace "Common Utilities" icons provider!', 'rmcommon'));

        return false;
      }

      $this->iconsProviders[$provider['id']] = $provider['directory'];
      $this->providersDetails[$provider['id']] = $provider;
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
        if ('.' == $file || '..' == $file || '.svg' != mb_substr($file, -4) || '.' == mb_substr($file, 0, 1)) {
          continue;
        }

        $list[] = 'svg-' . $id . '-' . mb_substr($file, 0, mb_strlen($file) - 4);
      }
    }

    return $list;
  }

  public function iconsListByProvider()
  {
    $providers = array_keys($this->iconsProviders);
    $icons_list = [];

    foreach($this->providersDetails as $id => $provider){
      if (!is_dir($this->iconsProviders[$id])) {
        continue;
      }

      $dir = opendir($this->iconsProviders[$id]);
      $icons_list[$id]['name'] = $provider['name'];

      while (false !== ($file = readdir($dir))) {
        if ('.' == $file || '..' == $file || '.svg' != mb_substr($file, -4) || '.' == mb_substr($file, 0, 1)) {
          continue;
        }

        $icons_list[$id]['icons'][] = 'svg-' . $id . '-' . mb_substr($file, 0, mb_strlen($file) - 4);
      }
    }

    return $icons_list;
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
    $data = explode('-', $icon);

    if ('svg' != $data[0]) {
      return '';
    }

    if (!array_key_exists($data[1], $this->iconsProviders)) {
      return $this->noIcon;
    }

    if (!is_dir($this->iconsProviders[$data[1]])) {
      return $this->noIcon;
    }

    $fileName = mb_substr($icon, mb_strlen($data[0] . '-' . $data[1] . '-'));
    $filePath = $this->iconsProviders[$data[1]] . '/' . $fileName . '.svg';

    if (!file_exists($filePath)) {
      return $this->noIcon;
    }

    return file_get_contents($filePath);
  }

  /**
   * Gets the content of a icon in SVG format.
   *
   * <p>
   * The parameter $icon must have the next format:
   * </p>
   *
   * <pre>
   * {provider}-{file name}
   * </pre>
   *
   * Example:
   *
   * <pre>
   * $icon = $this->svg('rmcommon-rmcommon');
   * </pre>
   *
   * **Note:** This method will return only the SVG content without the cu-icon wrapper.
   *
   *
   * @param $icon_id
   * @param array $attributes
   * @return bool|string
   */
  public function svg($icon_id, $attributes = []): bool|string
  {
    // Replace the svg- prefix if exists at the beginning in the icon id
    $icon_id = preg_replace('/^svg-/', '', $icon_id);

    // Replace the .svg extension if exists at the end in the icon id
    $icon_id = preg_replace('/\.svg$/', '', $icon_id);

    return $this->getIcon('svg-' . $icon_id, $attributes, false);
  }

  /**
   * Get an icon SVG, font icon or bitmap
   * @param string $icon
   * @param mixed $attributes
   * @return string
   */
  public function getIcon($icon, $attributes = [], $addSpanTag = true)
  {
    parent::__construct($attributes);
    $this->add('class', 'cu-icon');

    /**
     * Check if this is a SVG icon
     */
    if ('svg-' == mb_substr($icon, 0, 4)) {
      /**
       * The icon has additional css classes?
       * If yes, then the classes must be separated by a blank space
       * (e.g. svg-rmcommon-rmcommon text-blue)
       */
      $iconExploded = explode(' ', trim($icon));

      $this->add('class', str_replace($iconExploded[0], ' ', $icon));
      $renderedAttrs = $this->renderAttributeString();

      // Index 0 has the SVG icon
      $iconSVG = $this->providerIcon($iconExploded[0]);

      if ($addSpanTag) {
        return '<span ' . $renderedAttrs . '>' . $iconSVG . '</span>';
      }

      return $iconSVG;
    }

    $renderedAttrs = $this->renderAttributeString();

    // Relative or absolute url?
    $matches = [];
    $absolute = preg_match("/^(http:\/\/|https:\/\/|ftp:\/\/|\/\/)/m", $icon, $matches, PREG_OFFSET_CAPTURE);
    $is_svg = '.svg' == mb_substr($icon, -4);

    // Icon with absolute path
    if ($absolute) {
      if ($is_svg) {
        return '<span ' . $renderedAttrs . '>' . file_get_contents($icon) . '</span>'; //returns SVG code
      }

      return '<span ' . $renderedAttrs . '><img src="' . $icon . '"></span>'; // returns image URL
    }

    // Relative image url?
    $imageFormats = ['.jpg', '.gif', '.png', 'jpeg'];
    if (in_array(mb_substr($icon, -4), $imageFormats, true)) {
      return '<span ' . $renderedAttrs . '><img src="' . $icon . '"></span>';
    }

    // Last option: icon font
    if ($addSpanTag) {
      return '<span ' . $renderedAttrs . '><span class="' . $icon . '"></span></span>';
    }

    return '<span class="' . $icon . '"></span>';
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
