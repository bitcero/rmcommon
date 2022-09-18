<div id="li-sidebar" class="show">
    <div class="theme-logo">
        <div class="d-flex align-items-center justify-content-between">
            <div class="logo">
                <a href="<?php echo $common->url(); ?>">
                  <?php echo $logoLithium; ?>
                </a>
            </div>
            <div class="sidebar-toggle">
                <a href="#" class="btn btn-link" id="toggle-sidebar">
                    <span class="visually-hidden">Toggle sidebar</span>
                </a>
            </div>
        </div>
    </div>

    <div class="menu-wrapper">
        <div class="module-search">
            <input type="text" value="" class="form-control" id="filter-module" placeholder="<?php _e('Search modules...', 'lithium'); ?>">
        </div>

        <div class="sidebar-menu">

            <!-- CURRENT MODULE MENU -->
            <?php if (isset($currentModule) && !empty($currentModule)): ?>
                <h4 class="menu-heading current-module-head">
                    <a href="#menu-<?php echo $currentModule->directory; ?>" class="text-truncate"><?php echo $currentModule->name; ?></a>
                </h4>
                <?php if (!empty($currentModule->menu)): ?>
                <ul id="menu-<?php echo $currentModule->directory; ?>" class="current-module-menu">
                    <?php foreach ($currentModule->menu as $menu): ?>
                        <li<?php echo $menu['location'] == $common->location ? ' class="active"' : ''; ?>>
                            <a
                                <?php echo isset($menu['attributes']) ? $xoFunc->render_attributes($menu['attributes']) : ''; ?>
                                href="<?php echo $xoFunc->menuLink((object) $menu, $currentModule); ?>"
                                class="d-flex align-items-center justify-content-between <?php echo $menu['location'] == $common->location ? 'open' : ''; ?>"
                                <?php if (array_key_exists('options', $menu) && !empty($menu['options'])): ?>
                                data-submenu="yes"<?php endif; ?>
                            >
                                <span class="d-flex align-items-center justify-content-start">
                                    <?php echo $xoFunc->menuIcon($menu['icon'], $currentModule->directory); ?>
                                    <span class="menu-title text-truncate"><?php echo $menu['title']; ?></span>
                                </span>
                                <?php if (array_key_exists('options', $menu) && !empty($menu['options'])): ?>
                                  <?php echo $common->icons()->getIcon('svg-lithium-angle-right', [], false); ?>
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
                                                <?php echo isset($submenu['attributes']) ? $xoFunc->render_attributes($submenu['attributes']) : ''; ?>
                                                class="d-flex align-items-center justify-content-between"
                                            >
                                              <span class="d-flex align-items-center justify-content-start">
                                                <?php echo isset($submenu['icon']) ? $xoFunc->menuIcon($submenu['icon'], $currentModule->directory) : ''; ?>
                                                  <span class="menu-title text-truncate"><?php echo $submenu['title']; ?></span>
                                              </span>
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
                <a href="#menu-modules" class="text-truncate"><?php _e('Modules', 'rmcommon'); ?></a>
            </h4>
            <?php if (!empty($activeModules)): ?>
                <ul id="menu-modules">
                    <?php foreach ($activeModules as $module): ?>
                        <li>
                            <a
                                href="<?php echo RMUris::anchor($module->directory); ?>"
                                <?php if (!empty($module->menu)): ?>
                                data-submenu="yes"<?php endif; ?>
                                class="d-flex align-items-center justify-content-between"
                            >
                                <span class="d-flex align-items-center justify-content-start">
                                    <?php echo $xoFunc->menuIcon($module->icon, $module->directory); ?>
                                    <span class="menu-title text-truncate"><?php echo $module->name; ?></span>
                                </span>
                                <?php if (!empty($module->menu)): ?>
                                  <?php echo $common->icons()->getIcon('svg-lithium-angle-right', [], false); ?>
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