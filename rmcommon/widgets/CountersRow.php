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
 * This file contains a widget useful to show a counter with small information
 * and styled in different colors
 */
class CountersRow extends WidgetAbstract implements WidgetInterface
{
    private $tplPath = '';
    private $counters = [];

    public function __construct()
    {
        $this->suppressList = ['type'];
    }

    public function setup($data = [])
    {
        parent::__construct($data);
    }

    /**
     * Unique internal ID for this widget
     * @return string
     */
    public function id()
    {
        return __CLASS__;
    }

    public function addCounter($data = []){
        if(empty($data)){
            return false;
        }

        if(is_numeric($data['counter'])){
            if($data['counter'] >= 1000000){
                // Millions
                $reduced = $data['counter'] / 1000000;
                if(floor($reduced) < $reduced){
                    $data['counter'] = floor($reduced) . '<small>M+</small>';
                } else {
                    $data['counter'] = $reduced . '<small>M</small>';
                }
            } elseif ($data['counter'] >= 10000){
                $reduced = $data['counter'] / 1000;
                if(floor($reduced) < $reduced){
                    $data['counter'] = floor($reduced) . '<small>k+</small>';
                } else {
                    $data['counter'] = $reduced . '<small>k</small>';
                }
            } elseif ($data['counter'] > 1000) {
                $data['counter'] = number_format($data['counter'] / 1000, 1) . '<small>k</small>';
            }
        }
        
        $this->counters[] = (object) $data;
    }

    /**
     * Gets the HTML code for this widget
     * @return string
     */
    public function getHtml()
    {
        global $cuIcons;

        $this->add('class', 'row');

        // Widget color
        if($this->has('color')){
            $this->add('class', 'bg-' . $this->get('color'));
        }

        // Widget icon
        if( $this->has('icon') ){
            $this->tpl->assign('icon', $cuIcons->getIcon($this->get('icon')));
        } else {
            $this->tpl->assign('icon', $cuIcons->getIcon('svg-rmcommon-ok-circle'));
        }

        $this->tpl->assign('attributes', $this->renderAttributeString());
        $this->tpl->assign('caption', $this->get('caption'));
        $this->tpl->assign('counters', $this->counters);
        $this->tpl->assign('type', $this->get('type'));

        return \RMTemplate::getInstance()->render($this->template());
    }

    /**
     * Get the template path used in this widget
     * @return string
     */
    public function template()
    {
        if('' == $this->tplPath){
            $this->tplPath = \RMTemplate::getInstance()->path('widgets/widget-row-counters.php', 'module', 'rmcommon');
        }
        return $this->tplPath;
    }


}