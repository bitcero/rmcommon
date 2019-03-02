<div class="title"><?php echo sprintf(__('%s settings', 'rmcommon'), $block->getVar('name')); ?><span class="close" onclick="blocksAjax.close();"></span></div>

<ul class="nav nav-tabs settings-nav">
    <li class="active"><a href="#general-content" data-toggle="tab"><?php _e('General Settings', 'rmcommon'); ?></a></li>
    <?php if ($block_options || $block->getVar('type')=='custom'): ?>
        <li><a href="#custom-content" data-toggle="tab"><?php _e('Custom Options', 'rmcommon'); ?></a></li>
    <?php endif; ?>
</ul>

<form name="frmBkConfig" id="frm-block-config" method="post" action="blocks.php">
<fieldset>
<div id="block-config-form">

        <div class="tab-content">

            <!-- General Content -->
            <div class="tab-pane active" id="general-content">

                <div class="form-group">

                    <label for="bk-name"><strong><?php _e('Block Title', 'rmcommon'); ?></strong></label>
                    <input type="text" name="bk_name" size="50" class="form-control input-lg" value="<?php echo $block->getVar('name'); ?>" />

                </div>

                <div class="row">

                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label for="bk-pos"><strong><?php _e('Block position', 'rmcommon'); ?></strong></label>
                            <select name="bk_pos" id="bk-pos" class="form-control">
                                <?php foreach ($positions as $pos): ?>
                                    <option value="<?php echo $pos['id_position']; ?>"<?php echo $block->getVar('canvas')==$pos['id_position']?' selected="selected"' : ''; ?>><?php echo $pos['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label for="bk-weight"><strong><?php _e('Block weight', 'rmcommon'); ?></strong></label>
                            <input type="text" class="form-control" name="bk_weight" id="bk-weight" value="<?php echo $block->getVar('weight'); ?>" />
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label for="bk-cache"><strong><?php _e('Cache lifetime', 'rmcommon'); ?></strong></label>
                            <select size="1" name="bk_cache" id="bk-cache" class="form-control">
                                <option value="0" selected="selected"><?php _e('No Cache', 'rmcommon'); ?></option>
                                <option value="30"><?php _e('30 seconds', 'rmcommon'); ?></option>
                                <option value="60"><?php _e('1 minute', 'rmcommon'); ?></option>
                                <option value="300"><?php _e('5 minutes', 'rmcommon'); ?></option>
                                <option value="1800"><?php _e('30 minutes', 'rmcommon'); ?></option>
                                <option value="3600"><?php _e('1 hour', 'rmcommon'); ?></option>
                                <option value="18000"><?php _e('5 hours', 'rmcommon'); ?></option>
                                <option value="86400"><?php _e('1 day', 'rmcommon'); ?></option>
                                <option value="259200"><?php _e('3 days', 'rmcommon'); ?></option>
                                <option value="604800"><?php _e('1 week', 'rmcommon'); ?></option>
                                <option value="2592000"><?php _e('1 month', 'rmcommon'); ?></option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label for="bk-visible"><strong><?php _e('Visible', 'rmcommon'); ?></strong></label><br>
                            <div class="radio-inline">
                                <label><input type="radio" value="1" name="bk_visible" id="bk-visible" <?php echo $block->getVar('visible')==1?'checked="checked"':''; ?>/> <?php _e('Yes', 'rmcommon'); ?></label>
                            </div>
                            <div class="radio-inline">
                                <label><input type="radio" value="0" name="bk_visible" <?php echo $block->getVar('visible')==0?'checked="checked"':''; ?>/> <?php _e('No', 'rmcommon'); ?></label>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-4 col-lg-4">
                        <label class="options"><?php _e('Access Permissions', 'rmcommon'); ?></label>
                        <?php echo $groups->render(); ?>
                    </div>

                    <div class="col-md-4 col-lg-4">

                        <div class="form-group">
                            <label><strong><?php _e('Visible in', 'rmcommon'); ?></strong></label><br>
                            <?php echo $canvas->render(); ?>
                        </div>

                    </div>

                </div>


            </div>
            <!--// End general content -->

            <?php if ($block_options || $block->getVar('type')=='custom'): ?>
            <!-- Custom Content -->
            <div class="tab-pane" id="custom-content">
                <?php echo $block_options; ?>
                <?php if ($block->getVar('type')=='custom'): ?>
                    <legend><?php _e('Custom Block Content', 'rmcommon'); ?></legend>
                    <div class="form-group">
                        <textarea cols="45" rows="10" name="bk_content" id="bk-content" class="form-control" style="height: 300px;"><?php echo htmlspecialchars($block->getVar('content')); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="c-type"><strong><?php _e('Content type:', 'rmcommon'); ?></strong></label>
                        <select name="bk_ctype" id="c-type" class="form-control">
                            <option value="TEXT"<?php echo $block->getVar('content_type')=='TEXT' ? ' selected="selected"' : ''; ?>><?php _e('Formatted text', 'rmcommon'); ?></option>
                            <option value="HTML"<?php echo $block->getVar('content_type')=='HTML' ? ' selected="selected"' : ''; ?>><?php _e('HTML block', 'rmcommon'); ?></option>
                            <option value="PHP"<?php echo $block->getVar('content_type')=='PHP' ? ' selected="selected"' : ''; ?>><?php _e('PHP block', 'rmcommon'); ?></option>
                            <option value="XOOPS"<?php echo $block->getVar('content_type')=='XOOPS' ? ' selected="selected"' : ''; ?>><?php _e('XOOPS code', 'rmcommon'); ?></option>
                        </select>
                    </div>

                <?php endif; ?>
            </div>
            <!--// End custom content -->
            <?php endif; ?>

        </div>

</div>

    <div class="settings-form-controls">
        <button type="button" onclick="blocksAjax.close();" class="btn btn-default btn-lg pull-left"><?php _e('Cancel', 'rmcommon'); ?></button>
        <button type="button" onclick="blocksAjax.sendConfig();" class="btn btn-lg btn-primary pull-right"><?php _e('Save Settings', 'rmcommon'); ?></button>
    </div>

    <input type="hidden" name="action" value="saveconfig" />
    <input type="hidden" name="bid" value="<?php echo $id; ?>" />
    <input type="hidden" name="XOOPS_TOKEN_REQUEST" value="<?php echo $xoopsSecurity->createToken(); ?>">
</fieldset>
</form>
