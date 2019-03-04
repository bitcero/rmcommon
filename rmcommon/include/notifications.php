<?php
/**
 * Common Utilities Notifications Interface
 * Notifications subscription for modules, themes and plugins
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
if ('cu-notification-subscribe' == $page) {
    // Process subscriptions

    class Response
    {
        use RMModuleAjax;
    }

    $response = new Response();
    $response->prepare_ajax_response();

    $result = RMNotifications::get()->subscribe();

    if (false === $result) {
        $response->ajax_response(
            __('Sorry, your request could not be processed.', 'rmcommon'),
            1,
            0
        );
    }

    $message = 'subscribed' == $result->status ? __('Subscribed', 'rmcommon') : __('Subscription cancelled', 'rmcommon');

    $response->ajax_response(
        $message,
        0,
        0,
        $event
    );
} elseif ('cu-notification-list' == $page) {
    // Show subscriptions list

    global $xoopsUser, $cuSettings;

    if (!$xoopsUser) {
        RMUris::redirect_with_message(
            __('You need to register/login to view this page.', 'rmcommon'),
            XOOPS_URL,
            RMMSG_WARN
        );
    }

    $subscriptions = RMNotifications::get()->subscriptions();

    if (empty($subscriptions)) {
        RMUris::redirect_with_message(
            __('You are not subscribed to any notification event.', 'rmcommon'),
            XOOPS_URL,
            RMMSG_WARN
        );
    }

    $elements = [];
    $items = [];
    $tf = new RMTimeFormatter(0, __('%M% %d%, %Y%', 'rmcommon'));
    $crypt = new Crypt(null, $cuSettings->secretkey);

    foreach ($subscriptions as $item) {
        $class = ucfirst($item->element . '_Notifications');
        if (!class_exists($class)) {
            // Include controller file
            if ('plugin' == $event->type) {
                $file = XOOPS_ROOT_PATH . '/modules/rmcommon/plugins/' . $item->element . '/class/' . mb_strtolower($item->element) . '.notifications.class.php';
            } elseif ('theme' == $event->type) {
                $file = XOOPS_ROOT_PATH . '/themes/' . $item->element . '/class/' . mb_strtolower($item->element) . '.notifications.class.php';
            } else {
                $file = XOOPS_ROOT_PATH . '/modules/' . $item->element . '/class/' . mb_strtolower($item->element) . '.notifications.class.php';
            }

            require_once $file;
            if (!class_exists($class)) {
                continue;
            }
        }
        $notifications = $class::get();

        if (!$notifications->is_valid($item->event)) {
            continue;
        }

        $event = $notifications->event($item->event);

        if (!array_key_exists($item->type . '_' . $item->element, $elements)) {
            $elements[$item->type . '_' . $item->element] = $notifications->element_data();
        }

        $items[$item->type . '_' . $item->element][] = [
            'caption' => $event->caption,
            'element' => $elements[$item->type . '_' . $item->element],
            'params' => $item->params,
            'type' => $item->type,
            'event' => $item->event,
            'object' => $notifications->object_data($item),
            'date' => $tf->format($item->date),
            'hash' => $crypt->encrypt(json_encode([
                'event' => $item->event,
                'element' => $item->element,
                'type' => $item->type,
                'params' => $item->params,
            ])),
        ];
    }

    RMTemplate::get()->add_script('cu-handler.js', 'rmcommon', ['footer' => 1, 'id' => 'cuhandler']);

    RMTemplate::get()->header();

    include RMTemplate::get()->get_template('rmc-notifications-list.php', 'module', 'rmcommon');

    RMTemplate::get()->footer();

    exit();
}
