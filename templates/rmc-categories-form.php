
<form name="frmcats" id="img-cat-form" method="post" accept="images.php">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title"><?php _e('Create New Category', 'rmcommon'); ?></h1>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="cat-name" class="control-label"><?php _e('Category Name:', 'rmcommon'); ?></label>
                <div class="controls">
                    <input type="text" name="name" id="cat-name" value="<?php echo $edit ? $cat->getVar('name') : $name; ?>" size="50" class="form-control input-lg" required>
                </div>
            </div>

            <div class="row">

                <div class="col-md-4 col-lg-4">

                    <div class="form-group">
                        <label for="cat-status" class="control-label"><?php _e('Category Status:', 'rmcommon'); ?></label><br>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="status" id="cat-status" value="close"<?php echo 'open' != $active ? ' checked' : ''; ?>> <?php _e('Inactive', 'rmcommon'); ?>
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="status" id="cat-status" value="open"<?php echo 'open' == $active ? ' checked' : ($edit ? '' : ' checked'); ?>> <?php _e('Active', 'rmcommon'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="filesize" class="control-label"><?php _e('Maximum file size:', 'rmcommon'); ?></label>
                        <div class="controls">
                            <input type="text" name="filesize" id="filesize" size="5" value="<?php echo $edit ? $cat->getVar('filesize') : '50'; ?>" class="form-control">
                            <select name="sizeunit" class="form-control" id="sizeunit">
                                <option value="1"<?php echo $edit && '1' == $cat->getVar('sizeunit') ? ' selected="selected"' : ''; ?>><?php _e('Bytes', 'rmcommon'); ?></option>
                                <option value="1000"<?php echo $edit && '1024' == $cat->getVar('sizeunit') ? ' selected="selected"' : (!$edit ? 'selected="selected"' : ''); ?>><?php _e('Kilobytes', 'rmcommon'); ?></option>
                                <option value="1000000"<?php echo $edit && '1048576' == $cat->getVar('sizeunit') ? ' selected="selected"' : ''; ?>><?php _e('Megabytes', 'rmcommon'); ?></option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="col-md-4 col-lg-4">

                    <div class="form-group">
                        <label for="write[]" class="control-label"><?php _e('Groups that can upload images:', 'rmcommon'); ?></label>
                        <div class="checkbox">
                            <?php echo $fwrite->render(); ?>
                        </div>
                    </div>

                </div>

                <div class="col-md-4 col-lg-4">

                    <div class="form-group">
                        <label for="read[]" class="control-label"><?php _e('Groups that can use this category:', 'rmcommon'); ?></label>
                        <div class="checkbox">
                            <?php echo $fread->render(); ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e('Custom Sizes', 'rmcommon'); ?></h3>
        </div>
        <div class="panel-body">

			<p class="lead">
				<?php _e('This configurations stablish the sizes in wich images will be created.', 'rmcommon'); ?>
                <?php _e('You can specify all sizes that you wish according to your needs.', 'rmcommon'); ?>
			</p>

            <div id="sizes-container-all">

                <div class="form-group">
                    <button type="button" class="btn btn-info new-size-button"><i class="fa fa-plus"></i> <?php _e('Add New Size', 'rmcommon'); ?></button>
                </div>

                <?php if ($edit): ?>
                    <?php $scounter = 0 ?>
                    <?php foreach ($cat->getVar('sizes') as $size): ?>
                        <div class="single-size" id="single-size-<?php echo $scounter; ?>">

                            <a href="#" class="close text-danger" id="delete-0" onclick="return delete_size(this);"><i class="fa fa-minus-circle"></i> <?php _e('Delete', 'rmcommon'); ?></a>

                            <div class="form-group">
                                <label><?php _e('Size name', 'rmcommon'); ?></label>
                                <input type="text" name="sizes[<?php echo $scounter; ?>][name]" id="sizes[<?php echo $scounter; ?>][name]" size="20" class="form-control" value="<?php echo $size['name']; ?>" placeholder="<?php _e('e.g. thumbnail', 'rmcommon'); ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><?php _e('Width:', 'rmcommon'); ?></label>
                                        <input type="text" name="sizes[<?php echo $scounter; ?>][width]" size="5" class="form-control" value="<?php echo $size['width']; ?>" placeholder="<?php _e('e.g. 150', 'rmcommon'); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <label><?php _e('Height:', 'rmcommon'); ?></label>
                                        <input type="text" name="sizes[<?php echo $scounter; ?>][height]" size="5" class="form-control" value="<?php echo $size['height']; ?>" placeholder="<?php _e('e.g. 150', 'rmcommon'); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="btn-group" data-toggle="buttons">
                                <label class="btn btn-success<?php echo 'scale' == $size['type'] || '' == $size['type'] ? ' active' : ''; ?>">
                                    <input type="radio" name="sizes[<?php echo $scounter; ?>][type]" value="scale"<?php echo 'scale' == $size['type'] || '' == $size['type'] ? ' checked' : ''; ?>> <?php _e('Scale', 'rmcommon'); ?>
                                </label>
                                <label class="btn btn-success<?php echo 'crop' == $size['type'] ? ' active' : ''; ?>">
                                    <input type="radio" name="sizes[<?php echo $scounter; ?>][type]" value="crop"<?php echo 'crop' == $size['type'] ? ' checked' : ''; ?>> <?php _e('Crop', 'rmcommon'); ?>
                                </label>
                                <label class="btn btn-success<?php echo 'none' == $size['type'] ? ' active' : ''; ?>">
                                    <input type="radio" name="sizes[<?php echo $scounter; ?>][type]" value="none"<?php echo 'none' == $size['type'] ? ' checked' : ''; ?>> <?php _e('No Resize', 'rmcommon'); ?>
                                </label>
                            </div>
                            <hr>
                            <div class="form-group">
                                <a href="#" class="btn btn-danger" id="delete-0" onclick="return delete_size(this);"><i class="fa fa-minus-circle"></i> <?php _e('Delete', 'rmcommon'); ?></a>
                            </div>
                        </div>
                        <?php $scounter++; ?>
                    <?php endforeach; ?>
                <?php else: ?>

                    <div class="single-size" id="single-size-0">

                        <a href="#" class="close" id="delete-0" onclick="return delete_size(this);"><i class="fa fa-minus-circle"></i></a>

                        <div class="form-group">
                            <label><?php _e('Size name', 'rmcommon'); ?></label>
                            <input type="text" name="sizes[0][name]" id="sizes[0][name]" size="20" class="form-control" placeholder="<?php _e('e.g. thumbnail', 'rmcommon'); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label><?php _e('Width:', 'rmcommon'); ?></label>
                                    <input type="text" name="sizes[0][width]" size="5" class="form-control" placeholder="<?php _e('e.g. 150', 'rmcommon'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label><?php _e('Height:', 'rmcommon'); ?></label>
                                    <input type="text" name="sizes[0][height]" size="5" class="form-control" placeholder="<?php _e('e.g. 150', 'rmcommon'); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-success active">
                                <input type="radio" name="sizes[0][type]" value="scale" checked> <?php _e('Scale', 'rmcommon'); ?>
                            </label>
                            <label class="btn btn-success">
                                <input type="radio" name="sizes[0][type]" value="crop"> <?php _e('Crop', 'rmcommon'); ?>
                            </label>
                            <label class="btn btn-success">
                                <input type="radio" name="sizes[0][type]" value="none"> <?php _e('No Resize', 'rmcommon'); ?>
                            </label>
                        </div>

                    </div>

                <?php endif; ?>

                <div class="form-group">
                    <button type="button" class="btn btn-info new-size-button"><i class="fa fa-plus"></i> <?php _e('Add New Size', 'rmcommon'); ?></button>
                </div>

            </div>

        </div>
    </div>


		<div class="cat-buttons">
			<button type="submit" class="btn btn-primary btn-large"><?php _e($edit ? 'Update Category' : 'Create Category', 'rmcommon'); ?></button>
			<button type="button" class="btn btn-warning btn-large" onclick="window.location = 'images.php?action=showcats';"><?php _e('Cancel', 'rmcommon'); ?></button>
		</div>

	<input type="hidden" name="action" value="<?php echo $edit ? 'saveedit' : 'save'; ?>">
	<?php if ($edit): ?><input type="hidden" name="id" value="<?php echo $cat->id(); ?>"><?php endif; ?>
</form>
