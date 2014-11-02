<h1 class="cu-section-title"><?php _e('Blocks Administration','rmcommon'); ?></h1>

<?php $from = rmc_server_var($_REQUEST,'from', '')=='positions'?true:false; ?>
<div class="row blocks-nav">
    <ul class="nav nav-pills">
        <li>
            <a href="#" id="newpos"><?php empty($positions) ? _e('Show Blocks','rmcommon') : _e('Show Positions','rmcommon'); ?></a>
        </li>
        <li class="active">
            <a href="#" id="newban"><?php _e('Add New Block','rmcommon'); ?></a>
            <div id="megamenu1" class="megamenu">
                <div class="menucont">
                    <?php $i = 0; ?>
                    <?php foreach($blocks as $dir => $block): ?>
                        <?php if(empty($block['blocks'])) continue; ?>
                        <?php $i++; ?>
                        <div class="column">
                            <h3><?php echo $block['name']; ?></h3>
                            <ul class="nav nav-pills">
                                <?php foreach($block['blocks'] as $id => $bk): ?>
                                    <li><a href="#" id="block-<?php echo $dir; ?>-<?php echo $bk['id']; ?>"><?php echo $bk['name']; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </li>
        <li>
            <a href="#" id="blocks-console-control">
                <span class="fa fa-bell"></span>
                <?php _e('Messages Console','rmcommon'); ?>
            </a>
            <div id="blocks-console">
                <div id="bk-messages"></div>
            </div>
        </li>
    </ul>
</div>

<!-- Positions Grid -->
<div class="row<?php echo empty($positions) || $from=='positions' ? ' hidden-control' : ''; ?>" id="blocks-list">

    <div class="col-lg-12">

        <?php foreach($positions as $pos): ?>
            <div id="position-<?php echo $pos['id']; ?>" class="dd rmc-position-item" data-pos="<?php echo $pos['id']; ?>">
                <h3 class="bg-warning"><?php echo $pos['name']; ?><img src="images/ok.png"></h3>
                <?php if(!isset($used_blocks[$pos['id']])): ?>
                    <div class="dd-empty"><?php _e('Drag and drop blocks here','rmcommon'); ?></div>
                <?php else: ?>
                    <ol class="dd-list">
                        <?php foreach($used_blocks[$pos['id']] as $block): ?>
                            <li class="dd-item<?php echo $block['visible'] ? '' : ' invisible-block'; ?>" data-action="<?php echo $block['visible'] ? 'hide-block' : ' show-block'; ?>" data-position="<?php echo $pos['id']; ?>" data-id="<?php echo $block['id']; ?>" id="block-<?php echo $block['id']; ?>">
                                <div class="row-fluid item-controls">
                                    <strong class="dd-handle"><?php echo $block['title']; ?></strong>
                                    <a href="#" class="pull-right text-error control-delete" data-block="<?php echo $block['id']; ?>" onclick="control_action( 'delete', <?php echo $block['id']; ?> );" title="<?php _e('Delete Block','rmcommon'); ?>"><i class="fa fa-minus-circle text-danger"></i></a>
                                    <?php if($block['visible']): ?>
                                        <a href="#" class="pull-right text-warning control-visible" data-block="<?php echo $block['id']; ?>" onclick="control_action( 'hide', <?php echo $block['id']; ?> );" title="<?php _e('Hide block','rmcommon'); ?>"><i class="fa fa-eye-slash"></i></a>
                                    <?php else: ?>
                                        <a href="#" class="pull-right text-success control-visible" data-block="<?php echo $block['id']; ?>" onclick="control_action( 'show', <?php echo $block['id']; ?> );" title="<?php _e('Show block','rmcommon'); ?>"><i class="fa fa-eye"></i></a>
                                    <?php endif; ?>
                                    <a href="#" class="pull-right control-settings" data-block="<?php echo $block['id']; ?>" onclick="control_action( 'settings', <?php echo $block['id']; ?> );" title="<?php _e('Block Settings','rmcommon'); ?>"><i class="fa fa-wrench"></i></a>
                                </div>
                                <div class="row-fluid block-data">
                                    <strong class="text-primary"><?php echo $block['module']['name']; ?></strong>
                                    <p class="help-block"><small><?php echo $block['description']; ?></small></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>

    </div>

</div>
<input type="hidden" value="<?php echo $xoopsSecurity->createToken(); ?>" id="token-positions" name="token_positions">
<!--// End positions grid -->

