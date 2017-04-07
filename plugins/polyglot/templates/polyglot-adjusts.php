<div role="tabpanel" class="tab-pane active" id="adjustments">

    <form name="frmAdjusts" id="frm-adjustments" action="plugins.php?p=polyglot" method="post">
        <div class="table-responsive">

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th><?php _e('Language', 'polyglot'); ?></th>
                    <th class="text-center"><?php _e('Code', 'polyglot'); ?></th>
                    <th class="text-center"><?php _e('charset', 'polyglot'); ?></th>
                    <th class="text-center"><?php _e('Direction', 'polyglot'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(empty($languages)): ?>
                    <tr>
                        <td colspan="5" class="text-center">
                    <span class="label label-info">
                        <?php _e('There are not languages registered yet!', 'polyglot'); ?>
                    </span>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach($languages as $code => $lang): ?>
                    <tr>
                        <td class="text-center">
                            <?php if($code == $plugin->baseLanguage()): ?>
                                <?php echo $cuIcons->getIcon('svg-rmcommon-star text-orange'); ?>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $lang['name']; ?>
                        </td>
                        <td class="text-center">
                            <?php echo $code; ?>
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control text-center" name="langs[<?php echo $code; ?>][charset]" value="<?php echo array_key_exists('charset', $lang) ? $lang['charset'] : 'UTF-8'; ?>"
                        </td>
                        <td class="text-center">
                            <label>
                                <input type="checkbox" name="langs[<?php echo $code; ?>][rtl]" value="rtl"<?php echo array_key_exists('rtl', $lang) ? ' checked' : ''; ?>>
                                <?php _e('Right to left', 'polyglot'); ?>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>

        <div class="form-group text-center">
            <button class="btn btn-primary btn-lg" type="submit">
                <?php _e('Save Adjustments', 'polyglot'); ?>
            </button>
        </div>
        <input type="hidden" name="page" value="adjust">
        <input type="hidden" name="action" value="save">

    </form>

</div>