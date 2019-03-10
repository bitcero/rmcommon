<?php foreach ($installed_mods as $module): ?>
<div class="module">
    <a style="background-image: url(<?php echo XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . ('' != $module->getInfo('icon48') ? $module->getInfo('icon48') : $module->getInfo('image')); ?>);" class="image" href="<?php if ($module->getVar('hasadmin')): echo XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getInfo('adminindex'); else: echo 'javascript:;'; endif; ?>"><span>&nbsp;</span></a>
    <span class="module_data">
        <strong><a href="<?php if ($module->getVar('hasadmin')): echo XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getInfo('adminindex'); else: echo 'javascript:;'; endif; ?>"><?php echo $module->getVar('name', 'E'); ?></a></strong><br>
        <?php echo sprintf(__('Version: %s', 'rmcommon'), '<strong>' . round($module->getVar('version') / 100, 2) . '</strong>'); ?><br>
        <a href="<?php echo XOOPS_URL; ?>/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=<?php echo $module->getVar('dirname'); ?>"><?php _e('Update', 'rmcommon'); ?></a>
        <?php if ($module->getVar('hasmain')): ?>
        | <a href="<?php echo XOOPS_URL; ?>/modules/<?php echo $module->getVar('dirname'); ?>"><?php _e('View', 'rmcommon'); ?></a>
        <?php endif; ?>
    </span>
</div>
<?php endforeach; ?><br clear="all">
<?php $nav->display(false); ?>
