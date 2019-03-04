<?php
/**
 * Common Utilities for XOOPS
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

namespace Common\API;

class CategoriesComponent
{
    private $owner = '';
    private $key = '';
    private $category = null;

    /**
     * Constructs the categories component using the
     * owner (generally the module dirname) as main identifier
     * and the key as specific identifier.
     *
     * @param string $owner
     * @param string $key
     * @param mixed $id
     */
    public function __construct($owner, $key, $id = 0)
    {
        if ('' == $owner || '' == $key) {
            throw new \RMException(__('Categories component requires from a owner and key.', 'rmcommon'));
        }

        $this->owner = $owner;
        $this->key = $key;

        if ($id > 0) {
            $this->category = new Category($id);
            if ($this->category->isNew()) {
                throw new \RMException(__('Category does not exists', 'rmcommon'));
            }
        }
    }

    /**
     * Render the HTML form and return to client.
     * This method uses RMTemplate to get the template path.
     *
     * @return string;
     */
    public function renderForm()
    {
        $form = $this->template()->path('api/rmc-categories-form.php', 'module', 'rmcommon');

        return $form;
    }
}
