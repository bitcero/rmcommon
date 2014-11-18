<?php RMTemplate::get()->add_head('<script type="text/javascript">$(document).ready(function(){$("#frmupdimg").validate();});</script>'); ?>
<h1 class="cu-section-title"><?php _e('Edit Image','rmcommon'); ?></h1>

<div class="row">
	<div class="col-md-10 col-lg-10">
		<form name="frmupdimg" id="frmupdimg" method="post" action="images.php">

			<div class="form-group">
				<label><?php _e('File:','rmcommon'); ?></label>
				<span class="form-control"><strong><?php echo $image_data['file']; ?></strong></span>
			</div>

			<div class="form-group">
				<label><?php _e('Image data:','rmcommon'); ?></label>
				<div class="form-control">
					<span><?php echo $image_data['mime']; ?></span>,
					<span><?php echo formatTimestamp($image_data['date'], 'c'); ?></span>
				</div>
			</div>

			<div class="form-group">
				<label><?php _e('Available sizes:','rmcommon'); ?></label>
				<ul class="list-inline">
					<?php foreach($image_data['sizes'] as $size): ?>
						<li><a href="<?php echo $size['file']; ?>" class="btn btn-default"><?php echo $size['name']; ?></a></li>
					<?php endforeach; ?>
					<li><a href="<?php echo $image_data['url']; ?>" class="btn btn-info"><?php _e('Original Size','rmcommon'); ?></a></li>
				</ul>
			</div>
            <div class="clearfix"></div>
			<div class="form-group">
				<label>*<?php _e('Title:', 'rmcommon'); ?></label>
			    <input name="title" type="text" value="<?php echo $image_data['title']; ?>" size="50" class="form-control" required>
			</div>

			<div class="form-group">
				<label for="cat"><?php _e('Category:','rmcommon'); ?></label>
                <select name="cat" id="cat" class="form-control">
                    <?php foreach($categories as $catego): ?>
                        <option value="<?php echo $catego['id']; ?>"<?php echo $catego['id']==$cat->id() ? ' selected="selected"' : ''; ?>><?php echo $catego['name']; ?></option>
                    <?php endforeach; ?>
                </select>
			</div>

			<div class="form-group">
				<label for="desc"><?php _e('Description:','rmcommon'); ?></label>
                <textarea name="desc" id="desc" class="form-control" rows="4"><?php echo $image_data['desc']; ?></textarea>
			</div>

			<div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg"><?php _e('Update Image!','rmcommon'); ?></button>
                <button type="button" class="btn btn-default btn-lg" onclick="window.location = 'images.php?category=<?php echo $cat->id(); ?>&page=<?php echo $page; ?>';"><?php _e('Cancel','rmcommon'); ?></button>
			</div>

			<?php echo $rmEvents->run_event('rmcommon.image.edit.form'); ?>

			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="page" value="<?php echo $page; ?>" />
			<input type="hidden" name="XOOPS_TOKEN_REQUEST" value="<?php echo $xoopsSecurity->createToken(); ?>" />
		</form>
	</div>

	<div class="col-md-2 col-lg-2">
		<img src="<?php echo $image_data['thumbnail']; ?>" alt="" />
	</div>

</div>

<div id="image-loader"></div>
