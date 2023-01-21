<div class="title box-header">
    <h3 class="box-title">
      <?php echo sprintf(__('%s settings', 'rmcommon'), $block->getVar('name')); ?>
        <span class="close" onclick="blocksAjax.close();"></span>
    </h3>
</div>

<div class="box-content no-padding">
    <ul class="nav nav-tabs settings-nav">
        <li class="nav-item" role="presentation">
            <button data-bs-target="#general-content" data-bs-toggle="tab" class="nav-link active" id="general-tab"
                    type="button" role="tab" aria-controls="general-content"
                    aria-selected="true"><?php _e('General Settings', 'rmcommon'); ?></button>
        </li>
      <?php if ($block_options || 'custom' == $block->getVar('type')): ?>
          <li class="nav-item" role="presentation">
              <button data-bs-target="#custom-content" data-bd-toggle="tab" class="nav-link" id="custom-tab"
                      type="button" role="tab"
                      aria-controls="custom-content"><?php _e('Custom Options', 'rmcommon'); ?></button>
          </li>
      <?php endif; ?>
    </ul>

    <form name="frmBkConfig" id="frm-block-config" method="post" action="blocks.php">
        <fieldset>
            <div id="block-config-form">

                <div class="tab-content" id="block-settings-content">

                    <!-- General Content -->
                    <div class="tab-pane fade show active" id="general-content" role="tabpanel" aria-labelledby="general-tab">

                        <div class="mb-3">

                            <label for="bk-name" class="form-label">
                                <?php _e('Block Title', 'rmcommon'); ?>
                            </label>
                            <input type="text" name="bk_name" class="form-control input-lg"
                                   value="<?php echo $block->getVar('name'); ?>" />

                        </div>

                        <div class="mb-3 d-md-flex justify-content-between multicolumn-options">
                            <div class="mb-3 mb-lg-0">
                                <label for="bk-pos" class="form-label">
                                    <?php _e('Block position', 'rmcommon'); ?>
                                </label>
                                <select name="bk_pos" id="bk-pos" class="form-select">
                                  <?php foreach ($positions as $pos): ?>
                                      <option value="<?php echo $pos['id_position']; ?>"<?php echo $block->getVar('canvas') == $pos['id_position'] ? ' selected="selected"' : ''; ?>><?php echo $pos['name']; ?></option>
                                  <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3 mb-lg-0">
                                <label for="bk-weight" class="form-label">
                                    <?php _e('Block weight', 'rmcommon'); ?>
                                </label>
                                <input type="text" class="form-control" name="bk_weight" id="bk-weight"
                                       value="<?php echo $block->getVar('weight'); ?>">
                            </div>

                            <div class="mb-3 mb-lg-0">
                                <label for="bk-cache" class="form-label">
                                    <?php _e('Cache lifetime', 'rmcommon'); ?>
                                </label>
                                <select size="1" name="bk_cache" id="bk-cache" class="form-select">
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

                        <div class="mb-3 d-md-flex justify-content-between multicolumn-options">
                            <div class="mb-3 mb-lg-0">
                                <div class="form-group">
                                    <label for="bk-visible" class="form-label">
                                        <?php _e('Visible', 'rmcommon'); ?>
                                    </label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="1" name="bk_visible"
                                               id="bk-visible-1" <?php echo 1 == $block->getVar('visible') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="bk-visible-1"><?php _e('Yes', 'rmcommon'); ?></label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="0" id="bk-visible-2"
                                               name="bk_visible" <?php echo 0 == $block->getVar('visible') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="bk-visible-2"><?php _e('No', 'rmcommon'); ?></label>
                                    </div>
                                </div>

                            </div>

                            <div class="mb-3 mb-lg-0">
                                <label class="form-label"><?php _e('Access Permissions', 'rmcommon'); ?></label>
                              <?php echo $groups->render(); ?>
                            </div>

                            <div class="mb-3 mb-lg-0">

                                <label class="form-label"><?php _e('Visible in', 'rmcommon'); ?></label><br>
                              <?php echo $canvas->render(); ?>

                            </div>

                        </div>


                    </div>
                    <!--// End general content -->

                  <?php if ($block_options || 'custom' == $block->getVar('type')) : ?>
                      <!-- Custom Content -->
                      <div class="tab-pane fade" id="custom-content" role="tabpanel" aria-labelledby="custom-tab">
                        <?php echo $block_options; ?>
                        <?php if ('custom' == $block->getVar('type')): ?>
                            <div class="mb-3">
                                <label for="bk-content" class="form-label"><?php _e('Custom Block Content', 'rmcommon'); ?></label>
                                <textarea cols="45" rows="10" name="bk_content" id="bk-content" class="form-control"
                                      style="height: 300px;"><?php echo htmlspecialchars($block->getVar('content')); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="c-type" class="form-label"><?php _e('Content type:', 'rmcommon'); ?></label>
                                <select name="bk_ctype" id="c-type" class="form-select">
                                    <option value="TEXT"<?php echo 'TEXT' == $block->getVar('content_type') ? ' selected="selected"' : ''; ?>><?php _e('Formatted text', 'rmcommon'); ?></option>
                                    <option value="HTML"<?php echo 'HTML' == $block->getVar('content_type') ? ' selected="selected"' : ''; ?>><?php _e('HTML block', 'rmcommon'); ?></option>
                                    <option value="PHP"<?php echo 'PHP' == $block->getVar('content_type') ? ' selected="selected"' : ''; ?>><?php _e('PHP block', 'rmcommon'); ?></option>
                                    <option value="XOOPS"<?php echo 'XOOPS' == $block->getVar('content_type') ? ' selected="selected"' : ''; ?>><?php _e('XOOPS code', 'rmcommon'); ?></option>
                                </select>
                            </div>

                        <?php endif; ?>
                      </div>
                      <!--// End custom content -->
                  <?php endif; ?>

                </div>

            </div>

            <input type="hidden" name="action" value="saveconfig">
            <input type="hidden" name="bid" value="<?php echo $id; ?>">
            <input type="hidden" name="XOOPS_TOKEN_REQUEST" value="<?php echo $xoopsSecurity->createToken(); ?>">
        </fieldset>
    </form>

</div>
<div class="box-footer">
    <div class="d-flex justify-content-between align-items-center">
        <button type="button" onclick="blocksAjax.close();"
                class="btn btn-default btn-lg pull-left"><?php _e('Cancel', 'rmcommon'); ?></button>
        <button type="button" onclick="blocksAjax.sendConfig();"
                class="btn btn-lg btn-primary pull-right"><?php _e('Save Settings', 'rmcommon'); ?></button>
    </div>
</div>
