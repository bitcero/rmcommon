<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><?php global $rmc_config; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $rmc_config['lang']; ?>" lang="<?php echo $rmc_config['lang']; ?>">
<head>
    <title><?php _e('Insert Image', 'rmcommon'); ?></title>
    <!-- RMTemplateHeader -->
</head>
<body style="background: #FFF;">
<div id="img-toolbar">
    <a href="#" class="select" id="a-upload" onclick="show_upload(); return false;"><?php _e('Upload Files', 'rmcommon'); ?></a>
    <a href="#" id="a-fromurl" onclick="return false;"><?php _e('From URL', 'rmcommon'); ?></a>
    <a href="#" id="a-library" onclick="show_library(); return false;"><?php _e('From Library', 'rmcommon'); ?></a>
    <?php echo RMEvents::get()->run_event('rmcommon.imgmgr.editor.options', ''); ?>
</div>
<div id="upload-container" class="container">
    <div class="form-group">
        <form name="selcat" id="select-category" method="post" action="tiny-images.php">
            <?php _e('Select the category where you wish to upload images', 'rmcommon'); ?>
            <select name="category" onchange="$('#select-category').submit();" class="form-control">
                <option value="0"<?php echo $cat->isNew() ? ' selected="selected"' : ''; ?>><?php _e('Select...', 'rmcommon'); ?></option>
                <?php foreach ($categories as $catego): ?>
                    <?php if (!$catego->user_allowed_toupload($xoopsUser)) {
    continue;
} ?>
                    <option
                        value="<?php echo $catego->id(); ?>"<?php echo $cat->id() == $catego->id() ? ' selected="selected"' : ''; ?>><?php echo $catego->getVar('name'); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="type" value="<?php echo $type; ?>">
            <input type="hidden" name="name" value="<?php echo $en; ?>">
            <input type="hidden" name="target" value="<?php echo $target; ?>">
            <input type="hidden" name="multi" value="<?php echo $multi ? 'yes' : 'no'; ?>">
            <input type="hidden" name="idcontainer" value="<?php echo $container; ?>">
        </form>
    </div>
    <?php if (!$cat->isNew()): ?>

        <div id="files-container">
            <form class="dropzone" id="images-uploader">
                <input type="hidden" name="category" value="<?php echo $cat->id(); ?>">
            </form>
        </div>

        <div id="images-resizing">
            <h4><?php _e('Resizing images', 'rmcommon'); ?></h4>
            <div class="progress">
                <div id="bar-indicator" class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="0"
                     aria-valuemin="0" aria-valuemax="100" style="width: 0;">

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
</div>

<div id="fromurl-container" class="container">
    <div class="form-group row">
        <div class="col-sm-4 col-md-2">
            <label for="imgurl"><?php _e('Image URL', 'rmcommon'); ?></label>
        </div>
        <div class="col-sm-8 col-md-10">
            <input type="text" id="imgurl" class="form-control" size="50" value="">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-4 col-md-2">
            <label for="url-title"><?php _e('Title:', 'rmcommon'); ?></label>
        </div>
        <div class="col-sm-8 col-md-10">
            <input type="text" id="url-title" class="form-control" value="">
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-4 col-md-2">
            <label for="url-alt"><?php _e('Alternative text:', 'rmcommon'); ?></label>
        </div>
        <div class="col-sm-8 col-md-10">
            <input type="text" id="url-alt" class="form-control" value="">
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-4 col-md-2">
            <label><?php _e('Alignment:', 'rmcommon'); ?></label>
        </div>
        <div class="col-sm-8 col-md-10">
            <label class="radio-inline"><input type="radio" name="align_url" value="" checked> <?php _e('None', 'rmcommon'); ?></label>
            <label class="radio-inline"><input type="radio" name="align_url" value="left"> <?php _e('Left', 'rmcommon'); ?></label>
            <label class="radio-inline"><input type="radio" name="align_url" value="center"> <?php _e('Center', 'rmcommon'); ?></label>
            <label class="radio-inline"><input type="radio" name="align_url" value="right"> <?php _e('Right', 'rmcommon'); ?></label>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-sm-4 col-md-2">
            <label for="url-alt"><?php _e('Link:', 'rmcommon'); ?></label>
        </div>
        <div class="col-sm-8 col-md-10">
            <input type="text" id="url-link" class="form-control" value="">
        </div>
    </div>

    <div class="form-group row">

        <div class="col-sm-4 col-md-2">
            &nbsp;
        </div>
        <div class="col-sm-8 col-md-10">
            <a href="javascript:;" class="btn btn-primary"
               onclick="<?php if ('exmcode' == $type): ?>insert_from_url('xoops');<?php else: ?>insert_from_url('tiny');<?php endif; ?>"><?php _e('Insert Image', 'rmcommon'); ?></a>
            <?php if ('exmcode' == $type): ?>
                <a href="javascript:;" onclick="exmPopup.closePopup();" class="btn btn-default"><?php _e('Cancel', 'rmcommon'); ?></a>
            <?php endif; ?>
        </div>

    </div>
</div>


<div id="library-container" class="container">
    <div class="categories_selector">
        <?php _e('Select the category where yo want to select images', 'rmcommon'); ?>
        <select name="category" id="category-field" onchange="show_library();" class="form-control">
            <option value="0"<?php echo $cat->isNew() ? ' selected="selected"' : ''; ?>><?php _e('Select...', 'rmcommon'); ?></option>
            <?php foreach ($categories as $catego): ?>
                <?php if (!$catego->user_allowed_toupload($xoopsUser)) {
    continue;
} ?>
                <option
                    value="<?php echo $catego->id(); ?>"<?php echo $cat->id() == $catego->id() ? ' selected="selected"' : ''; ?>><?php echo $catego->getVar('name'); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="XOOPS_TOKEN_REQUEST" id="xoops-token" value="<?php echo $xoopsSecurity->createToken(); ?>">
    </div>
    <div id="library-content" class="loading">

    </div>
</div>

<input type="hidden" name="type" id="type" value="<?php echo $type; ?>">
<input type="hidden" name="editor" id="editor" value="<?php echo $editor; ?>">
<input type="hidden" name="name" id="name" value="<?php echo $en; ?>">
<input type="hidden" name="target" id="target" value="<?php echo $target; ?>">
<input type="hidden" name="multi" id="multi" value="<?php echo $multi ? 'yes' : 'no'; ?>">
<input type="hidden" name="idcontainer" id="idcontainer" value="<?php echo $container; ?>">
<input type="hidden" id="cu-token" value="<?php echo $common->security()->createToken(0, 'CUTOKEN'); ?>">
<span id="parameters">
<?php
$ev = RMEvents::get();
$ev->run_event('rmcommon.imgwin.parameter');
?>
</span>

<!-- Options from other elements -->
<?php RMEvents::get()->run_event('rmcommon.imgmgr.editor.containers', ''); ?>
<!-- RMTemplateFooter -->
</body>
</html>
