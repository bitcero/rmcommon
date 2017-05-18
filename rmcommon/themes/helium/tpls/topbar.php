<!-- Menu bar -->
<nav role="navigation" id="he-topbar">

    <a class="navbar-brand he-logo" href="<?php echo RMCURL; ?>">
        <?php echo $logoHelium; ?>
    </a>

    <div class="he-topbar-collapse">

        <ul class="main-options">

            <!-- Toggel sidebar -->
            <li>
                <a href="#" class="toggle-menu">
                    <?php echo $cuIcons->getIcon('fa fa-bars'); ?>
                </a>
            </li>

            <!-- Common Utilities Menu -->
            <li class="dropdown<?php if($xoopsModule->dirname()=='rmcommon'): ?> active<?php endif; ?> rmcommon-menu">
                <a href="<?php echo RMCURL; ?>" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-rmcommon'); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach($rmcommon_menu['menu'] as $menu): ?>
                        <?php if(isset($menu['divider'])): ?>
                            <li class="divider"></li>
                        <?php else: ?>
                            <li<?php if(isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                <a
                                    href="<?php echo $xoFunc->menuLink( (object) $menu, (object) $rmcommon_menu); ?>"<?php if(isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>
                                    <?php if(array_key_exists('attributes', $menu)): ?><?php echo $xoFunc->render_attributes($menu['attributes']); ?><?php endif; ?>
                                    >
                                    <?php echo $cuIcons->getIcon($menu['icon']); ?>
                                    <?php echo $menu['title']; ?>
                                </a>
                                <?php if(isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach($menu['options'] as $sub): ?>
                                            <?php if( isset( $sub['divider'] ) ): ?>
                                                <li class="divider"></li>
                                                <?php continue; endif; ?>
                                            <li>
                                                <a href="<?php echo $xoFunc->menuLink( (object) $sub, (object) $rmcommon_menu); ?>">
                                                    <?php echo $cuIcons->getIcon($sub['icon']); ?>
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
                <?php foreach($other_menu as $item): ?>
                    <li class="dropdown <?php echo $item['class']; ?>">
                        <a href="<?php echo $item['link']; ?>" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"<?php echo array_key_exists('title', $item) ? ' title="' . $item['title'] . '"' : ''; ?>>
                            <?php echo array_key_exists('icon', $item) ? $cuIcons->getIcon($item['icon']) : ''; ?>
                            <?php if(array_key_exists('caption', $item)): ?>
                            <span class="caption"><?php echo $item['caption']; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach($item['menu'] as $menu): ?>
                                <?php if(isset($menu['divider'])): ?>
                                    <li class="divider"></li>
                                <?php else: ?>
                                    <li<?php if(isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                        <a
                                            href="<?php echo $menu['link']; ?>"<?php if(isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>
                                            <?php if(array_key_exists('attributes', $menu)): ?><?php echo $xoFunc->render_attributes($menu['attributes']); ?><?php endif; ?>
                                        >
                                            <?php echo $cuIcons->getIcon($menu['icon']); ?>
                                            <?php echo $menu['title']; ?>
                                        </a>
                                        <?php if(isset($menu['options'])): ?>
                                            <ul class="dropdown-menu">
                                                <?php foreach($menu['options'] as $sub): ?>
                                                    <?php if( isset( $sub['divider'] ) ): ?>
                                                        <li class="divider"></li>
                                                        <?php continue; endif; ?>
                                                    <li>
                                                        <a href="<?php echo $menu['link']; ?>">
                                                            <?php echo $cuIcons->getIcon($sub['icon']); ?>
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
                <?php endforeach; ?>
            <?php endif; ?>

            <li class="xo-upd-notifier" style="display: none;">
                <!-- Updates notifier -->
                <a href="<?php echo RMCURL; ?>/updates.php">
                    <?php echo sprintf(__('%s UPDATES','rmcommon'), '<span class="badge badge-warning">%s</span>'); ?>
                </a>
            </li>
        </ul>

        <ul class="navbar-right right-options">

            <!--li class="dropdown user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-user-circle'); ?>
                </a>
            </li-->

            <li id="updater-info-top">
                <a href="<?php echo RMUris::relative_url(RMCURL . '/updates.php'); ?>">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-update'); ?>
                    <em>%s</em>
                </a>
            </li>

            <li class="dropdown<?php if($xoopsModule->dirname()=='system'): ?> active<?php endif; ?> system-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-gear'); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach($system_menu['menu'] as $menu): ?>
                        <?php if(isset($menu['divider'])): ?>
                            <li class="divider"></li>
                        <?php else: ?>
                            <li<?php if(isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                <a href="<?php echo $xoFunc->menuLink((object)$menu, (object) $system_menu); ?>"<?php if(isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>>
                                    <?php echo $cuIcons->getIcon($xoFunc->getSystemIcon($menu)); ?>
                                    <?php echo $menu['title']; ?>
                                </a>
                                <?php if(isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach($menu['options'] as $sub): ?>
                                            <li>
                                                <a href="<?php echo $xoFunc->menuLink((object) $sub, (object)  $system_menu); ?>">
                                                    <?php echo $cuIcons->getIcon($sub['icon']); ?>
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

            <!-- Quick menu -->
            <li class="dropdown quick-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-lightning'); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php if($xoopsModule->getInfo('social')): ?>
                        <li class="dropdown-submenu">
                            <a href="#" tabindex="-1" onclick="return false;">
                                <?php echo $cuIcons->getIcon('fa fa-users'); ?>
                                <?php _e('Social Links','rmcommon'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach($xoopsModule->getInfo('social') as $net): ?>
                                    <li class="nav_item">
                                        <a href="<?php echo $net['url']; ?>" target="_blank">
                                            <?php if(isset($net['type'])): ?>
                                                <?php echo $cuIcons->getIcon("fa fa-" . $net['type']); ?>
                                            <?php endif; ?>
                                            <?php echo $net['title']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?php echo RMCURL; ?>">
                            <?php echo $cuIcons->getIcon("fa fa-dashboard"); ?>
                            <?php _e('Control Panel','rmcommon'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo XOOPS_URL; ?>" target="_blank">
                            <?php echo $cuIcons->getIcon("fa fa-home"); ?>
                            <?php _e('View Home Page','rmcommon'); ?>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="http://www.xoops.org" target="_blank">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-xoops'); ?>
                            XOOPS.org
                        </a>
                    </li>
                    <li>
                        <a href="http://www.eduardocortes.mx" target="_blank">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-bitcero'); ?>
                            <?php _e('Author Website', 'rmcommon'); ?>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="<?php echo RMUris::relative_url(XOOPS_URL .'/user.php?op=logout'); ?>">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-power-off text-danger'); ?>
                            <?php _e('Close Session', 'helium'); ?>
                        </a>
                    </li>
                </ul>
            </li>

            <?php if(!empty($helpLinks)): ?>
                <li class="help-menu<?php if(count($helpLinks) == 1): ?> dropdown<?php endif; ?>">
                    <?php if(count($helpLinks) == 1): ?>
                        <a href="<?php echo $helpLinks[0]['link']; ?>" data-action="help">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
                        </a>
                    <?php else: ?>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach($helpLinks as $help): ?>
                                <li>
                                    <a href="<?php echo $help['link']; ?>" target="_blank" data-action="help">
                                        <span class="fa fa-question-circle"></span>
                                        <?php echo $help['caption']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        </ul>

    </div>
</nav>
<!-- End menu bar //-->