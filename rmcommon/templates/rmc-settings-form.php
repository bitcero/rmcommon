<?php if (!$is_popup): ?>
<h1 class="cu-section-title">
    <?php echo sprintf(__('%s Settings', 'rmcommon'), $module->getVar('name')); ?>
</h1>
<?php endif; ?>

<?php $sufix = time(); ?>

<!-- Categories tabs -->
<ul class="nav nav-tabs nav-tabs-color cu-top-tabs">
    <?php
    $i = 0;
    foreach ($categories as $id => $category): ?>
        <li<?php echo 0 == $i ? ' class="active"' : ''; ?>>
            <a href="#category-<?php echo $id; ?>-<?php echo $sufix; ?>" data-toggle="tab" title="<?php echo $category['caption']; ?>">
                <?php if (array_key_exists('icon', $category)): ?>
                    <?php echo $cuIcons->getIcon($category['icon']); ?>
                    <span class="caption"><?php echo $category['caption']; ?></span>
                <?php else: ?>
                    <?php echo $category['caption']; ?>
                <?php endif; ?>
            </a>
        </li>
        <?php
        $i++;
    endforeach; ?>
</ul>

<div class="cu-content-with-footer">

    <?php
    $form = new RMForm('', '', '');
    ?>
<form name="formSettings" id="form-settings" method="post" action="<?php echo RMCURL; ?>/settings.php"<?php echo $is_popup ? ' data-type="ajax"' : ''; ?>>
    <div class="tab-content">

        <?php
        $i = 0;
        foreach ($categories as $id => $category):
            ?>
            <div class="tab-pane fade in<?php echo 0 == $i ? ' active' : ''; ?>" id="category-<?php echo $id; ?>-<?php echo $sufix; ?>">

                <?php if (!isset($category['fields'])): ?>
                    <span class="label label-danger"><?php _e('There are not fields in this category.', 'rmcommon'); ?></span>
                <?php else: ?>
                    <?php foreach ($category['fields'] as $id => $field): ?>

                        <?php if ('hidden' == $field->field): ?>

                            <?php echo RMSettings::render_field($field); ?>

                        <?php else: ?>

                            <div class="row form-group">
                                <div class="col-md-4 col-lg-4">
                                    <label for="<?php echo $id; ?>"><?php echo $field->caption; ?></label>
                                    <?php if ('' != $field->description): ?>
                                        <span class="help-block"><?php echo $field->description; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8 col-lg-8">
                                    <?php echo RMSettings::render_field($field); ?>
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

    <div class="cu-content-footer">
        <div class="row">
            <div class="col-lg-12 text-right">
                <button type="button" class="btn btn-default" onclick="<?php if ($is_popup): ?>$('#cu-settings-form').modal('hide');<?php else: ?>window.location.href='<?php echo RMUris::anchor($module->getVar('dirname')); ?>';<?php endif; ?>"><?php _e('Cancel', 'rmcommon'); ?></button>
                <button type="submit" class="btn btn-primary"><?php _e('Save Changes', 'rmcommon'); ?></button>
            </div>
        </div>
    </div>
    <input type="hidden" name="action" value="save-settings">
    <input type="hidden" name="mod" value="<?php echo $module->mid(); ?>">
    <?php if ($is_popup): ?>
    <input type="hidden" name="via_ajax" value="1">
    <?php else: ?>
        <?php echo $xoopsSecurity->getTokenHTML(); ?>
    <?php endif; ?>
</form>
</div>
