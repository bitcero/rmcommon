<div id="he-sidebar">
    <div class="media he-current-user">
        <div class="media-left">
            <?php if ($cuServices->service('avatar')): ?>
            <img src="<?php echo $cuServices->avatar->getAvatarSrc($xoopsUser, 100); ?>" class="media-object"
                 alt="<?php echo '' != $xoopsUser->getVar('name') ? $xoopsUser->getVar('name') : $xoopsUser->getVar('uname'); ?>">
            <?php else: ?>
            <img class="media-object" src="<?php echo HELIUM_URL; ?>/images/avatar.png" alt="<?php echo '' != $xoopsUser->getVar('name') ? $xoopsUser->getVar('name') : $xoopsUser->getVar('uname'); ?>">
            <?php endif; ?>
        </div>
        <div class="media-body">
            <h4 class="media-heading">
                <?php echo '' != $xoopsUser->getVar('name') ? $xoopsUser->getVar('name') : $xoopsUser->getVar('uname'); ?>
            </h4>
            <span><?php echo $userRank['title']; ?></span>
        </div>
    </div>

    <div class="module-search">
        <input type="text" value="" class="form-control" id="filter-module" placeholder="<?php _e('Search modules...', 'rmcommon'); ?>">
    </div>

    <div class="menu-wrapper">
        <div class="sidebar-menu">

            <!-- CURRENT MODULE MENU -->
            <?php if (isset($currentModule) && !empty($currentModule)): ?>
                <h4 class="menu-heading current-module-head">
                    <a href="#menu-<?php echo $currentModule->directory; ?>"><?php echo $currentModule->name; ?></a>
                </h4>
                <?php if (!empty($currentModule->menu)): ?>
                <ul id="menu-<?php echo $currentModule->directory; ?>" class="current-module-menu">
                    <?php foreach ($currentModule->menu as $menu): ?>
                        <li<?php echo $menu['location'] == $common->location ? ' class="active"' : ''; ?>>
                            <a <?php echo isset($menu['attributes']) ? $xoFunc->render_attributes($menu['attributes']) : ''; ?> href="<?php echo $xoFunc->menuLink((object) $menu, $currentModule); ?>"<?php echo $menu['location'] == $common->location ? ' class="open"' : ''; ?> <?php if (array_key_exists('options', $menu) && !empty($menu['options'])): ?> data-submenu="yes"<?php endif; ?>>
                                <?php echo $xoFunc->menuIcon($menu['icon'], $currentModule->directory); ?>
                                <?php echo $menu['title']; ?>
                                <?php if (array_key_exists('options', $menu) && !empty($menu['options'])): ?>
                                    <span class="caret"></span>
                                <?php endif; ?>
                            </a>
                            <?php if (array_key_exists('options', $menu) && !empty($menu['options'])): ?>
                                <ul class="submenu"<?php echo $menu['location'] == $common->location ? ' style="display: block;"' : ''; ?>>
                                    <?php foreach ($menu['options'] as $submenu): ?>
                                        <?php if (array_key_exists('divider', $submenu)) {
    continue;
} ?>
                                        <li>
                                            <a
                                                href="<?php echo $xoFunc->menuLink((object) $submenu, $currentModule); ?>"
                                                <?php echo isset($submenu['attributes']) ? $xoFunc->render_attributes($submenu['attributes']) : ''; ?>>
                                                <?php echo isset($submenu['icon']) ? $xoFunc->menuIcon($submenu['icon'], $currentModule->directory) : ''; ?>
                                                <?php echo $submenu['title']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            <?php endif; ?>

            <!-- INSTALLED MODULES MENU -->
            <h4 class="menu-heading modules-menu-head">
                <a href="#menu-modules"><?php _e('Modules', 'rmcommon'); ?></a>
            </h4>
            <?php if (!empty($activeModules)): ?>
                <ul id="menu-modules">
                    <?php foreach ($activeModules as $module): ?>
                        <li>
                            <a href="<?php echo RMUris::anchor($module->directory); ?>"<?php if (!empty($module->menu)): ?> data-submenu="yes"<?php endif; ?>>
                                <?php echo $xoFunc->menuIcon($module->icon, $module->directory); ?>
                                <?php echo $module->name; ?>
                                <?php if (!empty($module->menu)): ?>
                                    <span class="caret"></span>
                                <?php endif; ?>
                            </a>
                            <?php if (!empty($module->menu)): ?>
                                <ul class="submenu">
                                    <?php foreach ($module->menu as $menu): ?>
                                        <?php if (array_key_exists('divider', $menu)) {
    continue;
} ?>
                                        <li>
                                            <a href="<?php echo $xoFunc->menuLink((object) $menu, $module); ?>">
                                                <?php echo $xoFunc->menuIcon($menu['icon'], $module->directory); ?>
                                                <?php echo $menu['title']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        </div>
    </div>

</div>