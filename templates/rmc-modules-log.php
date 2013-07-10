<h1 class="rmc_titles"><?php echo $log_title; ?></h1>
<div class="mod_options">
    <a href="modules.php"><?php _e('Go to Modules Management','rmcommon'); ?></a>
    <?php if($action!='uninstall_module' && $module): ?>
        <?php if($module->getVar('hasmain')): ?>
            | <a href="<?php echo XOOPS_URL; ?>/modules/<?php echo $module->dirname(); ?>/<?php echo $module->getInfo('adminindex'); ?>"><?php echo sprintf(__('%s Control Panel','rmcommon'), $module->name()); ?></a>
        <?php endif; ?>
    <?php endif; ?>
</div>
<div class="mod_log rounded">
    <?php echo $module_log; ?>
</div>
<div class="mod_options">
    <a href="modules.php"><?php _e('Go to Modules Management','rmcommon'); ?></a>
    <?php if($action!='uninstall_module' && $module): ?>
        <?php if($module->getVar('hasmain')): ?>
            | <a href="<?php echo XOOPS_URL; ?>/modules/<?php echo $module->dirname(); ?>/<?php echo $module->getInfo('adminindex'); ?>"><?php echo sprintf(__('%s Control Panel','rmcommon'), $module->name()); ?></a>
        <?php endif; ?>
    <?php endif; ?>
</div>