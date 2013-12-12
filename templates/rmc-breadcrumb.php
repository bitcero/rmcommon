<?php global $xoopsModule; ?>
<?php $total_crumbs = count($this->crumbs); ?>
<?php if ( $total_crumbs > 0 ): ?>
<!-- Breadcrumb -->
<div class="container">
    <ul class="breadcrumb rmc-breadcrumb">
        <li><a href="<?php echo RMCURL; ?>" title="<?php _e('Dashboard','rmcommon'); ?>"><i class="icon-dashboard"></i></a></li>
        <?php if($xoopsModule->dirname()!='rmcommon'): ?>
            <li>
                <a href="<?php echo $xoopsModule->hasadmin() ? XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/'.$xoopsModule->getInfo('adminindex') : ''; ?>">
                    <?php echo $xoopsModule->name(); ?>
                </a>
            </li>
        <?php endif; ?>
        <?php foreach($this->crumbs as $i => $item): ?>
            <?php if($item['link']!=''): ?>
                <li>
                    <a href="<?php echo $item['link']; ?>"><?php echo $item['caption']; ?></a>
                </li>
            <?php else: ?>
                <li class="active"><?php echo $item['caption']; ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
<!--// ENd breadcrumb -->
<?php endif; ?>