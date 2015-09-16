<?php
/*
Theme name: Two Point Six
Theme URI: http://www.redmexico.com.mx
Version: 1.0
Author: bitcero
Author URI: http://www.bitcero.info
*/

class Twop6Functions
{
    /**
     * Get the menu for the current module
     */
    public function currentModuleMenu($m=''){

        global $xoopsModule, $xoopsUser, $rmTpl;

        if(!is_a($xoopsModule, 'XoopsModule')){
            return false;
        } else {
            $mod = $xoopsModule;
        }
        // Check user
        if(!is_a($xoopsUser, 'XoopsUser')) return false;

        if(!$xoopsUser->isAdmin($mod->mid())) return false;

        $amenu = $mod->getAdminMenu();
        $amenu = RMEvents::get()->run_event('rmcommon.current.module.menu', $amenu);
        if ($amenu){
            foreach ($amenu as $menu){
                if(isset($menu['icon']) && $menu['icon']!=''){
                    if(self::is_absolute_url( $menu['icon']) || self::is_font_icon( $menu['icon'] ) )
                        $icon = $menu['icon'];
                    else{
                        $m = array();

                        $icon = XOOPS_URL.'/modules/'.$xoopsModule->dirname().'/'.(preg_match("/^(\.\.\/){2,}/", $menu['icon']) ? $menu['icon'] : preg_replace("/^\.\.\/{1}/",'', $menu['icon']));
                    }
                } else {
                    $icon = '';
                }

                $rmTpl->add_menu(
                    $menu['title'],
                    strpos($menu['link'], 'http://')!==FALSE && strpos($menu['link'], 'ftp://')!==FALSE ? $menu['link'] : XOOPS_URL.'/modules/'.$mod->getVar('dirname','n').'/'.$menu['link'],
                    $icon,
                    !empty($menu['class']) ? $menu['class'] : '',
                    isset($menu['location']) ? $menu['location'] : '',
                    isset($menu['options']) ? $menu['options'] : array()
                );
                //$rmTpl->add_tool($menu['title'], $menu['link'], isset($menu['icon']) ? $menu['icon'] : '');
            }
        }

        if($mod->hasconfig()){
            $rmTpl->add_menu(__('Options','rmcommon'), $mod->getInfo('rmnative') ? RMCURL .'/settings.php?mod='.$mod->mid().'&amp;action=configure&amp;popup=1' : XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$mod->mid(), 'option','');
        }

        return null;
    }

    static function is_absolute_url( $url ){

        return preg_match( "/^[http:\/\/|https:\/\/|ftp:\/\/]/", $url );

    }

    static function is_font_icon( $icon ){

        $pos = strpos( $icon, 'fa fa-' );
        if (false !== $pos)
            return true;

        $pos = strpos( $icon, 'icon icon-' );
        if (false !== $pos)
            return true;

        $pos = strpos( $icon, 'glyphicon glyphicon-' );
        if (false !== $pos)
            return true;

        return false;

    }


    /**
     * Get the menu for a specified module
     */
    public function moduleMenu($m){

        global $xoopsModule, $xoopsUser;

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

        $amenu = $mod->getAdminMenu();

        $amenu = RMEvents::get()->run_event($mod->dirname().'.module.menu', $amenu);
        if(empty($amenu)) return false;

        $return_menu = array();

        foreach ($amenu as $menu){
            $return_menu[] = array(
                'title' => $menu['title'],
                'link' => preg_match("/^(http:\/\/|https:\/\/|ftp:\/\/|mailto:)/i", $menu['link']) ? $menu['link'] : XOOPS_URL.'/modules/'.$mod->getVar('dirname','n').'/'.$menu['link'],
                'icon' => isset($menu['icon']) ? (preg_match("/^(http:\/\/|https:\/\/)/i", $menu['icon']) ?
                    $menu['icon'] : ( self::is_font_icon($menu['icon']) ? $menu['icon'] : XOOPS_URL.'/modules/'
                    .$mod->dirname()
                    .'/'
                    .$menu['icon'])) :
            '',
                'location' => isset($menu['location']) ? $menu['location'] : '',
                'options' => isset($menu['options']) ? self::moduleSubmenu($menu['options'], $mod) : ($m=='system' && $menu['title']==_AM_SYSTEM_PREF ? self::systemPreferences() : null)
            );
        }

        if($mod->hasconfig()){
            $return_menu[] = array( 'divider' => 1 ); // Divisor for options
            $return_menu[] = array(
                'title' => __('Options','rmcommon'),
                'link' => $mod->getInfo( 'rmnative' ) ? XOOPS_URL . '/modules/rmcommon/settings.php?mod=' . $mod->mid() . '&amp;popup=1&amp;action=configure' : XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$mod->mid(),
                'icon' => '',
                'type' => 1
            );
        }

        return $return_menu;

    }

