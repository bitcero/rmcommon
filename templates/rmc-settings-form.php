<?php if ( !$is_popup ): ?>
<h1 class="cu-section-title">
    <?php echo sprintf( __('%s Settings', 'rmcommon'), $module->getVar('name') ); ?>
</h1>
<?php endif; ?>

<?php $sufix = time(); ?>

<div class="cu-content-with-footer">
    <!-- Categories tabs -->
    <ul class="nav nav-tabs cu-top-tabs">
        <?php
        $i = 0;
        foreach( $categories as $id => $category): ?>
            <li<?php echo $i==0 ? ' class="active"' : ''; ?>><a href="#category-<?php echo $id; ?>-<?php echo $sufix; ?>" data-toggle="tab"><?php echo $category['caption']; ?></a></li>
            <?php
            $i++;
        endforeach; ?>
    </ul>

    <?php
    $form = new RMForm('','','');
    ?>
<form name="formSettings" id="form-settings" method="post" action="settings.php"<?php echo $is_popup ? ' class="ajax-form"' : ''; ?>>
    <div class="tab-content">

        <?php
        $i = 0;
        foreach( $categories as $id => $category ):
            ?>
            <div class="tab-pane fade in<?php echo $i==0 ? ' active' : ''; ?>" id="category-<?php echo $id; ?>-<?php echo $sufix; ?>">

                <?php if( !isset( $category['fields'] ) ): ?>
                    <span class="label label-danger"><?php _e('There are not fields in this category.', 'rmcommon'); ?></span>
                <?php else: ?>
                    <?php foreach($category['fields'] as $id => $field): ?>

                        <div class="row form-group">
                            <div class="col-md-4 col-lg-4">
                                <label for="<?php echo $id; ?>"><?php echo $field->caption; ?></label>
                                <?php if( $field->description != '' ): ?>
                                    <span class="help-block"><?php echo $field->description; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8 col-lg-8">
                                <?php echo RMSettings::render_field( $field ); ?>
                            </div>
                        </div>

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
                <button type="button" class="btn btn-default" onclick="<?php if ( $is_popup ): ?>$('#cu-settings-form').modal('hide');<?php else: ?>window.location.href='<?php echo RMUris::anchor($module->getVar('dirname')); ?>';<?php endif; ?>"><?php _e('Cancel', 'rmcommon'); ?></button>
                <button type="submit" class="btn btn-primary"><?php _e('Save Changes', 'rmcommon'); ?></button>
            </div>
        </div>
    </div>
    <?php echo $xoopsSecurity->getTokenHTML(); ?>
    <input type="hidden" name="action" value="save-settings">
    <input type="hidden" name="mod" value="<?php echo $module->mid(); ?>">
    <?php if ( $is_popup ): ?>
    <input type="hidden" name="via_ajax" value="1">
    <?php endif; ?>
</form>
</div>
