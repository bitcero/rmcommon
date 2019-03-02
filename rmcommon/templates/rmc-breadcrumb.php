<?php global $xoopsModule; ?>
<?php $total_crumbs = count($this->crumbs); ?>
<?php if ($total_crumbs > 0): ?>
<!-- Breadcrumb -->
    <ul class="breadcrumb rmc-breadcrumb">
        <?php if (defined('XOOPS_CPFUNC_LOADED')): ?>
            <li><a href="<?php echo RMCURL; ?>" title="<?php _e('Dashboard', 'rmcommon'); ?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <?php if ($xoopsModule->dirname()!='rmcommon'): ?>
                <li>
                    <a href="<?php echo $xoopsModule->hasadmin() ? XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/'.$xoopsModule->getInfo('adminindex') : ''; ?>">
                        <strong><?php echo $xoopsModule->name(); ?></strong>
                    </a>
                </li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="<?php echo XOOPS_URL; ?>" title="<?php _e('Home', 'rmcommon'); ?>"><span class="fa fa-home"></span></a></li>
            <?php if ($xoopsModule && $xoopsModule->getInfo('hasMain')): ?>
                <li>
                    <a href="<?php echo $controller == false ? XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname') . '/' : $controller->get_main_link(); ?>">
                        <?php echo $xoopsModule->name(); ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php foreach ($this->crumbs as $i => $item): ?>
            <?php if ($item['link']!=''): ?>
                <li>
                    <a href="<?php echo $item['link']; ?>">
                        <?php if ($item['icon'] != ''): ?>
                        <span class="<?php echo $item['icon']; ?>"></span>
                        <?php endif; ?>
                        <?php echo $item['caption']; ?>
                    </a>
                </li>
            <?php else: ?>
                <li class="active">
                    <?php if ($item['icon'] != ''): ?>
                        <span class="<?php echo $item['icon']; ?>"></span>
                    <?php endif; ?>
                    <?php echo $item['caption']; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

<!--// ENd breadcrumb -->
<?php endif; ?>
