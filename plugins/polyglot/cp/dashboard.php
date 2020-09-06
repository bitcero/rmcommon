<?php
/**
 * Polyglot for Common Utilities
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
 * @package      polyglot
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

global $common, $xoopsSecurity;

function polyglot_check_code($code, $file){
    global $xoopsSecurity, $common;

    if (!$xoopsSecurity->check(true, false, 'CUTOKEN')) {
        $common->ajax()->response(
            __('You have not authorization for this action!', 'polyglot'), 1, 0, ['reload' => 1]
        );
    }

    if ('' == $code) {
        $common->ajax()->response(
            __('Specified language is not valid!', 'polyglot'), 1, 1, [
                'notify' => [
                    'type' => 'alert-warning',
                    'icon' => 'svg-rmcommon-close'
                ]
            ]
        );
    }

    $languages = json_decode(file_get_contents($file), true);

    if (!array_key_exists($code, $languages)) {
        $common->ajax()->response(
            __('Specified language does not exists in Polyglot!', 'polyglot'), 1, 1
        );
    }

    return $languages;
}

/**
 * Receive data from Polyglot
 */
$action = $common->httpRequest()->post('action', 'string', '');

if ('add-language' == $action) {

    $common->ajax()->prepare();

    if (!$xoopsSecurity->check(true, false, 'CUTOKEN')) {
        $common->ajax()->response(
            __('You have not authorization to add languages!', 'polyglot'), 1, 0, ['reload' => 1]
        );
    }

    // Get data and verify that is complete
    $name = $common->httpRequest()->post('name', 'string', '');
    $code = $common->httpRequest()->post('code', 'string', '');
    $country = $common->httpRequest()->post('country', 'string', '');
    $file = $common->httpRequest()->post('file', 'string', '');
    $directory = $common->httpRequest()->post('directory', 'string', '');

    if ('' == $name || '' == $code || '' == $country || '' == $file || '' == $directory) {
        $common->ajax()->response(
            __('Please fill all fields in order to add a new language!', 'polyglot'), 1, 1, [
                'notify' => [
                    'icon' => 'svg-rmcommon-error',
                    'type' => 'alert-red'
                ]
            ]
        );
    }

    // Get languages from file
    $languages = json_decode(file_get_contents($this->file), true);

    if (!$languages || !is_array($languages)) {
        $languages = [];
    }

    if (array_key_exists($code, $languages)) {
        $common->ajax()->response(
            sprintf(__('The language "%s" already exists!', 'polyglot'), $name), 1, 1, [
                'notify' => [
                    'icon' => 'svg-rmcommon-warning',
                    'type' => 'alert-warning'
                ]
            ]
        );
    }

    $languages[$code] = [
        'code' => $code,
        'name' => $name,
        'country' => $country,
        'file' => $file,
        'directory' => $directory,
        'status' => 'enabled',
        'charset' => 'UTF-8'
    ];

    // Add a new table for language
    if (!$this->addLanguageTable($code)) {
        $common->ajax()->response(
            sprintf(__('The language "%s" could not be added: %s', 'polyglot'), $this->errors()), 1, 1, [
                'notify' => [
                    'icon' => 'svg-rmcommon-warning',
                    'type' => 'alert-warning'
                ]
            ]
        );
    }

    // Save file
    file_put_contents($this->file, json_encode($languages));

    $common->ajax()->response(
        sprintf(__('The language "%s" was added successfully!', 'polyglot'), $name), 0, 1, [
            'notify' => [
                'type' => 'alert-success',
                'icon' => 'svg-rmcommon-ok-circle'
            ],
            'language' => $languages[$code]
        ]
    );

}

/*
 * Set a specified language as base language
 * -----------------------------------------
 */
if ('set-base' == $action) {

    $common->ajax()->prepare();

    if (!$xoopsSecurity->check(true, false, 'CUTOKEN')) {
        $common->ajax()->response(
            __('You have not authorization for this action!', 'polyglot'), 1, 0, ['reload' => 1]
        );
    }

    $lang = $common->httpRequest()->post('lang', 'string', '');

    // Get languages from file
    $languages = json_decode(file_get_contents($this->file), true);

    if (!is_array($languages) || empty($languages)) {
        $common->ajax()->response(
            __('There are not languages registered in Polyglot!', 'polyglot'), 1, 1
        );
    }

    if (!array_key_exists($lang, $languages)) {
        $common->ajax()->response(
            __('Specified language does not exists in Polyglot!', 'polyglot'), 1, 1
        );
    }

    $languages[$lang]['type'] = 'base';

    file_put_contents($this->file, json_encode($languages));

    $common->ajax()->response(
        sprintf(__('Language "%s" was set as base language', 'polyglot'), $language[$lang]['name']), 0, 1, [
            'notify' => [
                'icon' => 'svg-rmcommon-ok',
                'type' => 'alert-success'
            ]
        ]
    );

}

/*
 * Save changes of a edited language
 * ---------------------------------
 */
