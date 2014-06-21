<?php if(!$isupdate): ?>
<h1 class="cu-section-title"><?php _e('Upload Images','rmcommon'); ?></h1>
<div class="select_image_cat">
	<form name="frmcat" method="get" action="images.php" class="form-inline">
	<?php _e('Select Category:','rmcommon'); ?> &nbsp;
	<select name="category" class="form-control">
		<option value=""><?php _e('Select...','rmcommon'); ?></option>
		<?php foreach($categories as $category): ?>
		<option value="<?php echo $category['id']; ?>"<?php echo $cat->id()==$category['id'] ? ' selected="selected"' : '' ?>><?php echo $category['name']; ?></option>
		<?php endforeach; ?>
	</select>
	<button type="submit" class="btn btn-info"><?php _e('Change Category','rmcommon'); ?></button>
    <button type="button" class="btn btn-default"><?php _e('Cancel','rmcommon'); ?></button>
	<input type="hidden" name="action" value="new" />
	</form>
</div>
<?php if (!$cat->isNew()): ?>
<div class="row-fluid">
	<button type="button" class="btn btn-success" onclick="$('#files-container').uploadifyUpload();"><?php _e('Upload','rmcommon'); ?></button>
	<button type="button" class="btn btn-warning" onclick="$('#files-container').uploadifyClearQueue(); $('#upload-errors').html('');"><?php _e('Clear All','rmcommon'); ?></button>
</div><br>
<div id="upload-controls">
    <div id="upload-errors">

    </div>
    <div id="files-container">

    </div>
</div>
<?php endif; ?>
<?php endif; ?>
<div id="resizer-bar">
<span class="message"></span>
<strong><?php _e('Resizing images','rmcommon'); ?></strong>
<div class="progress progress-striped active thebar">
<div class="bar indicator" id="bar-indicator">0</div>
</div>
<span><?php _e('Please, do not close the window until resizing process has finished!','rmcommon'); ?></span>
    <div class="donebutton">
        <?php if(!$isupdate): ?>
	        <button type="button" class="btn btn-warning donebutton" onclick="imgcontinue();"><?php _e('Done! Click to continue...','rmcommon'); ?></button>
        <?php endif; ?>
        <button type="button" class="btn btn-info" onclick="window.location = 'images.php?category=<?php echo $cat->id(); ?>';"><?php _e('Done! Show images...','rmcommon'); ?></button>
    </div>
</div>
<div id="gen-thumbnails"></div>

