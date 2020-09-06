<?php
/**
 * Advanced Form Fields for Common Utilities
 *
 * Copyright © 2015 - 2017 Eduardo Cortés http://www.eduardocortes.mx
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
 * @package      advform
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Plugins\AdvForm;

/**
 * This field presents an input to select and search users
 */
class AdvancedUsers extends \RMFormElement
{
    /**
     * AdvancedUsers constructor.
     *
     * <pre>
     * $users = new Common\Plugin\AdvForm\AdvancedUsers([
     *     'name' => 'Name of element',
     *     'id' => 'Id of element',
     *     'multiple' => null,
     *     'key' => 'Name of column fields that will be returned,
     *     'select' => array with values that will be selected
     * ]);
     *
     * @param array $options
     */
    public function __construct($options)
    {
        if(array_key_exists('key', $options)){
            $options['data-key'] = $options['key'];
            unset($options['key']);
        }

        parent::__construct($options);

        $this->setIfNotSet('name', 'name_error');
        $this->setIfNotSet('id', \TextCleaner::sweetstring($this->get('name')));
        $this->setIfNotSet('data-key', 'uid');
        $this->setIfNotSet('class', 'form-control');
        $this->set('data-advf-field', 'users-field');

        $this->suppressList = ['key', 'multi', 'selected', 'form', 'caption'];
    }

    public function render()
    {
        global $common;

        $selected = $this->get('selected');
        $attributes = $this->renderAttributeString();

        $common->template()->add_script('advanced-fields.min.js', 'rmcommon', [
            'id' => 'advform-js',
            'directory' => 'plugins/advform-pro',
            'footer' => 1
        ]);

        $common->template()->assign('attributes', $attributes);
        $common->template()->assign('selected', $selected);

        return $common->template()->render('user-select.php', 'plugin', 'rmcommon', 'advform-pro');

    }
}