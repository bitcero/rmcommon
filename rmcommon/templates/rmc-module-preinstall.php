<h1 class="cu-section-title"><?php echo sprintf(__('Install %s','rmcommon'), $module->getInfo('name')); ?></h1>

<div class="help-block"><?php _e('This module will make next changes in Xoops system. Please review in a detailed way all them in order to decide if you really wish to install this module','rmcommon'); ?></div>

<div class="row mod_preinstall_container">

    <div class="col-sm-6 col-md-5 col-lg-5">
        <div class="cu-box">

            <div class="box-header">
                <h3><?php echo sprintf(__('%s Details','rmcommon'), $module->getInfo('name')); ?></h3>
            </div>

            <div class="box-content">
                <table class="table table-module-details">
                    <tr class="even">
                        <td rowspan="2" class="head"><img src="<?php echo XOOPS_URL; ?>/modules/<?php echo $module->getInfo('dirname'); ?>/<?php echo $module->getInfo('image'); ?>" alt="<?php echo $module->getInfo('name'); ?>" /></td>
                        <td><strong><?php _e('Name:','rmcommon'); ?></strong></td>
                        <td>
                            <?php if( $module->getInfo('url') ): ?>
                                <a href="<?php echo $module->getInfo('url'); ?>" target="_blank">
                                    <?php echo $module->getInfo('name'); ?>
                                </a>
                            <?php else: ?>
                                <?php echo $module->getInfo('name'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="odd">
                        <td><strong><?php _e('Version:','rmcommon'); ?></strong>
                        <td><?php echo $module->getInfo('rmnative') ? RMModules::format_module_version($module->getInfo('rmversion')) : $module->getInfo('version'); ?></td>
                    </tr>
                    <tr class="even">
                        <td><strong><?php _e('Author:','rmcommon'); ?></strong></td>
                        <td colspan="2">
                            <?php if($module->getInfo('authorurl')): ?>
                                <a href="<?php echo $module->getInfo('authorurl'); ?>"><?php echo $module->getInfo('author'); ?></a>
                            <?php else: ?>
                                <?php echo $module->getInfo('author'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if($module->getInfo('rmnative')): ?>
                        <tr class="odd">
                            <td><strong><?php _e('Author Web:','rmcommon'); ?></strong></td>
                            <td colspan="2"><a href="<?php echo $module->getInfo('authorurl'); ?>" target="_blank"><?php echo $module->getInfo('authorweb'); ?></a></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="even">
                        <td><strong><?php _e('Description:','rmcommon'); ?></strong></td>
                        <td colspan="2"><?php echo $module->getInfo('description'); ?></td>
                    </tr>
                    <tr class="odd">
                        <td><strong><?php _e('Credits:','rmcommon'); ?></strong></td>
                        <td colspan="2"><?php echo $module->getInfo('credits'); ?></td>
                    </tr>
                    <tr class="even">
                        <td><strong><?php _e('License:','rmcommon'); ?></strong></td>
                        <td colspan="2"><?php echo $module->getInfo('license'); ?></td>
                    </tr>
                    <tr class="odd">
                        <td><strong><?php _e('Help:','rmcommon'); ?></strong></td>
                        <td colspan="2">
                            <?php if($module->getInfo('help')!=''): ?>
                                <a href="<?php echo preg_match("/http:|https:/is", $module->getInfo('help')) ? $module->getInfo('help') : XOOPS_URL . '/modules/' . $module->getInfo( 'dirname' ) . '/' . $module->getInfo('help'); ?>" target="_blank"><?php _e('Click here','rmcommon'); ?></a><?php endif; ?></td>
                    </tr>
                </table>
                <button type="submit" id="install-ok" class="btn btn-primary btn-lg"><span class="fa fa-check"></span> <?php _e('Install Now','rmcommon'); ?></button>
                <button type="button" onclick="window.location = 'modules.php';" class="btn btn-default btn-lg"><?php _e('Cancel','rmcommon'); ?></button>
                <form method="post" id="install-form" action="modules.php">
                    <input name="module" value="<?php echo $module->getInfo('dirname'); ?>" type="hidden">
                    <input name="action" value="install_now" type="hidden">
                    <?php echo $xoopsSecurity->getTokenHTML(); ?>
                </form>
            </div>

        </div>
    </div>

    <div class="col-sm-6 col-md-7 col-lg-7">

        <?php if($module->getInfo('templates')): ?>
        <div class="cu-box">

            <div class="box-header">
                <span class="fa fa-caret-down box-handler"></span>
                <h3><?php _e('Module Templates','rmcommon'); ?> (<?php echo count($module->getInfo('templates')); ?>)</h3>
            </div>

            <div class="box-content" style="display: none;">
                <ol>
                    <?php foreach($module->getInfo('templates') as $tpl): ?>
                        <div class="<?php echo tpl_cycle("even,odd"); ?>">
                            <li><?php echo $tpl['file']; ?>
                                <?php if($tpl['description']!=''): ?><span class="help-block"><small><?php echo defined($tpl['description']) ? constant($tpl['description']) : $tpl['description']; ?></small></span><?php endif; ?></li>
                        </div>
                    <?php endforeach; ?>
                </ol>
            </div>

        </div>
        <?php endif; ?>

        <?php if($module->getInfo('tables')): ?>
        <div class="cu-box">

            <div class="box-header">
                <span class="fa fa-caret-down box-handler"></span>
                <h3><?php _e('Database Tables','rmcommon'); ?> (<?php echo count($module->getInfo('tables')); ?>)</h3>
            </div>

            <div class="box-content" style="display: none;">
                <ol>
                    <?php foreach($module->getInfo('tables') as $table): ?>
                        <div class="<?php echo tpl_cycle("even,odd"); ?>">
                            <li><?php echo $table; ?></li>
                        </div>
                    <?php endforeach; ?>
                </ol>
            </div>

        </div>
        <?php endif; ?>

        <?php if($module->getInfo('config')): ?>
        <div class="cu-box">

            <div class="box-header">
                <span class="fa fa-caret-down box-handler"></span>
                <h3><?php _e('Option settings to insert','rmcommon'); ?> (<?php echo count($module->getInfo('config')); ?>)</h3>
            </div>

            <div class="box-content" style="display: none;">
                <ol>
                    <?php foreach($module->getInfo('config') as $item): ?>
                        <div class="<?php echo tpl_cycle("even,odd"); ?>">
                            <li><strong><?php echo defined($item['title']) ? constant($item['title']) : $item['title']; ?></strong>
                                <?php if($item['description']!=''): ?><span class="help-block"><small><?php echo defined($item['description']) ? constant($item['description']) : $item['description']; ?></small></span><?php endif; ?></li>
                        </div>
                    <?php endforeach; ?>
                </ol>
            </div>

        </div>
        <?php endif; ?>

        <?php if($module->getInfo('blocks')): ?>
        <div class="cu-box">

            <div class="box-header">
                <span class="fa fa-caret-down box-handler"></span>
                <h3><?php _e('Bloks to insert','rmcommon'); ?> (<?php echo count($module->getInfo('blocks')); ?>)</h3>
            </div>

            <div class="box-content" style="display: none;">
                <ol>
                    <?php foreach($module->getInfo('blocks') as $item): ?>
                        <div class="<?php echo tpl_cycle("even,odd"); ?>">
                            <li><strong><?php echo defined($item['name']) ? constant($item['name']) : $item['name']; ?></strong><br />
                                <?php if($item['description']!=''): ?><span class="help-block"><small><?php echo defined($item['description']) ? constant($item['description']) : $item['description']; ?></small></span><?php endif; ?></li>
                        </div>
                    <?php endforeach; ?>
                </ol>
            </div>

        </div>
        <?php endif; ?>

    </div>

</div>
