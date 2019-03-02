<h1 class="cu-section-title"><?php _e('Categories', 'rmcommon'); ?></h1>

<form name="frmimgcats" id="form-img-cats" method="post" action="images.php" class="form-inline">
<div class="cu-bulk-actions">
<?php echo $nav->render(); ?>
<select name="action" class="form-control">
	<option value=""><?php _e('Bulk Actions...', 'rmcommon'); ?></option>
	<option value="deletecat"><?php _e('Delete', 'rmcommon'); ?></option>
	<option value="closecat"><?php _e('Close Category', 'rmcommon'); ?></option>
	<option value="opencat"><?php _e('Open Category', 'rmcommon'); ?></option>
</select>
<button type="submit" class="btn btn-default"><?php _e('Apply', 'rmcommon'); ?></button>
</div>

    <div class="panel panel-default">
        <div class="table-responsive">
            <table class="table" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th width="20"><input type="checkbox" name="checkall" id="checkall" onclick="$('#form-img-cats').toggleCheckboxes(':not(#checkall)');" /></th>
                    <th align="left"><?php _e('Name', 'rmcommon'); ?></th>
                    <th class="text-center"><?php _e('Status', 'rmcommon'); ?></th>
                    <th align="left"><?php _e('Sizes', 'rmcommon'); ?></th>
                    <th align="left"><?php _e('Groups', 'rmcommon'); ?></th>
                    <th class="text-center">
                        <?php echo Common\Core\Helpers\Icons::getInstance()->getIcon('svg-rmcommon-images-stack'); ?>
                    </th>
                </tr>
                </thead>
                <?php if (empty($categories)): ?>
                    <tr class="even">
                        <td colspan="6"><?php _e('There are not categories yet! You must create at least one category in order to create images.', 'rmcommon'); ?></td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($categories as $cat): ?>
                    <tr class="<?php echo tpl_cycle("even,odd"); ?>" valign="top">
                        <td align="center" valign="top"><input type="checkbox" name="cats[]" value="<?php echo $cat['id']; ?>" /></td>
                        <td>
                            <strong><?php echo $cat['name']; ?></strong>
			<span class="cu-item-options">
				<a href="images.php?action=editcat&amp;id=<?php echo $cat['id']; ?>"><?php _e('Edit', 'rmcommon'); ?></a>
				<a href="javascript:;" onclick="if(confirm('<?php echo sprintf(__('Do you really want to delete &quot;%s&quot;?', 'rmcommon'), $cat['name']); ?>')) window.location = 'images.php?action=delcat&id=<?php echo $cat['id']; ?>&XOOPS_TOKEN_REQUEST='+$('#token').val();"><?php _e('Delete', 'rmcommon'); ?></a>
				<a href="images.php?action=<?php echo $cat['status']=='open' ? 'closecat' : 'opencat'; ?>&amp;id=<?php echo $cat['id']; ?>"><?php _e($cat['status']=='open' ? 'Close' : 'Open', 'rmcommon'); ?></a>
				<a href="images.php?category=<?php echo $cat['id']; ?>"><?php _e('Images', 'rmcommon'); ?></a>
			</span>
                        </td>
                        <td class="text-center">
                            <?php if ('open' == $cat['status']) {
    echo Common\Core\Helpers\Icons::getInstance()->getIcon('svg-rmcommon-unlock text-green');
} else {
    echo Common\Core\Helpers\Icons::getInstance()->getIcon('svg-rmcommon-lock text-orange');
} ?>
                        </td>
                        <td class="sizes_data">
                            <?php foreach ($cat['sizes'] as $size): ?>
                                <?php echo $size['name']; ?>
                                <?php if ($size['type']!='none'): ?>
                                    (<em class="text-info"><?php echo $size['type']; ?>: <?php echo $size['width']; ?><?php echo $size['height']>0 ? ' x '.$size['height'] : ''; ?></em>)
                                <?php else: ?>
                                    (<em class="text-info"><?php echo _e('No Resize', 'rmcommon'); ?></em>)
                                <?php endif; ?><br />
                            <?php endforeach; ?>
                        </td>
                        <td class="sizes_data">
                            <?php _e('Upload:', 'rmcommon'); ?> <em class="text-info"><?php echo $cat['gwrite']; ?></em><br />
                            <?php _e('Access:', 'rmcommon'); ?> <em class="text-info"><?php echo $cat['gread']; ?></em><br />
                        </td>
                        <td align="center">
                            <strong><?php echo $cat['images']; ?></strong>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

<div class="cu-bulk-actions text-right">
	<?php echo $nav->render(); ?>
</div>
<input type="hidden" name="token" id="token" value="<?php echo $xoopsSecurity->createToken(); ?>" />
</form>
