<div class="bkbk_forms">
    <form name="frmaddpos" id="frm-add-pos" />
    <label><?php _e('Position Name', 'rmcommon'); ?></label>
    <input type="text" name="posname" value="" />
    <span class="desc"><?php _e('Input a name to identify this position (<em>eg. Left blocks</em>)', 'rmcommon'); ?></span>
    <label><?php _e('Position tag', 'rmcommon'); ?></label>
    <input type="text" name="postag" value="" />
    <span class="desc"><?php _e('Specify a name for the smarty tag to use in templates (eg. left_blocks). This tag will be used as Smarty tag (eg. &lt;{$left_blocks}&gt).', 'rmcommon'); ?></span>
    <input type="button" name="bk_add_pos" id="add-position" value="<?php _e('Add Position', 'rmcommon'); ?>" />
    <?php echo $xoopsSecurity->getTokenHTML(); ?>
    </form>
    <span class="other_options"><a href="#" id="exspos"><?php _e('Existing positions', 'docs'); ?> <span>&#8711;</span></a></span>
    <div id="existing-positions">
        <?php foreach ($positions as $pos): ?>
        <span><?php echo $pos['name']; ?> <a href="#" class="edit-<?php echo $pos['id']; ?>"><?php _e('edit', 'rmcommon'); ?></a></span>
        <?php endforeach; ?>
    </div>
</div>
