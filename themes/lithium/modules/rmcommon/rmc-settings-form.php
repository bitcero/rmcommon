<h1 class="cu-section-title">
  <?php echo sprintf(__('%s Settings', 'rmcommon'), $module->getVar('name')); ?>
</h1>

<?php $sufix = time(); ?>

<div class="cu-box">
    <div class="box-header">
        <h5 class="box-title">
          <?php echo sprintf(__('%s Settings', 'rmcommon'), $module->getVar('name')); ?>
        </h5>
    </div>
  <?php
  $form = new RMForm('', '', '');
  ?>
    <form name="formSettings" id="form-settings" method="post"
          action="<?php echo RMCURL; ?>/settings.php"<?php echo $is_popup ? ' data-type="ajax"' : ''; ?>>
        <div class="box-content">
            <!-- Categories tabs -->
            <ul class="nav nav-tabs nav-tabs-color cu-top-tabs" role="tablist">
              <?php
              $i = 0;
              foreach ($categories as $id => $category): ?>
                  <li class="nav-item">
                      <button
                          class="nav-link <?php echo 0 == $i ? 'active' : ''; ?>"
                          href="#category-<?php echo $id; ?>-<?php echo $sufix; ?>"
                          data-bs-toggle="tab"
                          data-bs-target="#category-<?php echo $id; ?>-<?php echo $sufix; ?>"
                          title="<?php echo $category['caption']; ?>"
                          role="tab"
                          aria-controls="category-<?php echo $id; ?>-<?php echo $sufix; ?>"
                          <?php echo 0 == $i ? 'aria-selected="true"' : ''; ?>
                          type="button"
                      >
                        <?php if (array_key_exists('icon', $category)): ?>
                            <div class="d-flex align-items-center">
                              <?php echo $cuIcons->getIcon($category['icon'], [], false); ?>
                                <span class="caption"><?php echo $category['caption']; ?></span>
                            </div>
                        <?php else: ?>
                          <?php echo $category['caption']; ?>
                        <?php endif; ?>
                      </button>
                  </li>
                <?php
                $i++;
              endforeach; ?>
            </ul>
            <div class="tab-content">

              <?php
              $i = 0;
              foreach ($categories as $id => $category):
                ?>
                  <div class="tab-pane fade show<?php echo 0 == $i ? ' active' : ''; ?>"
                       id="category-<?php echo $id; ?>-<?php echo $sufix; ?>">

                    <?php if (!isset($category['fields'])): ?>
                        <span class="label label-danger"><?php _e('There are not fields in this category.', 'rmcommon'); ?></span>
                    <?php else: ?>
                      <?php foreach ($category['fields'] as $ids => $field): ?>

                        <?php if ('hidden' == $field->field): ?>

                          <?php echo RMSettings::render_field($field); ?>

                        <?php else: ?>

                                <div class="row mb-4">
                                    <div class="col-12 col-md-4 col-lg-4">
                                        <label for="<?php echo $ids; ?>"><?php echo $field->caption; ?></label>
                                    </div>
                                    <div class="col-12 col-md-8 col-lg-8">
                                      <?php echo RMSettings::render_field($field); ?>
                                      <?php if ('' != $field->description): ?>
                                          <div class="form-text"><?php echo $field->description; ?></div>
                                      <?php endif; ?>
                                    </div>
                                </div>

                        <?php endif; ?>

                      <?php endforeach; ?>
                    <?php endif; ?>

                  </div>
                <?php
                $i++;
              endforeach;
              ?>

            </div>

            <!--// Categories -->

            <input type="hidden" name="action" value="save-settings">
            <input type="hidden" name="mod" value="<?php echo $module->mid(); ?>">
          <?php if ($is_popup): ?>
              <input type="hidden" name="via_ajax" value="1">
          <?php else: ?>
            <?php echo $xoopsSecurity->getTokenHTML(); ?>
          <?php endif; ?>
        </div>
        <div class="box-footer d-flex align-items-center justify-content-between p-4">
            <button type="button" class="btn btn-link fw-bold"
                    onclick="<?php if ($is_popup): ?>$('#cu-settings-form').modal('hide');<?php else: ?>window.location.href='<?php echo RMUris::anchor($module->getVar('dirname')); ?>';<?php endif; ?>"><?php _e('Cancel', 'rmcommon'); ?></button>
            <button type="submit" class="btn btn-primary"><?php _e('Save Changes', 'rmcommon'); ?></button>
        </div>
    </form>
</div>
