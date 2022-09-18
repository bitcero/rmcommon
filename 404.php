<?php
/**
 * Common Utilities by Red Mexico
 * A framework and new GUI for XOOPS modules
 *
 * Copyright © 2014 Eduardo Cortés
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
 * @package      Common Utilities
 * @author       Eduardo Cortés <yo@eduardocortes.mx>
 * @copyright    2009 - 2014 Eduardo Cortés
 * @license      GPL v2
 * @link         https://eduardocortes.mx
 */
if (!isset($xoopsOption['module_subpage'])) {
    $xoopsOption['module_subpage'] = 'error404';
}

require dirname(__DIR__) . '/../mainfile.php';

RMTemplate::get()->header();

include RMTemplate::get()->get_template('404.php', 'module', 'rmcommon');

RMTemplate::get()->footer();
