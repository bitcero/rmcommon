<!-- Menu bar -->
<nav role="navigation" id="li-topbar">

    <div class="li-topbar-collapse d-flex justify-content-between align-items-center">

        <ul class="main-options d-flex align-items-center">

            <!-- Toggel sidebar -->
            <li>
                <a href="#" class="toggle-menu">
                    <?php echo $cuIcons->getIcon('fa fa-bars'); ?>
                </a>
            </li>

            <!-- Common Utilities Menu -->
            <li class="dropdown<?php if ('rmcommon' == $xoopsModule->dirname()): ?> active<?php endif; ?> rmcommon-menu">
                <a href="<?php echo RMCURL; ?>" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-rmcommon', [], false); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($rmcommon_menu['menu'] as $menu): ?>
                        <?php if (isset($menu['divider'])): ?>
                            <li class="divider"></li>
                        <?php else: ?>
                            <li<?php if (isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                <a
                                    class="dropdown-item"
                                    href="<?php echo $xoFunc->menuLink((object) $menu, (object) $rmcommon_menu); ?>"<?php if (isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>
                                    <?php if (array_key_exists('attributes', $menu)): ?><?php echo $xoFunc->render_attributes($menu['attributes']); ?><?php endif; ?>
                                    >
                                    <span class="d-flex align-items-center justify-content-start">
                                        <?php echo $cuIcons->getIcon($menu['icon'], [], false); ?>
                                        <?php echo $menu['title']; ?>
                                    </span>
                                </a>
                                <?php if (isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($menu['options'] as $sub): ?>
                                            <?php if (isset($sub['divider'])): ?>
                                                <li class="divider"></li>
                                                <?php continue; endif; ?>
                                            <li>
                                                <span class="d-flex align-items-center justify-content-start">
                                                    <a class="dropdown-item" href="<?php echo $xoFunc->menuLink((object) $sub, (object) $rmcommon_menu); ?>">
                                                        <?php echo $cuIcons->getIcon($sub['icon'], [], false); ?>
                                                        <?php echo $sub['title']; ?>
                                                    </a>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </li>

            <?php if ($other_menu): ?>
                <?php foreach ($other_menu as $item): ?>
                    <li class="dropdown <?php echo $item['class']; ?>">
                        <a href="<?php echo $item['link']; ?>" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"<?php echo array_key_exists('title', $item) ? ' title="' . $item['title'] . '"' : ''; ?>>
                            <?php echo array_key_exists('icon', $item) ? $cuIcons->getIcon($item['icon']) : ''; ?>
                            <?php if (array_key_exists('caption', $item)): ?>
                            <span class="caption"><?php echo $item['caption']; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($item['menu'] as $menu): ?>
                                <?php if (isset($menu['divider'])): ?>
                                    <li class="divider"></li>
                                <?php else: ?>
                                    <li<?php if (isset($menu['options'])): ?> class="dropdown-submenu"<?php endif; ?>>
                                        <a
                                            href="<?php echo $menu['link']; ?>"<?php if (isset($menu['options'])): ?> tabindex="-1"<?php endif; ?>
                                            <?php if (array_key_exists('attributes', $menu)): ?><?php echo $xoFunc->render_attributes($menu['attributes']); ?><?php endif; ?>
                                        >
                                            <?php echo $cuIcons->getIcon($menu['icon']); ?>
                                            <?php echo $menu['title']; ?>
                                        </a>
                                        <?php if (isset($menu['options'])): ?>
                                            <ul class="dropdown-menu">
                                                <?php foreach ($menu['options'] as $sub): ?>
                                                    <?php if (isset($sub['divider'])): ?>
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
                    <?php echo sprintf(__('%s UPDATES', 'rmcommon'), '<span class="badge badge-warning">%s</span>'); ?>
                </a>
            </li>
        </ul>

        <ul class="right-options d-flex align-items-center">

            <li id="updater-info-top">
                <a href="<?php echo RMUris::relative_url(RMCURL . '/updates.php'); ?>">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-update', [], false); ?>
                    <em>%s</em>
                </a>
            </li>

            <li class="dropdown<?php if ('system' == $xoopsModule->dirname()): ?> active<?php endif; ?> system-menu">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-gear', [], false); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach ($system_menu['menu'] as $menu): ?>
                        <?php if (isset($menu['divider'])): ?>
                            <li class="divider"></li>
                        <?php else: ?>
                            <li>
                                <a
                                    class="dropdown-item"
                                    href="<?php echo $xoFunc->menuLink((object)$menu, (object) $system_menu); ?>">
                                    <span>
                                        <?php echo $cuIcons->getIcon($xoFunc->getSystemIcon($menu), [], false); ?>
                                        <?php echo $menu['title']; ?>
                                    </span>
                                </a>
                                <?php if (isset($menu['options'])): ?>
                                    <ul class="dropdown-menu">
                                        <?php foreach ($menu['options'] as $sub): ?>
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="<?php echo $xoFunc->menuLink((object) $sub, (object)  $system_menu); ?>">
                                                    <?php echo $cuIcons->getIcon($sub['icon'], [], false); ?>
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
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $cuIcons->getIcon('svg-rmcommon-lightning', [], false); ?>
                </a>
                <ul class="dropdown-menu">
                    <?php if ($xoopsModule->getInfo('social')): ?>
                        <li class="dropdown-submenu">
                            <a class="dropdown-item" href="#" tabindex="-1" onclick="return false;">
                                <span>
                                    <?php echo $cuIcons->getIcon('svg-rmcommon-users', [], false); ?>
                                    <?php _e('Social Links', 'rmcommon'); ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($xoopsModule->getInfo('social') as $net): ?>
                                    <li class="nav_item">
                                        <a class="dropdown-item" href="<?php echo $net['url']; ?>" target="_blank">
                                            <?php if (isset($net['type'])): ?>
                                                <?php echo $cuIcons->getIcon('fa fa-' . $net['type'], [], false); ?>
                                            <?php endif; ?>
                                            <?php echo $net['title']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo RMCURL; ?>">
                            <span>
                                <?php echo $cuIcons->getIcon('svg-rmcommon-dashboard', [], false); ?>
                                <?php _e('Control Panel', 'rmcommon'); ?>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?php echo XOOPS_URL; ?>" target="_blank">
                            <span>
                                <?php echo $cuIcons->getIcon('svg-rmcommon-home', [], false); ?>
                                <?php _e('View Home Page', 'rmcommon'); ?>
                            </span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a class="dropdown-item" href="https://www.xoops.org" target="_blank">
                            <span>
                                <?php echo $cuIcons->getIcon('svg-rmcommon-xoops', [], false); ?>
                                XOOPS.org
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="http://www.eduardocortes.mx" target="_blank">
                            <span>
                                <?php echo $cuIcons->getIcon('svg-rmcommon-bitcero', [], false); ?>
                                <?php _e('Author Website', 'rmcommon'); ?>
                            </span>
                        </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?php echo RMUris::relative_url(XOOPS_URL . '/user.php?op=logout'); ?>">
                            <span>
                                <?php echo $cuIcons->getIcon('svg-rmcommon-power-off text-danger', [], false); ?>
                                <?php _e('Close Session', 'lithium'); ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php if (!empty($helpLinks)): ?>
                <li class="help-menu<?php if (1 == count($helpLinks)): ?> dropdown<?php endif; ?>">
                    <?php if (1 == count($helpLinks)): ?>
                        <a href="<?php echo $helpLinks[0]['link']; ?>" data-action="help">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
                        </a>
                    <?php else: ?>
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $cuIcons->getIcon('svg-rmcommon-question'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($helpLinks as $help): ?>
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

            <li class="dropdown user-menu">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                  <?php echo $cuIcons->getIcon('svg-rmcommon-user-circle', [], false); ?>
                </a>
            </li>
        </ul>

    </div>
</nav>
<!-- End menu bar //-->