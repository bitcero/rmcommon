<!DOCTYPE HTML>
<html lang="<?php echo $cuSettings->lang; ?>">
    <head>
        <meta charset="utf-8">
        <?php
        
        !defined('RMCLOCATION') ? define('RMCLOCATION', '') : true;
        !defined('RMCSUBLOCATION') ? define('RMCSUBLOCATION', '') : true;
        
        foreach ($this->tpl_styles as $id => $style){
            $url = $style['url'];
            unset($style['url'], $style['footer'], $style['type']);

            $extra = '';
            foreach($style as $name => $value){
                $extra .= ' ' . $name . '="' . $value . '"';
            }
            echo '<link id="'.$id.'" rel="stylesheet" type="text/css" href="'.$url.'"'.$extra.'>'."\n";
        }

        foreach ($this->tpl_scripts as $id => $script){
            $type = $script['type'];
            $url = $script['url'];

            unset($script['type'], $script['url'], $script['footer']);

            $extra = '';
            foreach($script as $name => $value){
                $extra .= ' ' . $name . '="' . $value . '"';
            }
            echo '<script id="'.$id.'" type="'.$type.'" src="'.$url.'"'.$extra.'></script>'."\n";
        }

        echo $this->head_scripts();
        
        foreach ($this->tpl_head as $head){
            echo $head."\n";
        }
        
        include_once 'include/xoops_metas.php';
        ?>
        <title><?php if($this->get_var('xoops_pagetitle')!=''): ?><?php echo $this->get_var('xoops_pagetitle'); ?> - <?php endif; ?><?php echo isset($xoopsModule) ? $xoopsModule->getInfo('name').' - ' : ''; ?><?php echo $xoopsConfig['sitename']; ?></title>
    </head>
    <body<?php if($this->get_toolbar()): ?> class="xo-body-toolbar"<?php endif; ?>>
        <!-- Menu bar -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="xo-menubar">

            <div class="navbar-header">
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".twop6-navbar-toolbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="<?php echo RMCURL; ?>"><img src="<?php echo TWOP6_URL; ?>/images/logo.png" alt="<?php _e('XOOPS','twop6'); ?>" /></a>
            </div>

            <div class="collapse navbar-collapse twop6-navbar-toolbar">

                    <ul class="nav navbar-nav">
                        
                        <li class="dropdown<?php if($xoopsModule->dirname()=='rmcommon'): ?> active<?php endif; ?>">
                            <a href="<?php echo RMCURL; ?>" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                <i class="xo-icon xicon-cu"></i> <?php _e('Common Utilities','twop6'); ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach($rmcommon_menu as $menu): ?>
                                <?php if(isset($menu['divider'])): ?>
                                <li class="divider"></li>
                                <?php else: ?>
                                <li<?php if(isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                    <a href="<?php echo $menu['link']; ?>"<?php if(isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>>
                                        <?php echo $xoFunc->getIcon($menu); ?>
                                        <?php echo $menu['title']; ?>
                                    </a>
                                    <?php if(isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                    <?php foreach($menu['options'] as $sub): ?>
                                        <?php if( isset( $sub['divider'] ) ): ?>
                                            <li class="divider"></li>
                                        <?php continue; endif; ?>
                                        <li>
                                            <a href="<?php echo $sub['link']; ?>">
                                                <?php echo $xoFunc->submenuIcon($sub, 'rmcommon'); ?>
                                                <?php echo $sub['title']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="dropdown<?php if($xoopsModule->dirname()=='system'): ?> active<?php endif; ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                <i class="xo-icon xicon-gear"></i>
                                <?php _e('System','twop6'); ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach($system_menu as $menu): ?>
                                <?php if(isset($menu['divider'])): ?>
                                <li class="divider"></li>
                                <?php else: ?>
                                <li<?php if(isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                    <a href="<?php echo $menu['link']; ?>"<?php if(isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>>
                                        <?php echo $xoFunc->getIcon($menu); ?>
                                        <?php echo $menu['title']; ?>
                                    </a>
                                    <?php if(isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                    <?php foreach($menu['options'] as $sub): ?>
                                        <li>
                                            <a href="<?php echo $sub['link']; ?>">
                                                <?php echo $sub['icon']; ?>
                                                <?php echo $sub['title']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php if($other_menu): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                <i class="xo-icon xicon-gear"></i>
                                <?php _e('Theme','twop6'); ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach($other_menu as $menu): ?>
                                <?php if(isset($menu['divider'])): ?>
                                <li class="divider"></li>
                                <?php else: ?>
                                <li<?php if(isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                    <a href="<?php echo $menu['link']; ?>"<?php if(isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>>
                                        <?php echo $xoFunc->getIcon($menu); ?>
                                        <?php echo $menu['title']; ?>
                                    </a>
                                    <?php if(isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                    <?php foreach($menu['options'] as $sub): ?>
                                        <li><a href="<?php echo $sub['link']; ?>"><?php echo $sub['title']; ?></a></li>
                                    <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        
                        <li class="dropdown">
                            <a href="#" title="<?php _e('Modules Box','twop6'); ?>" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                <i class="xo-icon xicon-modules"></i>
                                <?php _e('Modules','twop6'); ?>
	                            <b class="caret"></b>
                            </a>
	                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
		                        <?php foreach($xoModules as $mod): ?>
			                        <?php if($mod['dirname']=='rmcommon' || $mod['dirname']=='system') continue; ?>
			                        <?php $submenu = array(); $submenu = $xoFunc->moduleMenu($mod['dirname']); ?>
			                        <li<?php echo !empty($submenu) ? ' class="dropdown-submenu"' : ''; ?>>
				                        <a href="<?php echo $mod['admin_link']; ?>"<?php echo !empty($submenu) ? ' tabindex="-1"' : ''; ?>>
					                        <?php echo $xoFunc->moduleIcon($mod['dirname']); ?>
					                        <?php echo $mod['name']; ?>
				                        </a>
				                        <?php if(!empty($submenu)): ?>
				                        <ul class="dropdown-menu">
	                                        <?php foreach($submenu as $sub): ?>
		                                    <?php if(isset($sub['divider'])): ?>
			                                <li class="divider"></li>
			                                <?php else: ?>
											<li<?php echo isset($sub['options']) ? ' class="dropdown-submenu"' : ''; ?>>
		                                        <a href="<?php echo $sub['link']; ?>"<?php echo isset($sub['options']) ? ' tabindex="-1"' : ''; ?>>
			                                        <?php echo $xoFunc->submenuIcon($sub); ?>
			                                        <?php echo $sub['title']; ?>
		                                        </a>
												<?php if(isset($sub['options'])): ?>
												<ul class="dropdown-menu">
													<?php foreach($sub['options'] as $subsub): ?>
                                                        <?php if(isset($subsub['divider']) || $subsub=='divider'): ?>
                                                            <li class="divider"></li>
                                                        <?php else: ?>
                                                            <li>
                                                                <a href="<?php echo $subsub['link']; ?>">
                                                                    <?php echo $xoFunc->submenuIcon($subsub, $mod['dirname']); ?>
                                                                    <?php echo $subsub['title']; ?>
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
													<?php endforeach; ?>
												</ul>
												<?php endif; ?>
											</li>
                                            <?php endif; ?>
		                                    <?php endforeach; ?>
	                                    </ul>
				                        <?php endif; ?>
			                        </li>
		                        <?php endforeach; ?>
		                        <li class="divider"></li>
		                        <li>
			                        <a href="#" id="xo-showmodules"><i class="xo-icon xicon-modules"></i> <?php _e('Modules Box','twop6'); ?></a>
		                        </li>
	                        </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="xo-upd-notifier">
                            <!-- Updates notifier -->
                            <a href="<?php echo RMCURL; ?>/updates.php"><?php echo sprintf(__('%s updates available','twop6'), '<span class="badge badge-warning">%s</span>'); ?></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                <i class="xo-icon xicon-ray"></i>
                                <?php _e('Quick Links','rmcommon'); ?>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu">
                                <?php if($this->help()): ?>
                                <li class="dropdown-submenu">
                                    <a href="#" tabindex="-1" onclick="return false;">
                                        <i class="xo-icon xicon-faq"></i>
                                        <?php _e('Help','rmcommon'); ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php foreach($this->help() as $help): ?>
                                        <li>
                                        <a href="<?php echo $help['link']; ?>" class="help_button rm_help_button" style="background-image: url(<?php echo TWOP6_URL; ?>/images/help.png);" target="_blank" title="<?php echo $help['caption']; ?>"><?php echo $help['caption']; ?></a>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                                <li class="divider"></li>
                                <?php endif; ?>
                                <?php if($xoopsModule->getInfo('social')): ?>
                                    <li class="dropdown-submenu">
                                        <a href="#" tabindex="-1" onclick="return false;">
                                            <i class="xo-icon xicon-world"></i>
                                            <?php _e('Social Links','rmcommon'); ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <?php foreach($xoopsModule->getInfo('social') as $net): ?>
                                            <li class="nav_item">
                                                <a href="<?php echo $net['url']; ?>" target="_blank"><?php echo $net['title']; ?></a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a href="<?php echo RMCURL; ?>">
                                        <i class="xo-icon xicon-cp"></i>
                                        <?php _e('Control Panel','twop6'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo XOOPS_URL; ?>" target="_blank">
                                        <i class="xo-icon xicon-home"></i>
                                        <?php _e('View Home Page','twop6'); ?>
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="http://www.xoops.org" target="_blank">
                                        <i class="xo-icon xicon-xoops"></i>
                                        XOOPS.org
                                    </a>
                                </li>
                                <li>
                                    <a href="http://www.xoopsmexico.net" target="_blank">
                                        <i class="xo-icon xicon-xm"></i>
                                        XOOPSMexico.net
                                    </a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="<?php echo RMCURL; ?>/?twop6=about">
                                        <i class="xo-icon xicon-idea"></i>
                                        <?php _e('About Two·Six','twop6'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>

            </div>
        </nav>
        <!-- End menu bar //-->
        
        <!-- Toolbar with menus -->
	       <div class="navbar navbar-fixed-top" id="xo-toolbar">

               <div class="navbar-header">
                   <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".twop6-toolbar-toolbar">
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                   </button>

                   <a class="navbar-brand visible-xs" href="#"><?php _e('Menu', 'rmcommon'); ?></a>

               </div>

               <div class="collapse navbar-collapse cu-titlebar twop6-toolbar-toolbar">

	                    <?php
	                        if($xoopsModule->dirname()=='rmcommon')
	                            $menus =& $rmcommon_menu;
	                        elseif($xoopsModule->dirname()=='system')
	                            $menus =& $system_menu;
	                        else
	                            $menus = $this->get_menus();
	                    ?>
	                    <ul class="nav navbar-nav">
	                        <?php foreach($menus as $menu): ?>
                                <?php if( isset($menu['divider'] ) ): ?>
                                    <li class="divider"></li>
                                <?php continue; endif; ?>
	                        <li<?php if(isset($menu['options']) && $menu['options']): ?> class="dropdown"<?php endif; ?>>
	                            <a href="<?php echo $menu['link']; ?>"<?php if ( $menu['title'] == __('Options', 'rmcommon') ): ?> data-action="load-remote-dialog"<?php endif; ?><?php if(isset($menu['options']) && $menu['options']): ?> class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"<?php endif; ?>>
	                                <?php echo $xoFunc->getIcon($menu); ?>
		                            <?php echo $menu['title']; ?>
	                                <?php if(isset($menu['options']) && $menu['options']): ?><b class="caret"></b><?php endif; ?>
	                            </a>
	                            <?php if(isset($menu['options']) && $menu['options']): ?>
	                            <ul class="dropdown-menu">
	                                <?php foreach($menu['options'] as $sub): ?>
                                        <?php if( isset( $sub['divider'] ) ): ?>
                                            <li class="divider"></li>
                                        <?php else: ?>
                                        <li<?php if(isset($sub['options']) && $sub['options']): ?> class="dropdown-submenu"<?php endif; ?>>
                                            <a href="<?php echo strpos($sub['link'], 'http://')===FALSE ?  XOOPS_URL.'/modules/'.$xoopsModule->dirname().'/'.$sub['link'] : $sub['link']; ?>"<?php if(isset($sub['options']) && $sub['options']): ?> tabindex="-1"<?php endif; ?>>
                                                <?php echo $xoFunc->getIcon($sub); ?>
                                                <?php echo $sub['title']; ?>
                                            </a>
                                            <?php if(isset($sub['options']) && $sub['options']): ?>
                                                <ul class="dropdown-menu">
                                                    <?php foreach($sub['options'] as $submenu): ?>
                                                        <li><a href="<?php echo $submenu['link']; ?>"><?php echo $xoFunc->getIcon($submenu); ?><?php echo $submenu['title']; ?></a></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </li>
                                        <?php endif; ?>
	                                <?php endforeach; ?>
	                            </ul>
	                            <?php endif; ?>
	                        </li>
	                        <?php endforeach; ?>
	                    </ul>
	            </div>
	        </div>
        <!-- End toolbar with menus //-->

        <!-- rmcommon toolbar -->
        <?php if($this->get_toolbar()): ?>
            <nav class="navbar" id="rmc-toolbar">

                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".twop6-toolbar-icons">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand visible-xs" href="#"><?php _e('Toolbar', 'rmcommon'); ?></a>
                </div>

                <div class="navbar-collapse collapse twop6-toolbar-icons">
                    <ul class="nav navbar-nav">
                        <?php foreach($this->get_toolbar() as $menu): ?>
                            <li<?php echo $menu['location']==RMCSUBLOCATION ? ' class = "active"' : ($menu['location']==RMCLOCATION ? ' class="active"' : ''); ?>>
                                <button type="button" href="<?php echo $menu['link']; ?>" <?php echo $xoFunc->render_attributes( $menu['attributes'] ); ?>>
                                    <span><?php if($menu['icon']): ?><img src="<?php echo $menu['icon']; ?>"><?php endif; ?></span>
                                    <?php echo $menu['title']; ?>
                                </button>
                            </li>
                            <li class="divider-vertical"></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>
        <?php endif; ?>
        <!-- End rmcommon toolbar //-->

		<?php echo RMBreadCrumb::get()->render(); ?>
        
        <!-- System messages -->
        <?php foreach($rmc_messages as $message): ?>
        <div class="container">
            <div class="alert <?php echo $tp6Alerts[$message['level']]; ?>"<?php if($message['level']>4 && $message['icon']!=''): ?> style="background-image: url(<?php echo $message['icon']; ?>);"<?php endif; ?>>
                <button type="button" class="close" data-dismiss="alert">×</button>
                <?php echo html_entity_decode($message['text']); ?>
            </div>
        </div>
        <?php endforeach; ?>
        <!-- End system messages //-->
        
        <!-- Content -->
        <div class="container" id="xo-content">
            <div class="row">
                <?php if($left_widgets): ?>
                <aside class="col-md-4 col-lg-2">
                    <?php foreach($left_widgets as $widget): ?>
                    <div class="xo-widget">
                        <h3 class="xo-blackbar">
                            <?php echo isset($widget['icon']) && $widget['icon']!='' ? ' <i class="xo-icon" style="background-image: url('.$widget['icon'].');"></i> ' : ''; ?>
                            <?php echo $widget['title']; ?>
                        </h3>
                        <div class="xo-widget-content"><?php echo $widget['content']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </aside>
                <?php endif; ?>
                
                <div class="<?php echo $xoFunc->calculate_cols($left_widgets, $right_widgets); ?>">
                    <div id="xo-contents">

                        <?php echo $content; ?>
                    </div>
                </div>
                
                <?php if($right_widgets): ?>
                <aside class="col-md-4 col-lg-3">
                    <?php foreach($right_widgets as $widget): ?>
                    <?php if(!isset($widget['title']) && !isset($widget['content'])) continue; ?>
                    <div class="xo-widget">
                        <h3 class="xo-blackbar">
                            <?php echo isset($widget['icon']) && $widget['icon']!='' ? ' <i class="xo-icon" style="background-image: url('.$widget['icon'].');"></i> ' : ''; ?>
                            <?php echo $widget['title']; ?>
                        </h3>
                        <div class="xo-widget-content"><?php echo $widget['content']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </aside>
                <?php endif; ?>
            </div>
        </div>
        <!-- End content //-->
        
        <!-- Footer -->
        <div class="container xo-footer">
            <hr>
            <footer class="row">
                <div class="col-md-6 col-lg-6">
                    <?php echo sprintf(__('Powered by %s.','twop6'), '<a href="http://www.xoops.org">'.XOOPS_VERSION.'</a>'); ?>
                    <?php echo sprintf(__('Reloaded by %s.','twop6'), '<a href="http://www.xoopsmexico.net">Common Utilities '.RMCVERSION.'</a>'); ?>
                </div>
                <div class="col-md-6 col-lg-6 xo-foot-links">
                    <ul>
                        <li><a href="http://www.xoops.org">XOOPS</a></li>
                        <li><a href="http://www.xoopsmexico.net">Xoops Mexico</a></li>
                        <li><a href="http://www.redmexico.com.mx">Red Mexico</a></li>
                    </ul>
                </div>
            </footer>
            <hr>
        </div>
        <!-- End footer //-->
        
        <?php if($xoopsConfig['debug_mode']==1): ?>
        <div class="container">
            <div class="well"><!--{xo-logger-output}--></div>
        </div>
        <?php endif; ?>
        <input type="hidden" name="cu_token" id="cu-token" value="<?php echo $xoopsSecurity->createToken(0, 'CUTOKEN'); ?>">
    </body>
</html>