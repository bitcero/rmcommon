<div id="bks-and-pos" class="row">
    <!-- Positions -->
    <form name="formPos" id="frm-positions" method="post" action="blocks.php" class="form-inline">
        <div class="cu-bulk-actions d-flex justify-content-end">
            <div class="d-flex align-items-center">
                <select name="action" id="bulk-top" class="form-select me-2"
                        onchange="$('#bulk-bottom').val($(this).val());">
                    <option value=""><?php _e('Bulk actions...', 'rmcommon'); ?></option>
                    <option value="active"><?php _e('Active', 'rmcommon'); ?></option>
                    <option value="inactive"><?php _e('Inactive', 'rmcommon'); ?></option>
                    <option value="deletepos"><?php _e('Delete', 'rmcommon'); ?></option>
                </select>
                <button type="button" class="btn btn-primary" id="the-op-topp" onclick="before_submit('frm-positions');">
                  <?php _e('Apply', 'rmcommon'); ?>
                </button>
            </div>
        </div>
        <table class="table table-hover no-margin" id="table-positions">
            <thead>
            <tr>
                <th>
                    <input type="checkbox" id="checkallp" data-checkbox="positions" data-oncheck="positions">
                </th>
                <th><?php _e('ID', 'rmcommon'); ?></th>
                <th><?php _e('Name', 'rmcommon'); ?></th>
                <th class="text-center"><?php _e('Smarty Tag', 'rmcommon'); ?></th>
                <th><?php _e('Active', 'rmcommon'); ?></th>
                <th class="text-center"><?php _e('Options', 'rmcommon'); ?></th>
            </tr>
            <thead>
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
                <tr class="<?php echo tpl_cycle('even,odd'); ?>" id="ptr-<?php echo $pos['id']; ?>">
                    <td><input type="checkbox" name="ids[]"
                               id="itemp-<?php echo $pos['id']; ?>"
                               value="<?php echo $pos['id']; ?>"
                               data-oncheck="positions"></td>
                    <td><strong><?php echo $pos['id']; ?></strong></td>
                    <td>
                        <span class="name"><?php echo $pos['name']; ?></span>
                        <span class="pos_data hide">
                            <span class="name"><?php echo $pos['name']; ?></span>
                            <span class="ptag"><?php echo $pos['tag']; ?></span>
                            <span class="active"><?php echo $pos['active']; ?></span>
                        </span>
                    </td>
                    <td class="text-center font-monospace">&lt;{$xoBlocks.<span
                                class="ptag"><?php echo $pos['tag']; ?></span>}&gt;
                    </td>
                    <td><span
                                class="fa <?php echo 1 == $pos['active'] ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></span>
                    </td>
                    <td class="item-options">
                        <div class="d-flex align-items-center justify-content-center">
                            <a href="#" class="edit_position btn btn-link text-warning" title="<?php _e('Edit', 'rmcommon'); ?>">
                              <?php echo $common->icons()->svg('lithium-edit'); ?>
                            </a>
                            <a href="#"
                               onclick="select_option(<?php echo $pos['id']; ?>, 'delete', 'frm-positions')"
                               title="<?php _e('Delete', 'rmcommon'); ?>"
                                class="btn btn-link text-danger">
                              <?php echo $common->icons()->svg('lithium-delete'); ?>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
      <?php echo $xoopsSecurity->getTokenHTML(); ?>
    </form>

</div>

<div class="modal attach-to-body fade" id="positions-modal-form">
    <div class="modal-dialog">
        <form name="frmaddpos" id="frm-add-pos" method="post" action="blocks.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php _e('Add Position', 'rmcommon'); ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <fieldset>
                        <div class="mb-3">
                            <label class="form-label" for="position-name"><?php _e('Name', 'rmcommon'); ?></label>
                            <input type="text" id="position-name" name="posname" value="" class="form-control" required>
                            <div class="form-text"><?php _e('Input a name to identify this position (<em>eg. Left blocks</em>)', 'rmcommon'); ?></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="position-tag"><?php _e('Tag Name', 'rmcommon'); ?></label>
                            <input type="text" id="position-tag" name="postag" value="" class="form-control" required>
                            <div class="form-text">
                              <?php _e('Specify a name for the smarty tag to use in templates (eg. left_blocks). This tag will be used as Smarty tag (eg. <code>&lt;{$left_blocks}&gt</code>).', 'rmcommon'); ?>
                            </div>
                        </div>

                        <input type="hidden" name="action" value="save_position">
                      <?php echo $xoopsSecurity->getTokenHTML(); ?>

                        <hr>
                        <h5><?php _e('How to implement blocks', 'rmcommon'); ?></h5>
                        <pre>&lt;{foreach item="block" from=$xoBlocks.<em><strong>tag_name</strong></em>}&gt;
    &lt;{$block.title}&gt;
    &lt;{$block.content}&gt;
&lt;{/foreach}&gt;</pre>
                    </fieldset>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php _e('Cancel', 'rmcommon'); ?></button>
                    <button type="button" class="btn btn-primary"
                            id="save-position"><?php _e('Add Position', 'rmcommon'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>