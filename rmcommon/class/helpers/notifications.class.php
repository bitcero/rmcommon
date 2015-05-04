<?php
/**
 * Common Utilities Notifications Helper
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

/**
 * Class RMNotifications
 * Allows to integrate notifications with modules and plugins
 */
class RMNotifications
{
    use RMSingleton, RMModuleAjax, RMErrors;

    /**
     * Stores all notification items
     * @var array
     */
    private $items = array();

    /**
     * Counter for notifications forms
     * @var int
     */
    static private $index = 0;

    /**
     * Adds a new item to notifications list
     * @param RMNotificationItem $item
     * @return bool
     */
    public function add_item( RMNotificationItem $item ){

        if ( $item->isNew() )
            return false;

        // All parameters are required
        if ( '' == $item->event || '' == $item->element || '' == $item->params )
            return false;

        $this->items[] = $item;

        return true;

    }

    /**
     * This function displays the notifications options to be selected by user
     * @return string
     */
    public function render(){
        global $xoopsUser, $cuSettings;

        if ( !$xoopsUser )
            return null;

        $items = array();
        $user_groups = $xoopsUser->getGroups();
        $crypt = new Crypt(null,$cuSettings->secretkey);
        $subscriptions = $this->subscriptions();

        // Check permissions
        foreach ( $this->items as $item ){

            $item->type = $item->type != '' ? $item->type : 'module';
            $item->hash = $crypt->encrypt(json_encode(array(
                'event' => $item->event,
                'element' => $item->element,
                'type' => $item->type,
                'params' => $item->params
            )));

            // Check if users is subscribed to current event
            $id = hash('crc32', $item->event . ':' . $item->element . ':' . $item->type . ':' . $item->params);
            if ( array_key_exists( $id, $subscriptions ) )
                $item->subscribed = true;

            if ( !is_array( $item->permissions ) || count( $item->permissions ) <= 0 ){
                $items[] = $item->data();
                continue;
            }

            // Check if users were provided and current user is allowed
            if ( array_key_exists('users', $item->permissions ) && count( $item->permissions['users'] ) > 0 ){

                if ( in_array( $xoopsUser->uid(), $item->permissions['users']) ) {
                    $items[] = $item->data();
                    continue;
                }

            }

            if ( count( $item->permissions['groups'] ) <= 0 ){
                $items[] = $item->data();
                continue;
            }

            // Check if groups were provided and current user group is allowed
            if ( array_key_exists( 'groups', $item->permissions ) && count( $item->permissions['groups'] ) > 0 ){

                $intersect = array_intersect( $item->permissions['groups'], $user_groups );

                if ( !empty( $intersect ) ){
                    $items[] = $item->data();
                    continue;
                }

            }

        }

        $this->items = array();

        if ( empty( $items ) )
            return null;

        RMTemplate::get()->add_script('cu-handler.js', 'rmcommon', array('footer' => 1));

        ob_start();
        include RMTemplate::get()->get_template('rmc-notifications-options.php', 'module', 'rmcommon');
        $template = ob_get_clean();

        // Clear notifications items
        $this::$index++;

        return $template;
    }

    /**
     * Process subscription to an event by getting url parameters
     */
    public function subscribe(){
        global $cuSettings, $xoopsUser;

        $this->prepare_ajax_response();

        $event = RMHttpRequest::post( 'event', 'string', '' );
        $status = RMHttpRequest::post( 'status', 'integer', 1 );

        if ( !$xoopsUser ) {
            $this->add_error( __('No user has been specified', 'rmcommon') );
            return false;
        }

        if ( '' == $event ){
            $this->add_error( __('No event name has been specified', 'rmcommon') );
            return false;
        }

        include_once RMCPATH . '/class/crypt.php';
        $crypt = new Crypt(null, $cuSettings->secretkey );
        $event = $crypt->decrypt( $event );
        $event = json_decode( $event );

        $event->uid = $xoopsUser->uid();

        // Include controller file
        if ( 'plugin' == $event->type )
            $file = XOOPS_ROOT_PATH . '/modules/rmcommon/plugins/' . $event->element . '/class/' . strtolower( $event->element ) . '.notifications.class.php';
        elseif ( 'theme' == $event->type )
            $file = XOOPS_ROOT_PATH . '/themes/' . $event->element . '/class/' . strtolower( $event->element ) . '.notifications.class.php';
        else
            $file = XOOPS_ROOT_PATH . '/modules/' . $event->element . '/class/' . strtolower( $event->element ) . '.notifications.class.php';

        include_once $file;
        $class = ucfirst($event->element) . '_Notifications';

        if ( !class_exists( ucfirst($event->element) . '_Notifications' ) ) {
            $this->add_error( __('There are not a notifications controller for this element', 'rmcommon') );
            return false;
        }

        // Verify if event is a valid and existing event in module
        $notification = $class::get();

        if ( !$notification->is_valid($event->event) ){
            $this->add_error( __('Specified event is not valid for this element', 'rmcommon') );
            return false;
        }

        $subscribed = $this->is_subscribed( $event->event, $event->element, $event->type, $event->params );
        $event->status = $status ? 'subscribed' : 'removed';

        if ( $status && $subscribed )
            return $event;

        if ( !$status && $subscribed ) {
            if ( $this->unsubscribe($event) )
                return $event;
            else
                return false;
        }

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "INSERT INTO " . $db->prefix("mod_rmcommon_notifications") . " (`event`,`element`,`params`,`uid`,`type`,`date`)
                VALUES ('$event->event', '$event->element', '$event->params', '".$xoopsUser->uid()."',
                '$event->type', now())";

        if ( $db->queryF( $sql ) )
            return $event;
        else
            $this->add_error( $db->error() );

        return false;

    }

