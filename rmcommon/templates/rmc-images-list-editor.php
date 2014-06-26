<?php if( empty( $images ) ): ?>

    <div class="alert alert-info text-center">
        <?php _e('There are not images yet!','rmcommon'); ?>
    </div>

<?php endif; ?>

<?php foreach( $images as $image ): ?>

    <a href="#" data-id="<?php echo $image['id']; ?>" class="thumbnail-item" style="background-image: url('<?php echo $image['thumb']; ?>');" alt="<?php echo $image['title']; ?>">
        <span class="thumbnail-cover"></span>
        <span href="#" class="add"><span class="glyphicon glyphicon-plus"></span></span>
        <span href="#" class="insert"><span class="glyphicon glyphicon-ok"></span></span>
    </a>

<?php endforeach; ?>

<div id="inserter-blocker"></div>
<div id="image-inserter">
    <div class="row content">

        <div class="image-info">
            <span class="image"></span>

            <div class="media author-info">
                <a href="#" class="pull-left" target="_blank">
                    <img class="author-avatar img-thumbnail">
                </a>
                <div class="media-body">
                    <h5 class="media-heading"><strong>Uploaded by <a href="#" target="_blank"></a></strong></h5>
                    <small>on <span class="info-date"></span></small><br>
                </div>
            </div>
            <ul class="list-unstyled">
                <li>
                    <small>Title: <code><span class="info-title"></span></code></small>
                </li>
                <li>
                    <small>Description: <code><span class="info-description"></span></code></small>
                </li>
                <li>
                    <small>MIME type: <code><span class="info-mime"></span></code></small>
                </li>
                <li>
                    <small>Original: <code><span class="info-original"></span></code></small>
                </li>
                <li>
                    <small>File size: <code><span class="info-size"></span></code></small>
                </li>
                <li>
                    <small>Dimensions: <code><span class="info-dimensions"></span></code></small>
                </li>
            </ul>

        </div>

        <!-- Insert form -->
        <div class="image-form">
            <div class="form-group">
                <label><?php _e('Title:', 'rmcommon'); ?></label>
                <input class="form-control input-sm img-title" type="text">
            </div>
            <div class="form-group">
                <label><?php _e('Alternative text:','rmcommon'); ?></label>
                <input class="form-control input-sm img-alt" type="text">
            </div>
            <div class="form-group">
                <label><?php _e('Description:','rmcommon'); ?></label>
                <textarea class="form-control input-sm img-description"></textarea>
            </div>
            <div class="form-group">
                <label><?php _e('Link URL:','rmcommon'); ?></label>
                <input class="form-control input-sm img-link" type="text">
                <div class="btn-group btn-group-xs img-links">

                </div>
            </div>
        </div>
        <!-- Insert form /-->
    </div>
</div>

<table width="100%" cellspacing="0">
    <?php foreach($images as $image): ?>
    <tr class="image_data" id="data-<?php echo $image['id']; ?>">
        <td colspan="3">

        <table width="100%" cellpadding="2" cellspacing="0" class="the_data">
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
                        <br />(<?php list($w,$h) = getimagesize(str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $image['url']).'/'.$image['file'].'.'.$image['extension']); echo $w.' x '.$h; ?>)</label>
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
<?php echo $nav->display( false ); ?>
<input type="hidden" id="filesurl" value="<?php echo $filesurl; ?>" />