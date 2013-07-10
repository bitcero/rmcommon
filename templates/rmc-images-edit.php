<?php RMTemplate::get()->add_head('<script type="text/javascript">$(document).ready(function(){$("#frmupdimg").validate();});</script>'); ?>
<h1 class="rmc_titles"><?php _e('Edit Image','rmcommon'); ?></h1>

<div class="row-fluid">
	<div class="span10">
		<form name="frmupdimg" id="frmupdimg" method="post" action="images.php" class="form-horizontal">

			<div class="control-group">
				<label class="control-label"><?php _e('File:','rmcommon'); ?></label>
				<div class="controls">
					<span class="uneditable-input input-block-level"><?php echo $image_data['file']; ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php _e('Image data:','rmcommon'); ?></label>
				<div class="controls">
					<span class="label"><?php echo $image_data['mime']; ?></span>
					<span class="label"><?php echo formatTimestamp($image_data['date'], 'c'); ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php _e('Available sizes:','rmcommon'); ?></label>
				<div class="controls">
					<?php foreach($image_data['sizes'] as $size): ?>
						<a href="<?php echo $size['file']; ?>" class="btn"><?php echo $size['name']; ?></a>
					<?php endforeach; ?>
					<a href="<?php echo $image_data['url']; ?>" class="btn btn-info"><?php _e('Original Size','rmcommon'); ?></a>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">*<?php _e('Title:', 'rmcommon'); ?></label>
				<div class="controls">
					<input name="title" type="text" value="<?php echo $image_data['title']; ?>" size="50" class="required input-block-level" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php _e('Category:','rmcommon'); ?></label>
				<div class="controls">
					<select name="cat">
						<?php foreach($categories as $catego): ?>
							<option value="<?php echo $catego['id']; ?>"<?php echo $catego['id']==$cat->id() ? ' selected="selected"' : ''; ?>><?php echo $catego['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label"><?php _e('Description:','rmcommon'); ?></label>
				<div class="controls">
					<textarea name="desc" class="input-block-level"><?php echo $image_data['desc']; ?></textarea>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary btn-large"><?php _e('Update Image!','rmcommon'); ?></button>
					<button type="button" class="btn btn-warning btn-large" onclick="window.location = 'images.php?category=<?php echo $cat->id(); ?>&page=<?php echo $page; ?>';"><?php _e('Cancel','rmcommon'); ?></button>
				</div>
			</div>

			<?php echo $rmEvents->run_event('rmcommon.image.edit.form'); ?>

			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="page" value="<?php echo $page; ?>" />
			<input type="hidden" name="XOOPS_TOKEN_REQUEST" value="<?php echo $xoopsSecurity->createToken(); ?>" />
		</form>
	</div>

	<div class="span2">
		<img src="<?php echo $image_data['thumbnail']; ?>" alt="" />
	</div>

</div>

<div id="image-loader"></div>