    /**
     * Verify if there exists a subscription for a specif event and specif user
     * @param string $event Event name
     * @param string $element Element dirname
     * @param string $type Element type (module, theme, plugin)
     * @param string $params Identifier parameter
     * @param int $uid User id
     * @return bool
     */
    public function is_subscribed( $event, $element, $type, $params, $uid = 0 ){
        global $xoopsUser;

        if ( 0 >= $uid && !$xoopsUser )
            return false;

        $uid = $uid == 0 ? $xoopsUser->uid() : $uid;

        $type = $type == '' ? $type : 'module';

        if ( '' == $event || '' == $element )
            return false;

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT COUNT(*) FROM " . $db->prefix("mod_rmcommon_notifications") . " WHERE
                event = '$event' AND element = '$element' " . ($params!=''?" AND params = '$params'" : '') . "
                AND `type` = '$type' AND uid = $uid";

        list($exists) = $db->fetchRow( $db->query($sql) );

        if ( $exists )
            return true;
        else
            return false;

    }

    /**
     * Remove a subscription from database
     *
     * @param $event
     *
     * @return bool
     */
    public function unsubscribe( $event ){

        $event->type = $event->type == '' ? 'module' : $event->type;

        if ( '' == $event->event || '' == $event->element || 0 >= $event->uid )
            return false;

        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "DELETE FROM " . $db->prefix("mod_rmcommon_notifications") . " WHERE
                event = '$event->event' AND element = '$event->element' AND
                params = '$event->params' AND type = '$event->type' AND uid = $event->uid";

        return $db->queryF( $sql );

    }

    /**
     * Send a notification according to event details
     *
     * @param RMNotificationItem $event
     * @param array $params Parameters to pass to the local notifications controller
     */
    public function notify( RMNotificationItem $event, $params ){
        global  $xoopsConfig;

        if ( $event->isNew() )
            return false;

        // Include controller file
        if ( 'plugin' == $event->type )
            $file = XOOPS_ROOT_PATH . '/modules/rmcommon/plugins/' . $event->element . '/class/' . strtolower( $event->element ) . '.notifications.class.php';
        elseif ( 'theme' == $event->type )
            $file = XOOPS_ROOT_PATH . '/themes/' . $event->element . '/class/' . strtolower( $event->element ) . '.notifications.class.php';
        else
            $file = XOOPS_ROOT_PATH . '/modules/' . $event->element . '/class/' . strtolower( $event->element ) . '.notifications.class.php';

        include_once $file;
        $class = ucfirst($event->element) . '_Notifications';

        if ( !class_exists( $class ) )
            return false;

        $notification = $class::get();

        // Get subscribed users
        $users = $this->users( $event );

        // Get the email body
        $xoopsMailer =& xoops_getMailer();
        $xoopsMailer->useMail();
        $xoopsMailer->setHTML( $notification->use_html() );

        $xoopsMailer->setToUsers($users);
        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
        $xoopsMailer->setFromName( $notification->from_name() );
        $xoopsMailer->setSubject( sprintf( __('Automatic notification: %s', 'rmcommon'), $notification->subject($event->event, $params) ) );
        $xoopsMailer->setBody( $notification->body( $event, $params ) );
        $xoopsMailer->send();

    }

