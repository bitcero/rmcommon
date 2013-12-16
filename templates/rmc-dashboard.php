<h1 class="cu-section-title"><?php _e('Dashboard','rmcommon'); ?></h1>

<div class="row">

    <div class="col-md-4 col-lg-5">

        <!-- INstalled Modules -->
        <div class="cu-box box-collapse">
            <div class="box-header">
                <i class="icon-caret-up box-handler"></i>
                <h3><?php _e('Installed Modules','rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable">
                <div id="mods-list">
                    <div class="row">
                        <?php foreach($installed_modules as $mod): ?>
                            <div class="col-sm-4 col-md-6 col-lg-4">
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
                                            <a href="<?php echo $mod->config; ?>"><i class="icon-wrench"></i></a>
                                        <?php endif; ?>
                                        <?php if($mod->admin != ''): ?>
                                            <a href="<?php echo $mod->admin; ?>"><i class="icon-dashboard"></i></a>
                                        <?php endif; ?>
                                        <?php if($mod->admin != ''): ?>
                                            <a href="<?php echo $mod->admin; ?>"><i class="icon-home"></i></a>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <span class="description">
                    <?php _e('If you wish to manage or install new modules please go to Modules Management.','rmcommon'); ?><br />
                    <a href="<?php echo XOOPS_URL; ?>/modules/rmcommon/modules.php"><?php _e('Modules management', 'rmcommon'); ?></a>
                </span>
            </div>
        </div>

        <!-- System tools -->
        <div class="cu-box">
            <div class="box-header">
                <i class="icon-caret-up box-handler"></i>
                <h3><?php _e('System Tools','rmcommon'); ?></h3>
            </div>
            <div class="box-content system_tools collapsable">
                <div class="row">
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <a style="background-image: url(images/configure.png);" href="<?php echo XOOPS_URL; ?>/modules/system/admin.php?fct=preferences&op=showmod&mod=<?php echo $xoopsModule->mid(); ?>"><?php _e('Configure Common Utilities','rmcommon'); ?></a>
                    </div>
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <a style="background-image: url(images/images.png);" href="images.php"><?php _e('Images Manager','rmcommon'); ?></a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <a style="background-image: url(images/comments.png);" href="comments.php"><?php _e('Comments Management','rmcommon'); ?></a>
                    </div>
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <a style="background-image: url(images/plugin.png);" href="plugins.php"><?php _e('Plugins Management','rmcommon'); ?></a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <a style="background-image: url(images/modules.png);" href="modules.php"><?php _e('XOOPS Modules','rmcommon'); ?></a>
                    </div>
                    <div class="col-sm-6 col-md-12 col-lg-6">
                        <a style="background-image: url(images/users.png);" href="users.php"><?php _e('Users Management','rmcommon'); ?></a>
                    </div>
                </div>

                <?php
                $system_tools = RMEvents::get()->run_event('rmcommon.get.system.tools', array());
                $i = 1;
                ?>
                <?php if($system_tools): ?>
                    <div class="row">
                    <?php foreach ($system_tools as $tool): ?>
                        <?php if($i>2): ?>
                            </div><div class="row">
                            <?php $i=1; ?>
                        <?php endif; ?>
                        <div class="col-sm-6 col-md-12 col-lg-6"><a href="<?php echo $tool['link']; ?>" style="background-image: url(<?php echo $tool['icon']; ?>);"><?php echo $tool['caption']; ?></a></div>
                        <?php $i++; endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <!--// End system tools -->


    </div>

    <div class="col-md-4 col-lg-4">

        <!-- UPdates available -->
        <div class="alert alert-block" style="display: none;" id="updater-info">
            <h4><?php echo sprintf(__('%s Updates Available!','rmcommon'), '<span class="badge badge-important">%s</span>'); ?></h4>
            <p><?php echo sprintf(__('Please %s to view available updates.','rmcommon'), '<a href="updates.php">'.__('click here','rmcommon').'</a>'); ?></p>
        </div>

        <!-- Support me -->
        <div class="cu-box">
            <div class="box-header">
                <h3><i class="icon-thumbs-up"></i> <strong><?php _e('Support my Work','rmcommon'); ?></strong></h3>
            </div>
            <div class="box-content support-me">
                <img class="avatar" src="http://www.gravatar.com/avatar/a888698732624c0a1d4da48f1e5c6bb4?s=80" alt="Eduardo Cortés (bitcero)" />
                <p><?php _e('Do you like my work? Then maybe you want support me to continue developing new modules.','rmcommon'); ?></p>
                <?php echo $donateButton; ?>
            </div>
        </div>
        <!--// End support me -->

        <!-- Available Modules -->
        <div class="cu-box">
            <div class="box-header">
                <i class="icon-caret-up box-handler"></i>
                <h3><?php _e('Available Modules','rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable">
                <div class="mods-list">
                <?php foreach($available_mods as $module): ?>
                    <div class="<?php echo tpl_cycle("even,odd"); ?>">
                        <span class="modimg" style="background: url(../<?php echo $module->getInfo('dirname'); ?>/<?php echo $module->getInfo('icon32')!='' ? $module->getInfo('icon32') : $module->getInfo('image'); ?>) no-repeat center;">&nbsp;</span>
                        <strong><?php echo $module->getInfo('name'); ?></strong> <em><?php echo is_array($module->getInfo('rmversion')) ? RMModules::format_module_version( $module->getInfo('rmversion') ) : $module->getInfo('version'); ?></em><br />
                        <span class="moddesc"><?php echo $module->getInfo('description'); ?></span><br />
                        <a href="modules.php?action=install&dir=<?php echo $module->getInfo('dirname'); ?>" class="btn btn-warning btn-sm"><?php _e('Install', 'rmcommon'); ?></a>
                    </div>
                <?php endforeach; ?>
                </div>
                <span class="help-block">
	                <?php _e('If you wish to manage or install new modules please go to Modules Management.','rmcommon'); ?><br />
	                <a href="modules.php" class="btn btn-info"><?php _e('Modules management', 'rmcommon'); ?></a>
	            </span>
            </div>
        </div>
        <!-- End available modules -->

    </div>

    <div class="col-md-4 col-lg-3">

        <!-- Recent News -->
        <div class="cu-box">
            <div class="box-header">
                <i class="icon-caret-up box-handler"></i>
                <img src="images/loading_2.gif" alt="" class="loading" id="loading-news" />
                <h3><?php _e('Recent News','rmcommon'); ?></h3>
            </div>
            <div class="box-content collapsable" id="rmc-recent-news">

            </div>
        </div>
        <!--// End recent news -->

    </div>

</div>

<div class="row rmcw-container">
    <!-- Left widgets -->
    <div class="span6">


        <!-- Recent news -->

        <!-- End recent news -->

        <?php RMEvents::get()->run_event('rmcommon.dashboard.left.widgets'); ?>

    </div>
    <!-- End left widgets -->

    <!-- Right widgets -->
    <div class="span6" id="rmc-central-right-widgets">






        <?php RMEvents::get()->run_event('rmcommon.dashboard.right.widgets'); ?>
    </div>
    <!-- / End right widgets -->
</div>