if ('save-changes' == $action) {

    $common->ajax()->prepare();

    if (!$xoopsSecurity->check(true, false, 'CUTOKEN')) {
        $common->ajax()->response(
            __('You have not authorization for this action!', 'polyglot'), 1, 0, ['reload' => 1]
        );
    }

    $old = $common->httpRequest()->post('old', 'string', '');
    $code = $common->httpRequest()->post('code', 'string', '');
    $name = $common->httpRequest()->post('name', 'string', '');
    $country = $common->httpRequest()->post('country', 'string', '');
    $file = $common->httpRequest()->post('file', 'string', '');
    $directory = $common->httpRequest()->post('directory', 'string', '');

    // Check data
    if ('' == $old || '' == $code || '' == $name || '' == $country || '' == $file || '' == $directory) {
        $common->ajax()->response(
            __('You must provide all fields to modify this language. Please try again.', 'polyglot'), 1, 1, [
                'notify' => [
                    'icon' => 'svg-rmcommon-alert',
                    'type' => 'alert-warning'
                ]
            ]
        );
    }

    $languages = json_decode(file_get_contents($this->file), true);

    // Check if another language with same name already exists
    if ($old != $code && array_key_exists($code, $languages)) {
        $common->ajax()->response(
            __('Another language with same code already exists!', 'polyglot'), 1, 1, [
                'notify' => [
                    'icon' => 'svg-rmcommon-error',
                    'type' => 'alert-danger'
                ]
            ]
        );
    }

    if ($old != $code && array_key_exists($old, $languages)) {
        $languages[$code] = $languages[$old];
        unset($languages[$old]);
    }

    $languages[$code]['name'] = $name;
    $languages[$code]['code'] = $code;
    $languages[$code]['country'] = $country;
    $languages[$code]['file'] = $file;
    $languages[$code]['directory'] = $directory;

    // Save file
    file_put_contents($this->file, json_encode($languages));

    showMessage(__('Language updated successfully!', 'polyglot'), RMMSG_SUCCESS);

    $common->ajax()->response(
        __('Language updated successfully!', 'polyglot'), 0, 1
    );

}

/*
 * Delete languages from Polyglot
 * ------------------------------
 */
if ('delete-language' == $action) {
    $common->ajax()->prepare();

    $code = $common->httpRequest()->post('code', 'string', '');

    $languages = polyglot_check_code($code, $this->file);

    // Delete tables
    $tableS = $common->db()->prefix("plugin_polyglot_{$code}_shorts");
    $tableL = $common->db()->prefix("plugin_polyglot_{$code}_longs");

    if (!$common->db()->queryF("DROP TABLE $tableS")) {
        $common->ajax()->response(
            __('Language table could not be deleted:', 'polyglot') . $xoops->db()->error(), 1, 1, [
                'notify' => [
                    'type' => 'alert-danger',
                    'icon' => 'svg-rmcommon-database'
                ]
            ]
        );
    }

    if (!$common->db()->queryF("DROP TABLE $tableL")) {
        $common->ajax()->response(
            __('Language table could not be deleted:', 'polyglot') . $xoops->db()->error(), 1, 1, [
                'notify' => [
                    'type' => 'alert-danger',
                    'icon' => 'svg-rmcommon-database'
                ]
            ]
        );
    }

    // Delete language from file
    $name = $languages[$code]['name'];
    unset($languages[$code]);

    file_put_contents($this->file, json_encode($languages));

    showMessage(sprintf(__('Language %s deleted successfully!', 'polyglot'), '<strong>' . $name . '</strong>'), RMMSG_SUCCESS);

    $common->ajax()->response(
        sprintf(__('Language %s deleted successfully!', 'polyglot'), '<strong>' . $name . '</strong>'), 0, 1
    );

}

/**
 * Disable/Enable a language
 */
if ('enable-language' == $action) {
    $common->ajax()->prepare();

    $code = $common->httpRequest()->post('code', 'string', '');

    $languages = polyglot_check_code($code, $this->file);

    $status = array_key_exists('status', $languages[$code]) ? $languages[$code]['status'] : 'disabled';

    $newStatus = 'enabled' == $status ? 'disabled' : 'enabled';

    $languages[$code]['status'] = $newStatus;

    file_put_contents($this->file, json_encode($languages));

    showMessage(
        sprintf(__('Status for language %s was changed to %s', 'polyglot'), '<strong>' . $languages[$code]['name'] . '</strong>', '<strong>' . $newStatus . '</strong>'),
        RMMSG_SUCCESS
    );

    $common->ajax()->response(
        'Status updated', 0, 1
    );

}

$common->template()->add_attribute('html', [
    'class' => 'polyglot-dashboard'
]);

$common->template()->add_style('polyglot-dashboard.min.css', 'rmcommon', [
    'directory' => 'plugins/polyglot',
    'id' => 'polyglot-ds-css'
]);

// Language
include $common->path('plugins/polyglot/js/polyglot-lang.php');

$common->template()->add_script('polyglot-dashboard.min.js', 'rmcommon', [
    'directory' => 'plugins/polyglot',
    'footer' => 1,
    'id' => 'polyglot-js'
]);

$common->breadcrumb()->add_crumb(__('Plugins Manager', 'polyglot'), 'plugins.php');
$common->breadcrumb()->add_crumb(__('Polyglot', 'polyglot'));

$languages = json_decode(file_get_contents($this->file), true);
$baseAssigned = false;

foreach ($languages as $lang) {
    if (array_key_exists('type', $lang) && $lang['type'] == 'base') {
        $baseAssigned = true;
        break;
    }
}

$common->template()->header();

$common->template()->assign('languages', $languages);
$common->template()->assign('baseAssigned', $baseAssigned);
$common->template()->assign('plugin', $this);
$common->template()->assign('page', 'dashboard');
$common->template()->assign('file', 'polyglot-languages.php');


$common->template()->display('polyglot-dashboard.php', 'plugin', 'rmcommon', 'polyglot');

$common->template()->footer();
