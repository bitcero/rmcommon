<?php
/**
 * Common Utilities
 * Errors container for RM Objects
 *
 * Copyright © 2015 Eduardo Cortés
 * -----------------------------------------------------------------
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * -----------------------------------------------------------------
 * @package      rmcommon
 * @author       Eduardo Cortés
 * @copyright    Eduardo Cortés
 * @license      GPL 2
 * @link         http://eduardocortes.mx
 * @link         http://rmcommon.com
 */
trait RMErrors
{
    private $errors = [];

    protected function add_error($error)
    {
        $this->errors[] = $error;
    }

    public function errors($text = true)
    {
        if ($text) {
            return implode('<br> ', $this->errors);
        }

        return $this->errors;
    }
}
