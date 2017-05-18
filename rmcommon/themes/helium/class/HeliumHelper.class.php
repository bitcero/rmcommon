<?php
/**
 * Helium Theme
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
 * @package      helium
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

class HeliumHelper
{
    public function menuLink(\stdClass $menu, $module)
    {
        global $cuSettings;

        if($this->is_absolute_url($menu->link)){
            return $menu->link;
        }

        /**
         * If links are "standard" then return the url to module
         * and add the menu link
         */
        if (!$module->native || !$module->rewrite || !$cuSettings->permalinks) {
            return \RMUris::relative_url(XOOPS_URL . '/modules/' . $module->directory . '/' . $menu->link);
        }

        /**
         * If links could be "rewrited" by rmcommon then we need to detect the
         * module URL and form the right link.
         */

        $rewrite = property_exists($menu, 'rewrite') ? $menu->rewrite : false;

        if(!isset($cuSettings->modules_path)){
            $cuSettings->modules_path = [];
        }

        if (array_key_exists($module->directory, $cuSettings->modules_path) && $rewrite) {
            return \RMUris::relative_url(XOOPS_URL . '/admin/' . trim($cuSettings->modules_path[$module->directory], '/') . '/' . ltrim($rewrite, '/'));
        }

        if ($rewrite) {
            return \RMUris::relative_url(XOOPS_URL . '/modules/' . $module->directory . '/admin/index.php?s=' . $rewrite);
        } else {
            return \RMUris::relative_url(XOOPS_URL . '/modules/' . $module->directory . '/' . $menu->link);
        }

    }

    public function menuIcon($icon, $module)
    {
        global $cuIcons;

        /**
         * We need to verify that icon have the directory prefix
         */
        // Relative or absolute url?
        $matches = array();
        $absolute = preg_match("/^(http:\/\/|https:\/\/|ftp:\/\/|\/\/)/m", $icon, $matches, PREG_OFFSET_CAPTURE);

        if ($absolute) {
            return $cuIcons->getIcon($icon);
        }

        // Relative image url?
        $imageFormats = array('.jpg', '.gif', '.png', 'jpeg');
        if (in_array(substr($icon, -4), $imageFormats)) {
            return $cuIcons->getIcon(\RMUris::relative_url(XOOPS_URL . '/modules/' . $module . '/' . $icon));
        }


        return $cuIcons->getIcon($icon);

    }

    static function is_absolute_url( $url ){

        return preg_match( "/^(http:\/\/|https:\/\/|ftp:\/\/)/m", $url );

    }


    /**
     * Get the menu for a specified module
     */
    public function moduleMenu($m){

        global $xoopsModule, $xoopsUser, $common;

        if(!is_a($xoopsModule, 'XoopsModule')){
            $mod = RMModules::load_module($m);
        } else {
            if($xoopsModule->dirname()==$m)
                $mod = $xoopsModule;
            else
                $mod = RMModules::load_module($m);
        }

        if(!is_a($mod, 'XoopsModule')) return false;

        // Check user
        if(!is_a($xoopsUser, 'XoopsUser')) return false;

        if(!$xoopsUser->isAdmin($mod->mid())) return false;

        $amenu =& $mod->getAdminMenu();

        $amenu = RMEvents::get()->run_event($mod->dirname().'.module.menu', $amenu);
        if(empty($amenu)) return false;

        $return_menu = array();

        foreach ($amenu as $menu){
            $tempMenu = array(
                'title' => $menu['title'],
                'link' => $menu['link'],
                'icon' => array_key_exists('icon', $menu) ? $menu['icon'] : '',
                'location' => isset($menu['location']) ? $menu['location'] : '',
                'options' => isset($menu['options']) ? self::moduleSubmenu($menu['options'], $mod) : ($m=='system' && $menu['title']==_AM_SYSTEM_PREF ? self::systemPreferences() : null)
            );

            if (array_key_exists('rewrite', $menu)){
                $tempMenu['rewrite'] = $menu['rewrite'];
            }

            $return_menu[] = $tempMenu;

        }

        unset($tempMenu);

        if($mod->hasconfig()){
            $return_menu[] = array(
                'title' => __('Options','rmcommon'),
                'link' => $mod->getInfo( 'rmnative' ) ? XOOPS_URL . '/modules/rmcommon/settings.php?mod=' . $mod->mid() . '&amp;popup=1&amp;action=configure' : XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$mod->mid(),
                'icon' => 'svg-rmcommon-wrench',
                'type' => 1,
                'location' => 'cu-settings',
                'attributes' => array(
                    'data-action' => 'load-remote-dialog'
                )
            );
        }

        // Integration with other components
        $return_menu = $common->events()->trigger('rmcommon.module.menu', $return_menu, $m);

        return $return_menu;

    }

    /**
     * Prepare menu options
     */
    public function moduleSubmenu($submenu, $mod){

        if(!is_array($submenu)) return array();

        foreach($submenu as $i => $menu){
            if(isset($menu['divider']) || $menu == 'divider' ) continue;

            if(array_key_exists('rewrite', $menu)){
                return $submenu;
            }

            $submenu[$i]['link'] = preg_match("/^(http:\/\/|https:\/\/|ftp:\/\/|mailto:)/i", $menu['link']) ? $menu['link'] : XOOPS_URL.'/modules/'.$mod->getVar('dirname','n').'/'.$menu['link'];
        }

        return $submenu;

    }

    /**
     * Preferences menu for System module
     * @return array
     */
    public function systemPreferences(){

        include_once XOOPS_ROOT_PATH.'/modules/system/include/functions.php';
        $confcat_handler = xoops_getHandler('configcategory');
        $confcats = $confcat_handler->getObjects();
        $image = system_adminVersion('preferences', 'configcat');

        $options = array();

        foreach ( array_keys($confcats) as $i ) {
            $options[] = array(
                'title'     => self::getPreferenceData(system_AdminIcons( 'xoops/' . $image[$i]), 'title'),
                'link'      => XOOPS_URL.'/modules/system/admin.php?fct=preferences&op=show&confcat_id='.$confcats[$i]->getVar('confcat_id'),
                'icon'      => self::getPreferenceData(system_AdminIcons( 'xoops/' . $image[$i]), 'icon'),
            );
        }

        return $options;

    }

    /**
     * Get the icon for preferences categories
     * @param string Original image path
     * @param string Data type to return
     * @return string with required data
     */
    public function getPreferenceData($image, $data){

        $id = preg_replace("/^.*\/system_(.*)\.png$/i","$1",$image);

        $replacements_icons = array(
            'main'  => 'fa fa-cogs',
            'user'  => 'fa fa-user',
            'meta'  => 'fa fa-info-circle',
            'word'  => 'fa fa-filter',
            'search'  => 'fa fa-search',
            'mail'  => 'fa fa-envelope',
            'auth'  => 'fa fa-key',
            'mods'  => 'fa fa-cog',
        );

        $replacements_titles = array(
            'main'  => __('General Settings','rmcommon'),
            'user'  => __('User Info Settings','rmcommon'),
            'meta'  => __('Meta Tags and Footer','rmcommon'),
            'word'  => __('Word Censoring','rmcommon'),
            'search'  => __('Search Options','rmcommon'),
            'mail'  => __('Email Setup','rmcommon'),
            'auth'  => __('Authentication Options','rmcommon'),
            'mods'  => __('System Module Settings','rmcommon'),
        );

        switch($data){
            case 'icon':
                $icon = isset($replacements_icons[$id]) ? $replacements_icons[$id] : '';

                return $icon;

            case 'title':
                $title = isset($replacements_titles[$id]) ? $replacements_titles[$id] : '';
                return $title;
        }

        return null;
    }

    /**
     * Insert extra headers in theme
     */
    public function extra_headers(){
        global $xoopsModule;

        if($xoopsModule->dirname()=='rmcommon'
            && RMCLOCATION=='modules'
            && rmc_server_var($_REQUEST, 'action', '')==''){

            include DESIGNIA_PATH.'/include/sorter.inc';

        }
    }

    /**
    * Generate menu icon
    */
    public function getSystemIcon($menu, $noaccept = false, $class = ''){
        global $xoopsModule;

        // Icon equivalences
	    if($noaccept){
		    $accepted = array('preferences'   => 'fa fa-wrench');
	    } else {
	        $accepted = array(
	            'dashboard'     => 'fa fa-dashboard',
	            'modules'       => 'fa fa-folder',
	            'blocks'        => 'fa fa-th-large',
	            'users'         => 'fa fa-user',
	            'imgmanager'    => 'fa fa-picture-o',
	            'comments'      => 'fa fa-comments',
	            'plugins'       => 'fa fa-expand',
	            'avatars'       => 'fa fa-male',
	            'banners'       => 'fa fa-toggle-right',
	            'blocksadmin'   => 'fa fa-th-large',
	            'groups'        => 'fa fa-group',
	            'images'        => 'fa fa-picture-o',
	            'mailusers'     => 'fa fa-envelope',
	            'modulesadmin'  => 'fa fa-folder',
	            'maintenance'   => 'fa fa-fire-extinguisher',
	            'preferences'   => 'fa fa-wrench',
	            'smilies'       => 'fa fa-smile-o',
	            'tplsets'       => 'fa fa-files-o',
	            'userrank'      => 'fa fa-star',
	            'allusers'      => 'fa fa-users',
	            'newuser'       => 'xicon-plus'
	        );
	    }

        if(isset($menu['type']) || (isset($menu['icon']) && $menu['icon']=='option'))
            return 'fa fa-wrench';

        // Check system menu
        $matches = array();
        preg_match("/.*admin\.php\?fct=(.*)/", $menu['link'], $matches);

        if (!empty($matches) && isset($accepted[$matches[1]]))
            return $accepted[$matches[1]];

        return '';

    }

	public function submenuIcon($menu, $dir=''){

        if (!isset( $menu['location'] ) && !isset( $menu['selected'] ) )
            return $this->getIcon( $menu, true );

		if( isset($menu['icon']) && $menu['icon']!='' ){
            $pos_fa = strpos( $menu['icon'], 'fa fa-' );
            $pos_moon = strpos( $menu['icon'], 'icon icon-' );
            $pos_boot = strpos( $menu['icon'], 'glyphicon glyphicon-' );

            if ( false === $pos_fa && false === $pos_moon && false === $pos_boot )
                $menu['icon'] = ($dir!='' ? XOOPS_URL.'/modules/'.$dir.'/' : '').str_replace("../",'', $menu['icon']);
		}

		return $this->getSystemIcon($menu, true);

	}

    /**
     * Calculate the width of columns according to left and right widgets prescense
     *
     */
    public function calculate_cols($left, $right){

        $md = 12; $lg = 12;

        if ($left){
            $md -= 3;
            $lg -= 3;
        }

        if ($right){
            $md -= 3;
            $lg -= 3;
        }

        return "col-sm-$md col-lg-$lg";

    }

    /**
     * Render attributes
     * @param array $attrs <p>Attributes to render</p>
     * @return string
     */
    public function render_attributes( $attrs ){

        if ( empty( $attrs ) )
            return '';

        $rtn = '';
        foreach( $attrs as $name => $value ){

            $rtn .= ($rtn == '' ? '' : ' ') . $name .'="' . $value . '"';

        }

        return $rtn;

    }

    /**
     * Get general icon
     * @param string $icon
     * @return string
     */
    public function icon( $icon ){

        // Check if this is a font icon item
        if ( preg_match( "/^[fa|icon|glyphicon]/is", $icon ))
            return '<span class="' . $icon . '"></span>';

        return '<span class="xicon" style="background-image: url(' . $icon . ');"></span>';

    }

}
