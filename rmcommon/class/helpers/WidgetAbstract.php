<?php
/**
 * Common Utilities framework for Xoops
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
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

abstract class WidgetAbstract extends Attributes
{
    /**
     * Attributes to be ignored
     * @var array
     */
    protected $suppressList = [];

    protected $tpl;

    public function __construct($data)
    {
        parent::__construct($data);

        $this->tpl = \RMTemplate::getInstance();
    }

    /**
     * Create a valid HTML string with widget attributes
     * @return string
     */
    public function renderAttributeString()
    {
        $this->suppressRender($this->suppressList);

        // generate id from name if not already set
        return parent::renderAttributeString();
    }

    public function display()
    {
        echo $this->getHTML();
    }
}