    /**
     * Get all subscribed users
     * @param RMNotificationItem $event
     * @return array
     */
    public function users( $event ){
        global $xoopsDB;

        if ( $event->isNew() )
            return false;

        $db = $xoopsDB;
        $sql = "SELECT users.* FROM " . $db->prefix("users") . " users
                INNER JOIN " . $db->prefix("mod_rmcommon_notifications") . " n ON users.uid = n.uid WHERE
                n.event = '$event->event' AND n.element = '$event->element' AND n.params = '$event->params'
                ANd n.`type` = '$event->type'";
        $result = $db->query( $sql );

        $users = array();
        while( $row = $db->fetchArray( $result ) ){
            $user = new XoopsUser();
            $user->assignVars( $row );
            $users[] = $user;
        }

        return $users;

    }

    public function subscriptions( $uid = 0 ){
        global $xoopsUser, $xoopsDB;

        if ( $uid <= 0 && !$xoopsUser )
            return false;

        $uid = $uid <= 0 ? $xoopsUser->uid() : $uid;

        $db = $xoopsDB;

        $sql = "SELECT * FROM " . $db->prefix("mod_rmcommon_notifications") . " WHERE uid=$uid ORDER BY element, `type`";
        $result = $db->query( $sql );
        $subscriptions = array();

        while( $row = $db->fetchArray( $result ) ){

            $id = $row['event'] . ':' . $row['element'] . ':' . $row['type'] . ':' . $row['params'];
            $subscriptions[hash( 'crc32', $id, false )] = (object) array(
                'event'     => $row['event'],
                'element'   => $row['element'],
                'params'    => $row['params'],
                'type'      => $row['type'],
                'date'      => $row['date']
            );

        }

        return $subscriptions;

    }

}


class RMNotificationItem
{
    private $data = array();
    private $new = true;

    /**
     * Class constructor.
     * Initialize a new notification item receiving as parameter
     * the array containing all event data:
     * <pre>
     * array(
     *     'event'          => 'Event identifier name',
     *     'element'        => 'Element directory name (module, theme or plugin)',
     *     'params'         => 'Parameters to identify specific users subscriptions',
     *     'permissions'    => 'Array with permissions for users and groups',
     *     'caption'        => 'Title that shows on list'
     * );
     * </pre>
     * @param $event
     * @return RMNotificationItem
     */
    public function __construct( $event ){
        if ( !is_array( $event ) || empty( $event ) )
            return false;

        $this->data = $event;
        $this->new = false;
        return $this;
    }

    /**
     * Sets the parameters index of the event.
     * The <code>$params</code> can be any string that works as identifier for
     * specific subscription (e.g. a post ID)
     * @param string $params
     * @return $this|bool
     */
    public function parameters( $params ){

        if ( $this->new )
            return false;

        $this->data['params'] = $params;
        return $this;

    }

    /**
     * Sets the permissions for the event.
     * The <code>$permissions</code> parameter must be an array with next form:
     * <pre>
     * array(
     *     'users'  => array(0,1,2,), // All IDs from allowed users
     *     'groups' => array(1,3,5)   // All allowed groups IDs
     * );
     * </pre>
     *
     * The permissions verifications works in three steps:
     *
     * <ol>
     * <li>When users array is not provided, and groups array is not provided, and if the current user
     * is a registered user, then the notification item is shown.</li>
     * <li>When users array has been provided, the current user ID is searched in provided lists of users.
     * If user found then the notification item is shown to user.</li>
     * <li>When users list is not provided, or current user is not present in list, then the system
     * will check the groups list and compare it with users groups. If user belong to a group present in list
     * then the item will be shown.</li>
     * </ol>
     *
     * @param $permissions
     * @return $this|bool
     */
    public function permissions( $permissions ){

        if ( $this->new )
            return false;

        $this->data['permissions'] = $permissions;
        return $this;

    }

    /**
     * Returns a index from <code>event</code data or false if not exists.
     *
     * @param $index
     *
     * @return bool
     */
    public function __get( $index ){
        if ( $this->new )
            return false;

        if ( !array_key_exists( $index, $this->data ) )
            return false;

        return $this->data[$index];
    }

    public function __set( $index, $value ){

        if ( $this->new )
            return false;

        // Allowed data indexes
        $allowed = array('hash','type', 'subscribed');

        if (!in_array( $index, $allowed ) )
            return false;

        $this->data[$index] = $value;
        return true;

    }

    /**
     * Allows to determine if the object have a valid data or not
     * @return bool
     */
    public function isNew(){
        return $this->new;
    }

    /**
     * Returns all event data for this object
     * @return array|bool
     */
    public function data(){
        if ( $this->new )
            return false;

        return $this->data;
    }
}