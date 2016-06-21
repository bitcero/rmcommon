<?php if(!$isupdate): ?>
<h1 class="cu-section-title"><?php _e('Upload Images','rmcommon'); ?></h1>
<div class="select-image-category">
	<form name="frmcat" method="get" action="images.php" class="form-inline">
	<?php _e('Select Category:','rmcommon'); ?> &nbsp;
	<select name="category" class="form-control">
		<option value=""><?php _e('Select...','rmcommon'); ?></option>
		<?php foreach($categories as $category): ?>
		<option value="<?php echo $category['id']; ?>"<?php echo $cat->id()==$category['id'] ? ' selected="selected"' : '' ?>><?php echo $category['name']; ?></option>
		<?php endforeach; ?>
	</select>
	<button type="submit" class="btn btn-info"><?php _e('Change Category','rmcommon'); ?></button>
    <button type="button" class="btn btn-pink" data-action="upload-more"><?php _e('Upload More','rmcommon'); ?></button>
	<input type="hidden" name="action" value="new" />
	</form>
</div>
<?php if (!$cat->isNew()): ?>

<div id="upload-controls">
    <div id="upload-errors">

    </div>
</div>

        <div id="files-container">
            <form class="dropzone" id="images-uploader">
                <input type="hidden" name="category" value="<?php echo $cat->id(); ?>">
            </form>
        </div>

        <div id="images-resizing">
            <h4><?php _e('Resizing images', 'rmcommon'); ?></h4>
            <div class="progress">
                <div id="bar-indicator" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">

                </div>
            </div>
            <span class="message"></span>
        </div>

        <div id="uploading-messages">
            <h4><?php _e('Uploading messages', 'rmcommon'); ?></h4>
            <ul>
            </ul>
        </div>

<?php endif; ?>
<?php endif; ?>

