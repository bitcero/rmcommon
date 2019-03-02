<?php
/**
 * Common Utilities Framework for Xoops
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

namespace Common\Widgets;

use Common\Core\Helpers\WidgetAbstract;
use Common\Core\Helpers\WidgetInterface;

/**
 * Class TileBox
 *
 * Show a tile box.
 * <pre>
 * $tileBox = $common->widgets()->load('rmcommon', 'TileBox');
 * $tileBox->setup([
 *     'type' => 'counter', //Can be counter or user
 *     'style' => 'default|icon-right|icon-left|progress', // only available for style counter
 *     'color' => 'blue|orange|primary|etc',
 *     'user' => 'int|object', // only available when type is user
 *     'caption' => 'text',
 *     'solid' => bool,
 *     'icon' => string, // available when style is counter
 *     'progressValue' => int // only available when style is progress,
 *     'link' => string // when provided, a button will show
 * ]);
 * </pre>
 *
 * @package Common\Widgets
 */
class TileBox extends WidgetAbstract implements WidgetInterface
{
    private $tplPath = '';
    private $counters = [];

    public function __construct()
    {
        $this->suppressList = ['type', 'solid', 'style', 'color', 'user', 'caption', 'counter', 'icon', 'footer', 'progressValue'];
    }

    public function setup($data = [])
    {
        global $common;

        $defaults = [
            'type' => 'counter',
            'style' => 'default',
            'color' => '',
            'caption' => '',
            'solid' => false,
            'footer' => '',
            'link' => ''
        ];

        $data = array_merge($defaults, $data);

        parent::__construct($data);
        $this->add('class', 'widget-tile-box');

        if ($data['type'] == 'user') {
            $this->add('class', 'user');

            if (!is_a($data['user'], 'XoopsUser') && (int)$data['user'] <= 0) {
                return false;
            }

            if (is_a($data['user'], 'XoopsUser')) {
                $common->template()->assign('user', $data['user']);
            } else {
                $user = new \XoopsUser($data['user']);
                $common->template()->assign('user', $user);
            }
        } else {
            $this->add('class', 'counter');
            $this->add('class', $data['style'] != '' ? ($data['style'] == 'progress' ? 'with-progress' : $data['style']) : 'default');
        }

        if (true == $data['solid'] && 'progress' != $data['style']) {
            $this->add('class', 'solid bg-' . $data['color']);
        }

        $common->template()->assign('settings', (object) $data);
    }

    /**
     * Unique internal ID for this widget
     * @return string
     */
    public function id()
    {
        return __CLASS__;
    }

    /**
     * Gets the HTML code for this widget
     * @return string
     */
    public function getHtml()
    {
        global $common;

        $common->template()->assign('attributes', $this->renderAttributeString());

        return \RMTemplate::getInstance()->render($this->template());
    }

    /**
     * Get the template path used in this widget
     * @return string
     */
    public function template()
    {
        if ('' == $this->tplPath) {
            $this->tplPath = \RMTemplate::getInstance()->path('widgets/widget-tilebox.php', 'module', 'rmcommon');
        }
        return $this->tplPath;
    }
}
