<?php
/**
 * Common Utilities Framework
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

namespace Common\Core\Helpers;

class Help
{
    private $urls;
    private $baseURL = '';

    public function __construct($directory){

        if('' == $directory){
            throw new \RMException(__("Directory {$directory} with help docs does not exists", 'rmcommon'));
        }

        $path = XOOPS_ROOT_PATH . '/modules/' . $directory . '/include/help.php';

        if(false == is_file($path)){
            throw new \RMException(__("File with docs URL does not exists", 'rmcommon'));
        }

        $this->urls = include($path);

        if(empty($this->urls)){
            throw new \RMException(__("File with docs URL is empty", 'rmcommon'));
        }

        $this->urls = (object) $this->urls;
        $this->baseURL = $this->urls->base;

    }

    public function get($id){
        if('' == $id){
            throw new \RMException(__("You must provide an ID for help item", 'rmcommon'));
        }

        if(isset($this->urls->{$id})){
            return $this->baseURL . $this->urls->{$id};
        } else {
            return '';
        }
    }
}