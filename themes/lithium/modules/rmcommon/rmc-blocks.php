<h1 class="cu-section-title"><?php _e('Blocks Administration', 'rmcommon'); ?></h1>

<?php $from = 'positions' == $common->httpRequest()::request('from', 'string', '') ? true : false; ?>

<div class="cu-box">
    <div class="box-content no-padding">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                        class="nav-link"
                        id="position-tab"
                        type="button"
                        aria-controls="positions"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#positions">
                  <?php _e('Positions', 'rmcommon'); ?>
                </button>
            </li>
            <li class="nav-item">
                <button
                        class="nav-link active"
                        id="blocks-tab"
                        type="button"
                        aria-controls="blocks"
                        aria-selected="true"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#blocks"
                >
                  <?php _e('Blocks', 'rmcommon'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                        class="nav-link"
                        id="console-tab"
                        type="button"
                        aria-controls="console"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#console">
                  <?php echo $common->icons()->svg('lithium-terminal'); ?>
                  <?php _e('Console', 'rmcommon'); ?>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="blocks-tab-content">
            <div role="tabpanel" class="tab-pane fade" aria-labelledby="position-tab" id="positions">

                <div id="bks-and-pos" class="row">
                    <div id="form-pos" class="col-md-3 col-lg-3">
                        <form name="frmaddpos" id="frm-add-pos" method="post" action="blocks.php">
                            <fieldset>
                                <legend>Add Position</legend>

                                <div class="form-group">
                                    <label><?php _e('Name', 'rmcommon'); ?></label>
                                    <input type="text" name="posname" value="" class="form-control" required>
                                    <span class="help-block"><?php _e('Input a name to identify this position (<em>eg. Left blocks</em>)', 'rmcommon'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label><?php _e('Tag Name', 'rmcommon'); ?></label>
                                    <input type="text" name="postag" value="" class="form-control" required>
                                    <span class="help-block"><?php _e('Specify a name for the smarty tag to use in templates (eg. left_blocks). This tag will be used as Smarty tag (eg. &lt;{$left_blocks}&gt).', 'rmcommon'); ?></span>
                                </div>

                                <input type="submit" class="btn btn-info btn-large" name="bk_add_pos" id="add-position"
                                       value="<?php _e('Add Position', 'rmcommon'); ?>">
                                <input type="hidden" name="action" value="save_position">
                              <?php echo $xoopsSecurity->getTokenHTML(); ?>

                                <h4><?php _e('How to implement blocks', 'rmcommon'); ?></h4>
                                <pre>&lt;{foreach item="block" from=$xoBlocks.<em><strong>tag_name</strong></em>}&gt;
    &lt;{$block.title}&gt;
    &lt;{$block.content}&gt;
&lt;{/foreach}&gt;</pre>
                            </fieldset>
                        </form>
                    </div>

                    <!-- Positions -->
                    <div id="blocks-positions" class="col-md-9 col-lg-9">
                        <form name="formPos" id="frm-positions" method="post" action="blocks.php" class="form-inline">
                            <div class="cu-bulk-actions">
                                <select name="action" id="bulk-top" class="form-control"
                                        onchange="$('#bulk-bottom').val($(this).val());">
                                    <option value=""><?php _e('Bulk actions...', 'rmcommon'); ?></option>
                                    <option value="active"><?php _e('Active', 'rmcommon'); ?></option>
                                    <option value="inactive"><?php _e('Inactive', 'rmcommon'); ?></option>
                                    <option value="deletepos"><?php _e('Delete', 'rmcommon'); ?></option>
                                </select>
                                <button type="button" class="btn btn-default" id="the-op-topp"
                                        onclick="before_submit('frm-positions');"><?php _e('Apply', 'rmcommon'); ?></button>
                            </div>
                            <table class="table table-striped table-bordered" border="0" id="table-positions">
                                <thead>
                                <tr>
                                    <th width="30"><input type="checkbox" id="checkallp" data-checkbox="positions"
                                                          data-oncheck="positions"></th>
                                    <th width="30" align="left"><?php _e('ID', 'rmcommon'); ?></th>
                                    <th align="left"><?php _e('Name', 'rmcommon'); ?></th>
                                    <th><?php _e('Smarty Tag', 'rmcommon'); ?></th>
                                    <th><?php _e('Active', 'rmcommon'); ?></th>
                                </tr>
                                <thead>
                                <tfoot>
                                <tr>
                                    <th width="30"><input type="checkbox" id="checkallpb" data-checkbox="positions"
                                                          data-oncheck="positions"></th>
                                    <th width="50" align="left"><?php _e('ID', 'rmcommon'); ?></th>
                                    <th align="left"><?php _e('Name', 'rmcommon'); ?></th>
                                    <th><?php _e('Smarty Tag', 'rmcommon'); ?></th>
                                    <th><?php _e('Active', 'rmcommon'); ?></th>
                                </tr>
                                <tfoot>
                                <tbody>

                                <?php if (empty($positions)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">
                    <span class="label label-info">
                        <?php _e('There are not positions created yet!', 'rmcommon'); ?>
                    </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($positions as $pos): ?>
                                    <tr class="<?php echo tpl_cycle('even,odd'); ?>" id="ptr-<?php echo $pos['id']; ?>"
                                        valign="top">
                                        <td align="center"><input type="checkbox" name="ids[]"
                                                                  id="itemp-<?php echo $pos['id']; ?>"
                                                                  value="<?php echo $pos['id']; ?>"
                                                                  data-oncheck="positions"></td>
                                        <td align="left"><strong><?php echo $pos['id']; ?></strong></td>
                                        <td>
                                            <span class="name"><?php echo $pos['name']; ?></span>
                                            <span class="cu-item-options">
                            <a href="#"
                               onclick="select_option(<?php echo $pos['id']; ?>, 'delete', 'frm-positions')"><?php _e('Delete', 'rmcommon'); ?></a> |
                            <a href="#" class="edit_position"><?php _e('Edit', 'rmcommon'); ?></a>
                        </span>
                                            <span class="pos_data hide">
                            <span class="name"><?php echo $pos['name']; ?></span>
                            <span class="ptag"><?php echo $pos['tag']; ?></span>
                            <span class="active"><?php echo $pos['active']; ?></span>
                        </span>
                                        </td>
                                        <td align="center">&lt;{$xoBlocks.<span
                                                    class="ptag"><?php echo $pos['tag']; ?></span>}&gt;
                                        </td>
                                        <td align="center"><span
                                                    class="fa <?php echo 1 == $pos['active'] ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="cu-bulk-actions">
                                <select name="actionb" class="form-control" id="bulk-bottom"
                                        onchange="$('#bulk-top').val($(this).val());">
                                    <option value=""><?php _e('Bulk actions...', 'rmcommon'); ?></option>
                                    <option value="active"><?php _e('Active', 'rmcommon'); ?></option>
                                    <option value="inactive"><?php _e('Inactive', 'rmcommon'); ?></option>
                                    <option value="deletepos"><?php _e('Delete', 'rmcommon'); ?></option>
                                </select>
                                <button type="button" class="btn btn-default" id="the-op-topp"
                                        onclick="before_submit('frm-positions');"><?php _e('Apply', 'rmcommon'); ?></button>
                            </div>
                          <?php echo $xoopsSecurity->getTokenHTML(); ?>
                        </form>
                    </div>
                </div>

            </div>

            <div role="tabpanel" class="tab-pane fade show active" aria-labelledby="blocks-tab" id="blocks">

                <!-- Positions Grid -->
                <div class="row" id="blocks-list">
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
                                          <h3 class="box-title"><?php echo $pos['name']; ?><?php echo $cuIcons->getIcon('svg-rmcommon-ok-circle text-success'); ?></h3>
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
                                                      <div class="row item-controls">
                                                          <strong class="dd-handle"
                                                                  title="<?php echo sprintf(__('Module: %s', 'rmcommon'), $block['module']['name']); ?>">
                                                            <?php echo $cuIcons->getIcon($block['module']['icon']); ?>
                                                            <?php echo $block['title']; ?>
                                                          </strong>

                                                          <div class="d-flex justify-content-between align-items-center">
                                                              <a href="#" class="pull-right text-error control-delete"
                                                                 data-block="<?php echo $block['id']; ?>"
                                                                 onclick="control_action( 'delete', <?php echo $block['id']; ?> );"
                                                                 title="<?php _e('Delete Block', 'rmcommon'); ?>"><i
                                                                          class="fa fa-minus-circle text-danger"></i></a>
                                                            <?php if ($block['visible']): ?>
                                                                <a href="#" class="pull-right text-warning control-visible"
                                                                   data-block="<?php echo $block['id']; ?>"
                                                                   onclick="control_action( 'hide', <?php echo $block['id']; ?> );"
                                                                   title="<?php _e('Hide block', 'rmcommon'); ?>"><i
                                                                            class="fa fa-eye-slash"></i></a>
                                                            <?php else: ?>
                                                                <a href="#" class="pull-right text-success control-visible"
                                                                   data-block="<?php echo $block['id']; ?>"
                                                                   onclick="control_action( 'show', <?php echo $block['id']; ?> );"
                                                                   title="<?php _e('Show block', 'rmcommon'); ?>"><i
                                                                            class="fa fa-eye"></i></a>
                                                            <?php endif; ?>
                                                              <a href="#" class="pull-right control-settings"
                                                                 data-block="<?php echo $block['id']; ?>"
                                                                 onclick="control_action( 'settings', <?php echo $block['id']; ?> );"
                                                                 title="<?php _e('Block Settings', 'rmcommon'); ?>"><i
                                                                          class="fa fa-wrench"></i></a>
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

            </div>

            <div role="tabpanel" class="tab-pane" aria-labelledby="console-tab" id="console">
                <div id="blocks-console">
                    <ul id="bk-messages" class="list-unstyled"></ul>
                </div>
            </div>
        </div>
    </div>
</div>


<input type="hidden" value="<?php echo $xoopsSecurity->createToken(); ?>" id="token-positions" name="token_positions">
<!--// End positions grid -->


<!--/ Positions -->
<script type="text/x-jsrender" id="loading-tpl">
    <div class="d-flex justify-content-center align-items-center">
        <?php echo $common->icons()->svg('svg-rmcommon-spinner-02'); ?>
        <?php _e('Loading data...', 'rmcommon'); ?>
    </div>
</script>

<script type="text/x-jsrender" id="console-log-item">
    <li class="console-item {{:type}}">
        {{:message}}
    </li>

</script>

<script type="text/javascript">
  $(document).ready(function () {
    <?php foreach ($positions as $pos): ?>
    $("#position-<?php echo $pos['id']; ?> .box-content").nestable({
      group: 1,
      maxDepth: 1
    }).on('change', blocksAjax.saveOrder);
    <?php endforeach; ?>
  });
</script>
