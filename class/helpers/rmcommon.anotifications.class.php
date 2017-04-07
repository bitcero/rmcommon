<?php
/**
 * Common Utilities Notifications Interface
 * A helper class that allows to integrate notifications with modules and plugins
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

abstract class Rmcommon_ANotifications
{
    protected $events;
    /**
     * Determines if an event exists in the module and if the user have permissions to subscribe
     * @param string $event Evetn name
     * @return bool
     */
    public abstract function is_valid($event);

    /**
     * Indicates if the notification email uses HTML or not
     * @return bool
     */
    public abstract function use_html();

    /**
     * Returns the string to be used in From Name email field
     * @return string
     */
    public abstract function from_name();

    /**
     * Generate the subject for a notification event
     *
     * @param string $event Event identifier
     *
     * @return mixed
     */
    public abstract function subject($event, $params);

    /**
     * Creates the body for notification message
     *
     * @param array $params
     *
     * @return mixed
     */
    public abstract function body($event, $params);

    /**
     * Must return an array with element data
     * @return array
     */
    public abstract function element_data();

    /**
     * Must return an array with object data
     * @return array
     */
    public abstract function object_data($event);

    /**
     * Get an event object
     * @param string $name Event name
     * @return bool|RMNotificationItem
     */
    public function event( $name ){

        if ( !array_key_exists( $name, $this->events ) )
            return false;

        $event = new RMNotificationItem( $this->events[$name] );
        return $event;

    }

    /**
     * Set permissions for specific event
     * @param string $event Event index name (generally event name)
     * @param array $permissions Array with users and groups indexes
     * @return bool
     *
    public function permissions( $event, $permissions ){

        if ( !array_key_exists( $event, $this->events ) )
            return false;

        if ( !is_array( $permissions ) )
            return false;

        $this->events[$event]['permissions'] = $permissions;
        return $this;

    }

    /**
     * Set parameters for specific event
     * @param string $event Event index name (generally event name)
     * @param string $id The parameter identifier
     * @return bool
     *
    public function parameters( $event, $params ){
        if ( !array_key_exists( $event, $this->events ) )
            return false;

        $this->events[$event]['params'] = $params;
        return $this;
    }

    /**
     * Return the event array
     * @param string $event Event index name
     * @return array
     *
    public function event( $event ){

        if ( !array_key_exists( $event, $this->events ) )
            return false;

        return $this->events[$event];

    }*/


}