<div id="bks-and-pos" class="row-fluid<?php echo empty($positions) || $from=='positions' ? ' visible-control' : ' hidden-control'; ?>">
<div id="form-pos" class="col-md-3 col-lg-3">
    <form name="frmaddpos" id="frm-add-pos" method="post" action="blocks.php" />
        <fieldset>
            <legend>Add Position</legend>

            <div class="form-group">
                <label><?php _e('Name','rmcommon'); ?></label>
                <input type="text" name="posname" value="" class="form-control" required>
                <span class="help-block"><?php _e('Input a name to identify this position (<em>eg. Left blocks</em>)','rmcommon'); ?></span>
            </div>

            <div class="form-group">
                <label><?php _e('Tag Name','rmcommon'); ?></label>
                <input type="text" name="postag" value="" class="form-control" required>
                <span class="help-block"><?php _e('Specify a name for the smarty tag to use in templates (eg. left_blocks). This tag will be used as Smarty tag (eg. &lt;{$left_blocks}&gt).','rmcommon'); ?></span>
            </div>

            <input type="submit" class="btn btn-info btn-large" name="bk_add_pos" id="add-position" value="<?php _e('Add Position','rmcommon'); ?>" />
            <input type="hidden" name="action" value="save_position" />
            <?php echo $xoopsSecurity->getTokenHTML(); ?>

                <h4><?php _e('How to implement blocks','rmcommon'); ?></h4>
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
            <select name="action" id="bulk-top" class="form-control">
                <option value=""><?php _e('Bulk actions...','rmcommon'); ?></option>
                <option value="active"><?php _e('Active','rmcommon'); ?></option>
                <option value="inactive"><?php _e('Inactive','rmcommon'); ?></option>
                <option value="deletepos"><?php _e('Delete','rmcommon'); ?></option>
            </select>
            <button type="button" class="btn btn-default" id="the-op-topp" onclick="before_submit('frm-positions');"><?php _e('Apply','rmcommon'); ?></button>
        </div>
        <table class="table table-striped table-bordered" border="0" id="table-positions">
            <thead>
            <tr>
                <th width="30"><input type="checkbox" id="checkallp" onclick="$('#frm-positions').toggleCheckboxes(':not(#checkallp)');" /></th>
                <th width="30" align="left"><?php _e('ID','rmcommon'); ?></th>
                <th align="left"><?php _e('Name','rmcommon'); ?></th>
                <th><?php _e('Smarty Tag','rmcommon'); ?></th>
                <th><?php _e('Active','rmcommon'); ?></th>
            </tr>
            <thead>
            <tfoot>
            <tr>
                <th width="30"><input type="checkbox" id="checkallpb" onclick="$('#frm-positions').toggleCheckboxes(':not(#checkallpb)');" /></th>
                <th width="50" align="left"><?php _e('ID','rmcommon'); ?></th>
                <th align="left"><?php _e('Name','rmcommon'); ?></th>
                <th><?php _e('Smarty Tag','rmcommon'); ?></th>
                <th><?php _e('Active','rmcommon'); ?></th>
            </tr>
            <tfoot>
            <tbody>

            <?php if(empty($positions)): ?>
            <tr>
                <td colspan="5" class="text-center">
                    <span class="label label-info">
                        <?php _e('There are not positions created yet!','rmcommon'); ?>
                    </span>
                </td>
            </tr>
            <?php endif; ?>

            <?php foreach($positions as $pos): ?>
                <tr class="<?php echo tpl_cycle('even,odd'); ?>" id="ptr-<?php echo $pos['id']; ?>" valign="top">
                    <td align="center"><input type="checkbox" name="ids[]" id="itemp-<?php echo $pos['id']; ?>" value="<?php echo $pos['id']; ?>" /></td>
                    <td align="left"><strong><?php echo $pos['id']; ?></strong></td>
                    <td>
                        <span class="name"><?php echo $pos['name']; ?></span>
                        <span class="cu-item-options">
                            <a href="#" onclick="select_option(<?php echo $pos['id']; ?>, 'delete', 'frm-positions')"><?php _e('Delete','rmcommon'); ?></a> |
                            <a href="#" class="edit_position"><?php _e('Edit','rmcommon'); ?></a>
                        </span>
                        <span class="pos_data hide">
                            <span class="name"><?php echo $pos['name']; ?></span>
                            <span class="ptag"><?php echo $pos['tag']; ?></span>
                            <span class="active"><?php echo $pos['active']; ?></span>
                        </span>
                    </td>
                    <td align="center">&lt;{$xoBlocks.<span class="ptag"><?php echo $pos['tag']; ?></span>}&gt;</td>
                    <td align="center"><span class="fa <?php echo $pos['active'] == 1 ? 'fa-check text-success' : 'fa-times text-danger'; ?>"></span></td>
                </tr>    
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cu-bulk-actions">
            <select name="actionb" class="form-control" id="bulk-bottomp">
                <option value=""><?php _e('Bulk actions...','rmcommon'); ?></option>
                <option value="active"><?php _e('Active','rmcommon'); ?></option>
                <option value="inactive"><?php _e('Inactive','rmcommon'); ?></option>
                <option value="deletepos"><?php _e('Delete','rmcommon'); ?></option>
            </select>
            <button type="button" class="btn btn-default" id="the-op-topp" onclick="before_submit('frm-positions');"><?php _e('Apply','rmcommon'); ?></button>
        </div>
        <?php echo $xoopsSecurity->getTokenHTML(); ?>
    </form>
</div>
</div>
<!--/ Positions -->

<div id="settings-blocker"></div>
<div id="settings-loading"><img src="images/loadinga.gif" width="16" height="16" alt="<?php _e('Loading','rmcomon'); ?>" /><?php _e('Loading data...','rmcommon'); ?></div>
<div id="settings-form-window">
    
</div>

<script type="text/javascript">
    $(document).ready(function(){
        <?php foreach($positions as $pos): ?>
        $("#position-<?php echo $pos['id']; ?>").nestable({
            group: 1,
            maxDepth: 1
        }).on('change', blocksAjax.saveOrder);
        <?php endforeach; ?>
    });
</script>
