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

use Common\Core\Helpers\Widget;
use Common\Core\Helpers\WidgetAbstract;
use Common\Core\Helpers\WidgetInterface;

/**
 * This file contains a widget useful to show a counter with small information
 * and styled in different colors
 */
class Counter extends WidgetAbstract implements WidgetInterface
{
    private $tplPath = '';
    private $cells = [];

    public function __construct($data = [])
    {

        $this->suppressList = ['color', 'icon', 'format'];

        if(false === empty($data)){

            parent::__construct($data);

            if(!$this->has('format')){
                $this->set('format', 'y');
            }

        }
    }

    public function setup($data = [])
    {
        parent::__construct($data);

        if(!$this->has('format')){
            $this->set('format', 'y');
        }
    }

    /**
     * Unique internal ID for this widget
     * @return string
     */
    public function id()
    {
        return 'CUWidgetCounter';
    }

    /**
     * Gets the HTML code for this widget
     * @return string
     */
    public function getHtml()
    {
        global $cuIcons;

        $this->add('class', 'widget-counter');

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
        $this->tpl->assign('cells', $this->cells);

        // Calculate cells


        return \RMTemplate::getInstance()->render($this->template());
    }

    /**
     * Get the template path used in this widget
     * @return string
     */
    public function template()
    {
        if('' == $this->tplPath){
            $this->tplPath = \RMTemplate::getInstance()->path('widgets/widget-counter.php', 'module', 'rmcommon');
        }
        return $this->tplPath;
    }

    /**
     * Add a cell of information to the counter.
     * You can add up to 4 rows for this widget
     *
     * @param $caption
     * @param $value
     * @return bool
     * @throws \RMException
     */
    public function addCell($caption, $value){
        if('' == $caption || '' == $value){
            trigger_error(__('You must provide the caption and value when add a new cell to Counter Widget', 'rmcommon'));
            return false;
        }

        if(count($this->cells) >= 4){
            trigger_error(__('It is not possible to add more than 4 cells to this widget', 'rmcommon'));
            return false;
        }

        /**
         * Check if value is a number and if it must be formatted.
         * Remember to assign attribute 'format' to enable this.
         */
        $format = $this->has('format') && $this->get('format') == 'y';

        if(is_integer($value) || is_float($value) && $format){

            // Format for numbers
            if($value >= 1000000){
                // Millions
                $reduced = $value / 1000000;
                if(floor($reduced) < $reduced){
                    $value = floor($reduced) . 'M+';
                } else {
                    $value = $reduced . 'M';
                }
            } elseif ($value >= 10000){
                $reduced = $value / 1000;
                if(floor($reduced) < $reduced){
                    $value = floor($reduced) . 'k+';
                } else {
                    $value = $reduced . 'k';
                }
            } elseif ($value > 1000) {
                $value = number_format($value / 1000, 1) . 'k';
            }

        } elseif(is_numeric($value)) {

            $value = number_format($value, 2);
            if((int)$value == $value){
                $value = (int)$value;
            }

        }

        $this->cells[] = (object) ['caption' => $caption, 'value' => $value];
        return true;
    }
}
