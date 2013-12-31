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
                    if(preg_match("/^http.*/", $menu['icon']))
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
                'link' => strpos($menu['link'], 'http://')!==FALSE && strpos($menu['link'], 'ftp://')!==FALSE ? $menu['link'] : XOOPS_URL.'/modules/'.$mod->getVar('dirname','n').'/'.$menu['link'],
                'icon' => isset($menu['icon']) ? (strpos($menu['icon'], 'http://')!==FALSE ? $menu['icon'] : XOOPS_URL.'/modules/'.$mod->dirname().'/'.$menu['icon']) : '',
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
            $submenu[$i]['link'] = strpos($menu['link'], 'http://')!==FALSE && strpos($menu['link'], 'ftp://')!==FALSE ? $menu['link'] : XOOPS_URL.'/modules/'.$mod->getVar('dirname','n').'/'.$menu['link'];
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
            'main'  => __('General Settings','twop6'),
            'user'  => __('User Info Settings','twop6'),
            'meta'  => __('Meta Tags and Footer','twop6'),
            'word'  => __('Word Censoring','twop6'),
            'search'  => __('Search Options','twop6'),
            'mail'  => __('Email Setup','twop6'),
            'auth'  => __('Authentication Options','twop6'),
            'mods'  => __('System Module Settings','twop6'),
        );

        switch($data){
            case 'icon':
                $icon = isset($replacements_icons[$id]) ? '<i class="'.$replacements_icons[$id].'"></i>' : '';;
                return $icon;

            case 'title':
                $title = isset($replacements_titles[$id]) ? $replacements_titles[$id] : '';
                return $title;
        }

    }

    /**
     * Get the module icon
     */
    public function moduleIcon($module, $size = '16'){
        
        global $xoopsModule;
        
        $available = array(
            'mylinks'           => 'xicon-link',
            'news'              => 'xicon-consulting',
            'contact'           => 'xicon-email',
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
    public function getIcon($menu, $noaccept = false){
        global $xoopsModule;
        
        // Icon equivalences
	    if($noaccept){
		    $accepted = array('preferences'   => 'xicon-settings');
	    } else {
	        $accepted = array(
	            'dashboard'     => 'xicon-home',
	            'modules'       => 'xicon-plus',
	            'blocks'        => 'xicon-block',
	            'users'         => 'xicon-user',
	            'imgmanager'    => 'xicon-picture',
	            'comments'      => 'xicon-comment',
	            'plugins'       => 'xicon-plugin',
	            'avatars'       => 'xicon-account',
	            'banners'       => 'xicon-offer',
	            'blocksadmin'   => 'xicon-block',
	            'groups'        => 'xicon-users',
	            'images'        => 'xicon-picture',
	            'mailusers'     => 'xicon-email',
	            'modulesadmin'  => 'xicon-plus',
	            'maintenance'   => 'xicon-publish',
	            'preferences'   => 'xicon-settings',
	            'smilies'       => 'xicon-smile',
	            'tplsets'       => 'xicon-invoice',
	            'userrank'      => 'xicon-star',
	            'allusers'      => 'xicon-users',
	            'newuser'       => 'xicon-plus',
	            'rmc_imgcats'   => 'xicon-category',
	            'rmc_imgnewcat' => 'xicon-addfolder',
	            'rmc_images'    => 'xicon-picture',
	            'rmc_newimages' => 'xicon-addpicture',
	            'updates'       => 'xicon-refresh',
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
            $pos_fa = strpos( $menu['icon'], 'fa fa-' );
            $pos_moon = strpos( $menu['icon'], 'icon icon-' );
            $pos_boot = strpos( $menu['icon'], 'glyphicon glyphicon-' );

            if ( false !== $pos_fa || false !== $pos_moon || false !== $pos_boot )
                return '<span class="' . $menu['icon']. '"></span> ';
            else
                return '<i class="xo-icon" style="background-image: url('.$menu['icon'].'); background-size: 16px 16px;"></i> ';
        }
        
        // Check system menu
        $matches = array();
        preg_match("/.*admin\.php\?fct=(.*)/", $menu['link'], $matches);

        if (!empty($matches) && isset($accepted[$matches[1]]))
            return '<i class="xo-icon '.$accepted[$matches[1]].'"></i> ';
        
    }

	public function submenuIcon($menu, $dir=''){

		if(isset($menu['location']) && $menu['location']!='' && isset($menu['icon']) && $menu['icon']!=''){
			$menu['icon'] = ($dir!='' ? XOOPS_URL.'/modules/'.$dir.'/' : '').str_replace("../",'', $menu['icon']);
		}

		if(isset($menu['selected']) && $menu['selected']!='' && isset($menu['icon']) && $menu['icon']!=''){
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
            $md -= 4;
            $lg -= 2;
        }

        if ($right){
            $md -= 4;
            $lg -= 3;
        }

        return "col-md-$md col-lg-$lg";

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
    
}
