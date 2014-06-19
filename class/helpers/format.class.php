<?php
/**
----------------------------------------
Smart-B ERP
@package:   Sistema Base
@author     Red México
@author     http://www.redmexico.com.mx
@author     Eduardo Cortés
@copyright  2013 Red México
@version    $Id$
----------------------------------------
**/

/**
 * Esta clase contiene métodos útiles para dar formato a diversos datos
 */
class RMFormat
{
    /**
     * Da formato a un número telefónico basado en su longitud
     * Ejemplo:
     * <code>RMFormat::phone( "9991999999")</code>
     * Devuelve: 999-199-9999
     *
     * @param $phone <p>Número teléfonico a formatear
     * @return string
     */
    static function phone( $phone ){

        $matches = array();
        $found = false;

        $patterns = array(
            '/^(\d{3})[^\d]*(\d{4})$/', // Número local
            '/^(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$/', // Celular o con clave lada (sin 044)
            '/^(\d{3})(\d{1})[^\d]*(\d{2})[^\d]*(\d{4})$/', // Celular o con clave lada (sin 044)
            '/^(0\d{2})[^\d]*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$/', // Celular con 044 al principio,
            '/^(\d{2})[^\d]*(\d{3})[^\d]*(\d{3})[^\d]*(\d{4})$/' // Con código de país
        );

        $replaces = array(
            '$1&middot;$2',
            '($1) $2&middot;$3',
            '($1) $2$3&middot;$4',
            '$1 ($2) $3&middot;$4',
            '+$1 ($2) $3&middot;$4',
        );

        $formatted = preg_replace( $patterns, $replaces, $phone );
        /*foreach ( $patterns as $search ){

            if( preg_match( $search, $phone, $matches ) ){
                $found = true;
                break;
            }

        }

        if (!$found)
            return $phone;
        */

        return $formatted;

        //$matches = array_slice( $matches, 1);
        //return implode("&middot;", $matches);

    }

    /**
     * Da formato a fechas MySQL
     * @param string $date
     * @param string $format
     * @param bool $local Utilizar formato de localización
     * @return string
     */
    static function date( $date, $format = '', $local = false ){

        if($date=='') return;

        $time = strtotime($date);

        if ($time<=0)
            return '<code>?</code>';

        if ($local){

            $tf = new RMTimeFormatter($time, $format);
            return $tf->format();

        }

        return date($format!='' ? $format : 'd/m/Y H:i:s', $time);

    }

    /**
     * Get the icon for a specific social network
     * The icon is formatted according to FontAwesome icons
     *
     * @param string $type <p>Identifier type of social network</p>
     * @return string
     */
    public static function social_icon( $type ){

        $networks = array(

            'twitter' => 'fa-twitter-square',
            'linkedin' => 'fa-linkedin-square',
            'github' => 'fa-github-alt',
            'pinterest' => 'fa-pinterest-square',
            'google+' => 'fa-google-plus-square',
            'youtube' => 'fa-youtube-square',
            'rss' => 'fa-rss-square',
            'xing' => 'fa-xing-square',
            'dropbox' => 'fa-dropbox',
            'instagram' => 'fa-instagram',
            'flickr' => 'fa-flickr',
            'tumblr' => 'fa-tumblr-square',
            'dribbble' => 'fa-dribbble',
            'skype' => 'fa-skype',
            'foursquare' => 'fa-foursquare',
            'vimeo' => 'fa-vimeo-square',
            'vimeo' => 'fa-vimeo-square',

        );

        if ( isset( $networks[$type] ) )
            return $networks[$type];
        else
            return 'fa-chain';

    }

}