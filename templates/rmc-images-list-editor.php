<table width="100%" cellspacing="0">

    <?php if(empty($images)): ?>
    <tr class="even error">
        <td colspan="3">
            <?php _e('There are not images yet!','rmcommon'); ?>
        </td>
    </tr>
    <?php endif; ?>
    <?php foreach($images as $image): ?>
    <tr class="<?php echo tpl_cycle("even,odd"); ?> image_list" valign="top" id="list-<?php echo $image['id']; ?>">
        <td width="35"><img src="<?php echo $image['thumb']; ?>" alt="" width="35" height="30" /></td>
        <td>
            <strong><?php echo $image['title']; ?></strong>
            <?php if($image['desc']!=''): ?>
            <span class="description"><?php echo $image['desc']; ?></span>
            <?php endif; ?>
        </td>
        <td align="center">
            <a href="javascript:;" class="data_show" onclick="show_image_data(<?php echo $image['id']; ?>);"><?php _e('Show','rmcommon'); ?></a>
        </td>
    </tr>
    <tr class="image_data" id="data-<?php echo $image['id']; ?>">
        <td colspan="3">
        
        <table width="100%" cellpadding="2" cellspacing="0" class="the_data">
            <tr class="odd">
                <td rowspan="3"><img src="<?php echo $image['thumb']; ?>" alt="" style="max-width: 150px;" /></td>
                <td><a href="javascript:;" class="data_hide" onclick="hide_image_data(<?php echo $image['id']; ?>);"><?php _e('Hide','rmcommon'); ?></a><strong><?php echo $image['title']; ?></strong></td>
            </tr>
            <tr class="even"><td><?php echo $image['mime']; ?></td></tr>
            <tr class="odd"><td><?php echo $image['date']; ?></td></tr>
            <tr class="even">
                <td><strong>*<?php _e('Title:','rmcommon'); ?></strong></td>
                <td><input type="text" id="image-name-<?php echo $image['id']; ?>" size="50" value="<?php echo $image['title']; ?>" /></td>
            </tr>
            <tr class="odd">
                <td><strong><?php _e('Alternative text:','rmcommon'); ?></strong></td>
                <td><input type="text" id="image-alt-<?php echo $image['id']; ?>" size="50" value="" /></td>
            </tr>
            <tr class="even" valign="top">
                <td><strong><?php _e('Description:','rmcommon'); ?></strong></td>
                <td><textarea id="image-desc-<?php echo $image['id']; ?>" style="width: 90%; height: 100px;"><?php echo $image['desc']; ?></textarea></td>
            </tr>
            <tr class="odd">
                <td><strong><?php _e('Link URL:','rmcommon'); ?></strong></td>
                <td class="image_link">
                	<input type="hidden" id="fileurl_<?php echo $image['id']; ?>" value="<?php echo $image['links']['file']['value']; ?>" />
                    <input type="text" id="image-link-<?php echo $image['id']; ?>" size="50" value="<?php echo $image['links']['file']['value']; ?>" />
                    <?php foreach ($image['links'] as $link): ?>
                    <a href="javascript:;" onclick="$('#image-link-<?php echo $image['id']; ?>').val('<?php echo $link['value']; ?>');"><?php echo $link['caption']; ?></a>
                    <?php endforeach; ?>
                </td>
            </tr>
            <tr class="even">
                <td><strong><?php _e('Alignment:','rmcommon'); ?></strong></td>
                <td><strong>
                    <label><input type="radio" name="align_<?php echo $image['id']; ?>" value="" checked="checked" /> <?php _e('None','rmcommon'); ?></label>
                    <label><input type="radio" name="align_<?php echo $image['id']; ?>" value="left" /> <?php _e('Left','rmcommon'); ?></label>
                    <label><input type="radio" name="align_<?php echo $image['id']; ?>" value="center" /> <?php _e('Center','rmcommon'); ?></label>
                    <label><input type="radio" name="align_<?php echo $image['id']; ?>" value="right" /> <?php _e('Right','rmcommon'); ?></label></strong>
                </td>
            </tr>
            <tr class="odd">
            	<td><strong><?php _e('Image size:','rmcommon'); ?></strong></td>
            	<td class="sizes">
            		<?php foreach($cat->getVar('sizes') as $i => $size): ?>
            		<?php if($size['width']<=0) continue; ?>
                    <?php
                        
                        $tfile = str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $image['url']).'/sizes/'.$image['file'].'_'.$size['width'].'x'.$size['height'].'.'.$image['extension'];
                        if(!is_file($tfile)) continue;
                    ?>
            		<label><input type="radio" rel="<?php echo $size['width']; ?>" name="size_<?php echo $image['id']; ?>" value="<?php echo $image['url']; ?>/sizes/<?php echo $image['file'].'_'.$size['width'].'x'.$size['height'].'.'.$image['extension']; ?>" /><br /><?php echo $size['name']; ?><br />(<?php echo $size['width'].($size['height']!='' ? ' x '.$size['height'] : ''); ?>)</label>
            		<?php endforeach; ?>
            		<label><input type="radio" rel="original" name="size_<?php echo $image['id']; ?>" value="<?php echo $image['url']; ?>/<?php echo $image['file'].'.'.$image['extension']; ?>" checked="checked" /><br /><?php _e('Original','rmcommon'); ?>
                    <br />(<?php list($w,$h) = getimagesize($image['url'].'/'.$image['file'].'.'.$image['extension']); echo $w.' x '.$h; ?>)</label>
            		<input type="hidden" id="extension_<?php echo $image['id']; ?>" value="<?php echo $image['extension']; ?>">
            	</td>
            </tr>
            <tr class="even">
                <td class="size_url" colspan="2" align="center"><span><?php echo $image['url']; ?>/<?php echo $image['file'].'.'.$image['extension']; ?></span></td>
            </tr>
            <tr class="odd">
            	<td colspan="2">
            		<a href="javascript:;" class="insert_button" onclick="insert_image(<?php echo $image['id']; ?>,'<?php echo $type!=''?$type:'tiny'; ?>','<?php echo $target; ?>', '<?php echo $container; ?>');"><?php _e('Insert image','rmcommon'); ?></a>
            		<a href="javascript:;"><?php _e('Delete','rmcommon'); ?></a>
            	</td>
            </tr>
        </table>
        
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<input type="hidden" name="token" id="ret-token" value="<?php echo $xoopsSecurity->createToken(); ?>" />
<?php echo $nav->display(); ?>
<input type="hidden" id="filesurl" value="<?php echo $filesurl; ?>" />