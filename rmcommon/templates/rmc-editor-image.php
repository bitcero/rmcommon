<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><?php global $rmc_config; ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $rmc_config['lang']; ?>" lang="<?php echo $rmc_config['lang']; ?>">
<head>
<title><?php _e('Insert Image','rmcommon'); ?></title>
    <!-- RMTemplateHeader -->
</head>
<body style="background: #FFF;">
<div id="img-toolbar">
	<a href="#" class="select" id="a-upload" onclick="show_upload(); return false;"><?php _e('Upload Files','rmcommon'); ?></a>
	<a href="#" id="a-fromurl" onclick="return false;"><?php _e('From URL','rmcommon'); ?></a>
	<a href="#" id="a-library" onclick="show_library(); return false;"><?php _e('From Library','rmcommon'); ?></a>
    <?php echo RMEvents::get()->run_event('rmcommon.imgmgr.editor.options', ''); ?>
</div>
<div id="upload-container" class="container">
    <div class="form-group">
        <form name="selcat" id="select-category" method="post" action="tiny-images.php">
        <?php _e('Select the category where you wish to upload images','rmcommon'); ?>
        <select name="category" onchange="$('#select-category').submit();" class="form-control">
            <option value="0"<?php echo $cat->isNew() ? ' selected="selected"' : ''; ?>><?php _e('Select...','rmcommon'); ?></option>
            <?php foreach($categories as $catego): ?>
                <?php if(!$catego->user_allowed_toupload($xoopsUser)) continue; ?>
                <option value="<?php echo $catego->id(); ?>"<?php echo $cat->id()==$catego->id() ? ' selected="selected"' : ''; ?>><?php echo $catego->getVar('name'); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="type" value="<?php echo $type; ?>" />
        <input type="hidden" name="name" value="<?php echo $en; ?>" />
            <input type="hidden" name="target" value="<?php echo $target; ?>" />
            <input type="hidden" name="idcontainer" value="<?php echo $container; ?>" />
        </form>
    </div>
    <?php if (!$cat->isNew()): ?>
    <div id="upload-controls">
        <div id="upload-errors"></div>
        <div id="files-container"></div>
    </div>
    <div id="resizer-bar">
        <strong><?php _e('Resizing images','rmcommon'); ?></strong>
        <div class="thebar">
            <div class="indicator" id="bar-indicator">0</div>
        </div>
        <span class="message"></span>
        <span><?php _e('Please, do not close the window until resizing process has finished!','rmcommon'); ?></span>
    </div>
    <div id="gen-thumbnails"></div>
    <?php endif; ?>
</div>

<div id="fromurl-container" class="container">
    <table width="100%" cellpadding="3" cellspacing="0">
        <tr>
            <td><?php _e('Image URL','rmcommon'); ?></td>
            <td><input type="text" id="imgurl" size="50" value="" /></td>
        <tr>
        <tr>
            <td><?php _e('Title:','rmcommon'); ?></td>
            <td><input type="text" id="url-title" value="" /></td>
        </tr>
        <tr>
            <td><?php _e('Alternative text:','rmcommon'); ?></td>
            <td><input type="text" id="url-alt" value="" /></td>
        </tr>
        <tr>
            <td><?php _e('Alignment:','rmcommon'); ?></td>
            <td>
            <strong>
                    <label><input type="radio" name="align_url" value="" checked="checked" /> <?php _e('None','rmcommon'); ?></label>
                    <label><input type="radio" name="align_url" value="left" /> <?php _e('Left','rmcommon'); ?></label>
                    <label><input type="radio" name="align_url" value="center" /> <?php _e('Center','rmcommon'); ?></label>
                    <label><input type="radio" name="align_url" value="right" /> <?php _e('Right','rmcommon'); ?></label></strong>
            </td>
        </tr>
        <tr>
            <td><?php _e('Link:','rmcommon'); ?></td>
            <td><input type="text" id="url-link" value="" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
            <a href="javascript:;" class="insert_button" onclick="<?php if($type=='exmcode'): ?>insert_from_url('xoops');<?php else: ?>insert_from_url('tiny');<?php endif; ?>"><?php _e('Insert Image','rmcommon'); ?></a>
            <?php if($type=='exmcode'): ?>
            <a href="javascript:;" onclick="exmPopup.closePopup();"><?php _e('Cancel','rmcommon'); ?></a>
            <?php endif; ?>
            </td>
        </tr>
    </table>
</div>


<div id="library-container" class="container">
    <div class="categories_selector">
        <?php _e('Select the category where you wish to upload images','rmcommon'); ?>
        <select name="category" id="category-field" onchange="show_library();">
            <option value="0"<?php echo $cat->isNew() ? ' selected="selected"' : ''; ?>><?php _e('Select...','rmcommon'); ?></option>
            <?php foreach($categories as $catego): ?>
                <?php if(!$catego->user_allowed_toupload($xoopsUser)) continue; ?>
                <option value="<?php echo $catego->id(); ?>"<?php echo $cat->id()==$catego->id() ? ' selected="selected"' : ''; ?>><?php echo $catego->getVar('name'); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="XOOPS_TOKEN_REQUEST" id="xoops-token" value="<?php echo $xoopsSecurity->createToken(); ?>" />
    </div>
    <div id="library-content" class="loading">
        
    </div>
</div>

<input type="hidden" name="type" id="type" value="<?php echo $type; ?>" />
<input type="hidden" name="name" id="name" value="<?php echo $en; ?>" />
<input type="hidden" name="target" id="target" value="<?php echo $target; ?>" />
<input type="hidden" name="idcontainer" id="idcontainer" value="<?php echo $container; ?>" />
<span id="parameters">
<?php
    $ev = RMEvents::get();
    $ev->run_event('rmcommon.imgwin.parameter');
?>
</span>

<!-- Options from other elements -->
<?php RMEvents::get()->run_event('rmcommon.imgmgr.editor.containers',''); ?>

</body>
</html>