    /**
     * Prepare menu options
     */
    public function moduleSubmenu($submenu, $mod){

        if(!is_array($submenu)) return array();

        foreach($submenu as $i => $menu){
            if(isset($menu['divider']) || $menu == 'divider' ) continue;
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
        $confcat_handler = xoops_gethandler('configcategory');
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
                $icon = isset($replacements_icons[$id]) ? '<i class="'.$replacements_icons[$id].'"></i>' : '';;
                return $icon;

            case 'title':
                $title = isset($replacements_titles[$id]) ? $replacements_titles[$id] : '';
                return $title;
        }

        return null;
    }

    /**
     * Get the module icon
     */
    public function moduleIcon($module, $size = '16'){

        global $xoopsModule;

        $available = array(
            'mylinks'           => 'fa fa-link',
            'news'              => 'xicon-consulting',
            //'contact'           => 'xicon-email',
            'extgallery'        => 'xicon-picture',
            'date'              => 'xicon-date',
            'fmcontent'         => 'xicon-library',
            'marquee'           => 'xicon-advertising',
            'mastop_go2'        => 'xicon-cloud',
            'moduleinstaller'   => 'xicon-download',
            'pm'                => 'xicon-comment',
            'profile'           => 'xicon-profile',
            'protector'         => 'xicon-shield',
            'tag'               => 'xicon-tag',
            'xforms'            => 'xicon-form',
            'xlanguage'         => 'xicon-comment',
            'xoopsfaq'          => 'xicon-faq',
            'xoopspartners'     => 'xicon-me',
            'system'            => 'xicon-gear'
        );

        if(!is_a($xoopsModule, 'XoopsModule')) return false;

        if($xoopsModule->dirname()!=$module){
            $mod = RMModules::load_module($module);
        } else {
            $mod = $xoopsModule;
        }

        if(isset($available[$mod->dirname()]))
            return '<i class="xo-icon '.$available[$mod->dirname()].'"></i> ';

        $icon = $mod->getInfo('icon'.$size);
        $path = XOOPS_ROOT_PATH.'/modules/'.$mod->dirname().'/'.$icon;
        if(!is_file($path)){
            $path = TWOP6_PATH.'/images/modules/'.$mod->dirname().'-'.$size.'.png';

            if(!is_file($path))
                $path = TWOP6_PATH.'/images/module.png';

        }

        $icon = str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $path);

        return '<i class="xo-icon" style="background-image: url('.$icon.');"></i> ';

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
    public function getIcon($menu, $noaccept = false, $class = ''){
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
            return '<span class="fa fa-wrench"></span> ';

        /*if (isset($menu['location']) && isset($accepted[$menu['location']]))
            return '<i class="xo-icon '.$accepted[$menu['location']].'"></i> ';

        if(isset($menu['selected']) && isset($accepted[$menu['selected']]))
            return '<i class="xo-icon '.$accepted[$menu['selected']].'"></i> ';*/

        $modurl = XOOPS_URL.'/modules/'.$xoopsModule->dirname().'/';

        if(isset($menu['icon']) && $menu['icon']!=''){
            if ( self::is_font_icon( $menu['icon'] ) )
                return '<span class="' . $menu['icon']. ' ' . $class . '"></span> ';
            else
                return '<span class="xo-icon ' . $class . '" style="background-image: url('.$menu['icon'].');"></span> ';
        }

        // Check system menu
        $matches = array();
        preg_match("/.*admin\.php\?fct=(.*)/", $menu['link'], $matches);

        if (!empty($matches) && isset($accepted[$matches[1]]))
            return '<span class="'.$accepted[$matches[1]]. ' ' . $class . '"></span> ';

        return '<span></span>';

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

		return $this->getIcon($menu, true);

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
