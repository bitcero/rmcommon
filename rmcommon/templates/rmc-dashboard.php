<h1 class="cu-section-title"><?php _e('Dashboard','rmcommon'); ?></h1>

<div class="row" data-news="load" data-boxes="load" data-module="rmcommon" data-target="#rmc-recent-news">

    <div class="col-md-4 col-lg-5" data-box="rmcommon-box-left">

        <!-- INstalled Modules -->
        <div class="cu-box box-collapse">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('Installed Modules','rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable">
                <div id="mods-list">
                    <div class="row">
                        <?php foreach($installed_modules as $mod): ?>
                            <div class="col-xs-6 col-sm-4 col-md-6 col-lg-4">
                                <div class="installed-item">
                                    <a href="<?php echo $mod->admin != '' ? $mod->admin : $mod->main; ?>" class="icon" title="<?php $mod->real_name; ?>">
                                        <img src="<?php echo $mod->logo; ?>">
                                    </a>
                                    <span class="name">
                                        <a href="<?php echo $mod->admin != '' ? $mod->admin : $mod->main; ?>" class="name"><?php echo $mod->name; ?></a>
                                    </span>
                                    <span class="version">
                                        <?php echo $mod->version; ?>
                                    </span>
                                    <span class="options">
                                        <?php if($mod->config != ''): ?>
                                            <a href="<?php echo $mod->config; ?>"><i class="fa fa-wrench"></i></a>
                                        <?php endif; ?>
                                        <?php if($mod->admin != ''): ?>
                                            <a href="<?php echo $mod->admin; ?>"><i class="fa fa-dashboard"></i></a>
                                        <?php endif; ?>
                                        <?php if($mod->admin != ''): ?>
                                            <a href="<?php echo $mod->admin; ?>"><i class="fa fa-home"></i></a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>


        <!-- Recent News -->
        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('Recent News','rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable" id="rmc-recent-news">

            </div>
        </div>
        <!--// End recent news -->

        <?php RMEvents::get()->run_event('rmcommon.dashboard.left.widgets'); ?>

    </div>

    <div class="col-md-4 col-lg-4" data-box="rmcommon-box-center">

        <div class="cu-box" id="updater-info" style="display: none;">
            <div class="box-header">
                <h3><?php echo sprintf(__('%s Updates Available!','rmcommon'), '<span class="badge badge-warning">%s</span>'); ?></h3>
            </div>
            <div class="box-content">
                <p class="text-warning"><?php echo sprintf(__('Please %s to view available updates.','rmcommon'), '<a href="updates.php">'.__('click here','rmcommon').'</a>'); ?></p>
            </div>
        </div>
        <!-- UPdates available -->

        <!-- Available Modules -->
        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('Available Modules','rmcommon'); ?></h3>
            </div>
            <div class="box-content">
                <ul class="available-list list-unstyled">
                <?php foreach($available_mods as $module): ?>
                    <li>
                        <img class="module-icon" src="../<?php echo $module->getInfo('dirname'); ?>/<?php echo $module->getInfo('icon48')!='' ? $module->getInfo('icon48') : $module->getInfo('image'); ?>" alt="<?php echo $module->getInfo('name'); ?>">
                        <strong><?php echo $module->getInfo('name'); ?></strong>
                        <small><?php echo is_array($module->getInfo('rmversion')) ? RMModules::format_module_version( $module->getInfo('rmversion') ) : $module->getInfo('version'); ?></small><br />
                        <span class="help-block"><small><?php echo $module->getInfo('description'); ?></small></span>
                        <a href="modules.php?action=install&dir=<?php echo $module->getInfo('dirname'); ?>" class="btn btn-warning btn-sm"><?php _e('Install', 'rmcommon'); ?></a>
                    </li>
                <?php endforeach; ?>
                </ul>
                <span class="help-block">
	                <span class="fa fa-info-circle text-info"></span> <?php _e('If you wish to manage or install new modules please go to Modules Management.','rmcommon'); ?><br />
	            </span>
                <a href="modules.php" class="btn btn-info"><?php _e('Modules management', 'rmcommon'); ?></a>
            </div>
        </div>
        <!-- End available modules -->

        <?php RMEvents::get()->run_event('rmcommon.dashboard.center.widgets'); ?>

    </div>

    <div class="col-md-4 col-lg-3" data-box="rmcommon-box-right">

        <!-- System tools -->
        <div class="cu-box">
            <div class="box-header">
                <span class="fa fa-caret-up box-handler"></span>
                <h3><?php _e('System Tools','rmcommon'); ?></h3>
            </div>
            <div class="box-content system-tools collapsable">
                <ul class="nav nav-pills nav-stacked">
                    <li>
                        <a href="<?php echo RMCURL; ?>/settings.php?action=configure&amp;&mod=<?php echo $xoopsModule->mid(); ?>">
                            <img src="images/configure.png" alt="<?php _e('Configure Common Utilities','rmcommon'); ?>">
                            <?php _e('Configure Common Utilities','rmcommon'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="images.php">
                            <img src="images/images.png" alt="<?php _e('Images Manager','rmcommon'); ?>">
                            <?php _e('Images Manager','rmcommon'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="comments.php">
                            <img src="images/comments.png" alt="<?php _e('Comments Management','rmcommon'); ?>">
                            <?php _e('Comments Management','rmcommon'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="plugins.php">
                            <img src="images/plugin.png" alt="<?php _e('Plugins Management','rmcommon'); ?>">
                            <?php _e('Plugins Management','rmcommon'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="modules.php">
                            <img src="images/modules.png" alt="<?php _e('XOOPS Modules','rmcommon'); ?>">
                            <?php _e('XOOPS Modules','rmcommon'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="users.php">
                            <img src="images/users.png" alt="<?php _e('Users Management','rmcommon'); ?>">
                            <?php _e('Users Management','rmcommon'); ?>
                        </a>
                    </li>
                    <?php
                    $system_tools = RMEvents::get()->run_event('rmcommon.get.system.tools', array());
                    $i = 1;
                    ?>
                    <?php if($system_tools): ?>
                        <?php foreach ($system_tools as $tool): ?>
                            <li>
                                <a href="<?php echo $tool['link']; ?>">
                                    <img src="<?php echo $tool['icon']; ?>" alt="<?php echo $tool['caption']; ?>">
                                    <?php echo $tool['caption']; ?>
                                </a>
                            </li>
                            <?php $i++; endforeach; ?>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
        <!--// End system tools -->

        <?php RMEvents::get()->run_event('rmcommon.dashboard.right.widgets'); ?>

    </div>

</div>
