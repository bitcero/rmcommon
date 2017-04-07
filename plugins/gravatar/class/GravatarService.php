<?php

/**
 * Gravatar plugin for Common Utilities
 *
 * Copyright © 2015 Red Mexico http://www.redmexico.com.mx
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
 * @copyright    Red Mexico (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @subpackage   gravatar
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */


class GravatarService extends \Common\Core\Helpers\ServiceAbstract implements \Common\Core\Helpers\ServiceInterface
{

    /**
     * Retrieves the url pointing to an user avatar
     *
     * This method acceptas a main parameter in multiple formats:
     *
     * Using XoopsUser object:
     * <pre>
     * $src = $rmServices->avatar->getSrc($xoopsUser, 120);
     * </pre>
     *
     * Using RMUser objetc:
     * <pre>
     * $rmUser = new RMUser(1);
     * $src = $rmServices->avatar->getSrc($rmUser, 120);
     * </pre>
     *
     * Using stdClass object:
     * <pre>
     * $user = new stdClass();
     * $user->email = 'my@email.com';
     * $src = $rmServices->avatar->getSrc($user, 120);
     * </pre>
     *
     * Using the user email as parameter (most common):
     * <pre>
     * $src = $rmServices->avatar->getSrc('my@email.com', 120);
     * </pre>
     *
     * @param mixed $user_or_email
     * @param int $size
     * @param string $default
     * @return string
     */
    public function getAvatarSrc($user_or_email, $size = 80, $default = '')
    {

        if (is_object($user_or_email)) {

            if (is_a($user_or_email, 'XoopsUser')) {
                $email = $user_or_email->getVar('email');
            } elseif (is_a($user_or_email, 'RMUser')) {
                $email = $user_or_email->email;
            } else {
                $email = $user_or_email->email;
            }

        } else {
            $email = $user_or_email;
        }

        $config = RMSettings::plugin_settings('gravatar', true);

        $size = $size <= 0 ? $size = $config->size : $size;
        $default = $default == '' ? $config->default : $default;

        $avatar = "//www.gravatar.com/avatar/" . md5($email) . "?s=" . $size . '&d=' . $default;

        return $avatar;

    }

    /**
     * Retrieves an avatar grom GRavatar.com and return a HTML image.
     *
     * This method is very similar to {@link getSrc()} method but with
     * small changes:
     *
     * <code>$user_or_email</code> accepts object or email. But when an
     * objet is provided (XoopsUser, RMuser or stdClass) this function takes
     * the name and the uname properties also.
     *
     * @param $user_or_email
     * @param int $size
     * @param string $default
     * @return string
     */
    public function getAvatar($user_or_email, $size = 80, $default = '')
    {

        if (is_object($user_or_email)) {

            if (is_a($user_or_email, 'XoopsUser')) {
                $name = $user_or_email->getVar('name');
                $uname = $user_or_email->getVar('uname');
            } elseif (is_a($user_or_email, 'RMUser')) {
                $name = $user_or_email->name;
                $uname = $user_or_email->uname;
            } else {
                $name = $user_or_email->name;
                $uname = $user_or_email->uname;
            }

        }

        $avatar = '<img src="' . $this->getAvatarSrc($user_or_email, $size, $default) . '" alt="' . ($name != '' ? $name : $uname) . '">';
        return $avatar;

    }

    /**
     * Singleton method
     */
    public static function getInstance()
    {
        static $instance;

        if (isset($instance))
            return $instance;

        $instance = new GravatarService();

        return $instance;
    }

}