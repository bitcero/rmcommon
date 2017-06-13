<?php
/**
 * Common Utilities Framework for XOOPS
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

namespace Common\Widgets;

use Common\Core\Helpers\WidgetAbstract;
use Common\Core\Helpers\WidgetInterface;

class UserCard extends WidgetAbstract implements WidgetInterface
{
    private $tplPath = '';

    public function __construct()
    {
        $this->suppressList = [
            'type',
            'image',
            'name',
            'link',
            'charge',
            'user',
            'mainButton',
            'color',
            'counters',
            'info',
            'social',
            'headerBg',
            'headerGradient'
        ];
    }

    /**
     * @return mixed
     */
    public function id()
    {
        return __CLASS__;
    }

    /**
     * @param mixed $parameters
     * @return mixed
     */
    public function setup($parameters)
    {
        global $common;

        $defaults = [
            'type' => 'large',
            'name' => '',
            'email' => '',
            'user' => null,
            'link' => '',
            'carge' => '',
            'mainButton' => [],
            'color' => 'primary',
            'counters' => [],
            'info' => '',
            'social' => [],
            'highlight' => '',
            'headerBg' => '',
            'headerGradient' => false,
        ];

        $data = array_merge($defaults, $parameters);

        parent::__construct($parameters);
        $this->add('class', 'widget-user-card');
        $this->add('class', 'card-' . $data['type']);

        // Get user data
        if (is_a($data['user'], 'XoopsUser')) {
            $data['name'] = $data['user']->getVar('name') == '' ? $data['user']->getVar('name') : $data['user']->getVar('uname');
            $data['email'] = $data['user']->email;
        } elseif ($data['user'] > 0) {
            $xUser = new XoopsUser($data['user']);
            $data['name'] = $xUser->getVar('name') == '' ? $xUser->getVar('name') : $xUser->getVar('uname');
            $data['email'] = $xUser->email;
        }

        if (false == array_key_exists('name', $data) || '' == $data['name']) {
            throw new \RMException(__('the widget UserCard requires the user name', 'rmcommon'));
        }

        // Avatar
        if ('' == $data['image']) {
            if ('' != $data['email'] && checkEmail($data['email'])) {
                $data['image'] = $common->services()->service('avatar')->getAvatarSrc($data['email'], 200);
            } else {
                throw new \RMException(__('the widget UserCard requires the user email', 'rmcommon'));
            }
        }

        $common->template()->assign('widgetSettings', (object)$data);
    }

    /**
     * @return mixed
     */
    public function template()
    {
        if ('' == $this->tplPath) {
            $this->tplPath = \RMTemplate::getInstance()->path('widgets/widget-usercard.php', 'module', 'rmcommon');
        }
        return $this->tplPath;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        global $common;

        $common->template()->assign('attributes', $this->renderAttributeString());

        return \RMTemplate::getInstance()->render($this->template());
    }


}