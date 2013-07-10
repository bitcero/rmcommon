<?php global $xoopsModule; ?>
<?php $total_crumbs = count($this->crumbs); ?>
<!-- Breadcrumb -->
<div class="container-fluid">
    <ul class="breadcrumb rmc-breadcrumb">
        <li><span class="indicator"><?php _e('You are here:','twop6'); ?></span></li>
        <li><a href="<?php echo RMCURL; ?>" title="<?php _e('Dashboard','rmcommon'); ?>"><i class="icon-dashboard"></i></a> <span class="divider">/</span></li>
        <?php if($xoopsModule->dirname()!='rmcommon'): ?>
            <li>
                <a href="<?php echo $xoopsModule->hasadmin() ? XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/'.$xoopsModule->getInfo('adminindex') : ''; ?>">
                    <?php echo $xoopsModule->name(); ?>
                </a>
                <?php if($this->crumbs): ?>
                    <span class="divider">/</span>
                <?php endif; ?>
            </li>
        <?php endif; ?>
        <?php foreach($this->crumbs as $i => $item): ?>
            <?php if($item['link']!=''): ?>
                <li>
                    <a href="<?php echo $item['link']; ?>"><?php echo $item['caption']; ?></a>
                    <?php if($i<$total_crumbs-1): ?>
                        <span class="divider">/</span>
                    <?php endif; ?>
                </li>
            <?php else: ?>
                <li class="active"><?php echo $item['caption']; ?></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
<!--// ENd breadcrumb -->