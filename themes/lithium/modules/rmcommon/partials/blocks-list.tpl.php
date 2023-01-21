<div class="ps-5 pe-5 pt-2 pb-4" id="blocks-list">
  <div class="d-flex justify-content-between">
    <div id="blocks-available">
      <p><?php _e('Select from the following list the block or blocks you want to add.', 'rmcommon'); ?></p>
      <?php $i = 0; ?>
      <?php foreach ($blocks as $dir => $block): ?>
        <?php if (empty($block['blocks'])) {
          continue;
        } ?>
        <?php $i++; ?>
        <div class="new-module">
          <h5 class="d-flex align-items-center">
            <?php if ('' != $block['icon']) {
              echo $cuIcons->getIcon($block['icon'], [], false);
            } ?>
            <?php echo $block['name']; ?>
          </h5>
          <ul>
            <?php foreach ($block['blocks'] as $id => $bk): ?>
              <li class="mb-2">
                <a href="#" id="block-<?php echo $dir; ?>-<?php echo $bk['id']; ?>">
                  <?php echo $bk['name']; ?>
                  <?php if (array_key_exists('description', $bk) && '' != $bk['description']): ?>
                    <small><?php echo $bk['description']; ?></small>
                  <?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="available-positions">

      <?php foreach ($positions as $pos): if ($pos['active'] <= 0) {
        continue;
      } ?>
        <div class="position-item-container">
          <div id="position-<?php echo $pos['id']; ?>" class="cu-box rmc-position-item"
               data-id="<?php echo $pos['id']; ?>">
            <div class="box-header">
              <span class="fa fa-caret-up box-handler"></span>
              <h3 class="box-title d-flex align-items-center">
                <?php echo $cuIcons->svg('svg-rmcommon-ok-circle', ['class' => 'success-icon']); ?>
                <?php echo $pos['name']; ?>
              </h3>
            </div>
            <div class="dd box-content collapsable" data-pos="<?php echo $pos['id']; ?>">

              <?php if (!isset($used_blocks[$pos['id']])): ?>
                <div class="dd-empty"><?php _e('Drag and drop blocks here', 'rmcommon'); ?></div>
              <?php else: ?>
                <ol class="dd-list">
                  <?php foreach ($used_blocks[$pos['id']] as $block): ?>
                    <li class="dd-item<?php echo $block['visible'] ? '' : ' invisible-block'; ?>"
                        data-action="<?php echo $block['visible'] ? 'hide-block' : ' show-block'; ?>"
                        data-position="<?php echo $pos['id']; ?>"
                        data-id="<?php echo $block['id']; ?>"
                        id="block-<?php echo $block['id']; ?>">
                      <div class="item-controls d-flex justify-content-between align-items-center p-2">
                        <strong class="dd-handle"
                                title="<?php echo sprintf(__('Module: %s', 'rmcommon'), $block['module']['name']); ?>">
                          <?php echo $cuIcons->getIcon($block['module']['icon']); ?>
                          <?php echo $block['title']; ?>
                        </strong>

                        <div class="d-flex align-items-center">
                          <?php if ($block['visible']): ?>
                            <a href="#" class="pull-right text-warning control-visible text-warning"
                               data-block="<?php echo $block['id']; ?>"
                               onclick="control_action( 'hide', <?php echo $block['id']; ?> );"
                               title="<?php _e('Hide block', 'rmcommon'); ?>">
                              <?php echo $common->icons()->svg('lithium-hide'); ?>
                            </a>
                          <?php else: ?>
                            <a href="#" class="pull-right text-success control-visible text-success"
                               data-block="<?php echo $block['id']; ?>"
                               onclick="control_action( 'show', <?php echo $block['id']; ?> );"
                               title="<?php _e('Show block', 'rmcommon'); ?>">
                              <?php echo $common->icons()->svg('lithium-show'); ?>
                            </a>
                          <?php endif; ?>
                          <a href="#" class="pull-right control-settings text-blue-grey"
                             data-block="<?php echo $block['id']; ?>"
                             onclick="control_action( 'settings', <?php echo $block['id']; ?> );"
                             title="<?php _e('Block Settings', 'rmcommon'); ?>">
                            <?php echo $common->icons()->svg('lithium-setting'); ?>
                          </a>
                          <a href="#" class="pull-right text-error control-delete text-danger"
                             data-block="<?php echo $block['id']; ?>"
                             onclick="control_action( 'delete', <?php echo $block['id']; ?> );"
                             title="<?php _e('Delete Block', 'rmcommon'); ?>">
                            <?php echo $common->icons()->svg('lithium-remove'); ?>
                          </a>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ol>
              <?php endif; ?>

            </div>

          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

</div>