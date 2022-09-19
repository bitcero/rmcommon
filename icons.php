<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.eduardocortes.mx
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
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */
require dirname(dirname(__DIR__)) . '/include/cp_header.php';
$common->location = 'icons';

// Add scripts
$common->template()->add_script('icons.min.js', 'rmcommon', ['footer' => 1, 'id' => 'icons-js']);

// Load providers
$providers = \RMEvents::get()->trigger('rmcommon.register.icon.provider', []);

$action = $common->httpRequest()->request('action', 'string', '');

switch ($action) {
    case 'load-icons':

        $common->ajax()->prepare();
        $common->checkToken(true);
        $provider = $common->httpRequest()->get('provider', 'string', '');
        $size = $common->httpRequest()->get('size', 'integer', 32);

        $tempProviders = [];
        foreach ($providers as $item) {
            $tempProviders[$item['id']] = $item;
        }
        unset($item);
        $providers = $tempProviders;
        unset($tempProviders);

        if (!array_key_exists($provider, $providers) && 'rmcommon' != $provider) {
            $common->ajax()->notifyError(__('Icons provider does not exists!', 'rmcommon'));
        }

        $selected = $providers[$provider];

        $name = isset($providers[$provider]['name']) ? $providers[$provider]['name'] : $providers[$provider]['id'];

        $icons = $cuIcons->getIconsList([$provider]);
        $common->template()->assign('icons', $icons);

        $common->template()->assign('selectedProvider', ['id' => 'rmcommon', 'name' => $name]);
        $common->template()->assign('providerPrefix', 'svg-' . $selected['id'] . '-');
        $common->template()->assign('size', $size);

        $common->ajax()->response(
            sprintf(__('Icons from %s', 'rmcommon'), $name),
            0,
            1,
            [
                'content' => $common->template()->render('ajax/ajax-icons.php', 'module', 'rmcommon'),
            ]
        );

        break;
    default:
        // SHOW ICONS

        $common->template()->append('providers', ['id' => 'rmcommon', 'name' => 'Common Utilities']);

        foreach ($providers as $provider) {
            if (!is_dir($provider['directory'])) {
                continue;
            }

            $common->template()->append('providers', ['id' => $provider['id'], 'name' => isset($provider['name']) ? $provider['name'] : $provider['id']]);
        }

        $common->template()->assign('selectedProvider', ['id' => 'rmcommon', 'name' => 'Common Utilities']);
        $common->template()->assign('providerPrefix', 'svg-rmcommon-');

        $icons = $cuIcons->getIconsList(['rmcommon']);
        $common->template()->assign('icons', $icons);

        $common->template()->add_attribute('body', ['class' => 'rmcommon-icons']);

        $common->template()->header();
        $common->template()->display('rmc-icons.php', 'module', 'rmcommon');
        $common->template()->footer();
        break;
}
