<div id="rmc-reactivate-form">
    <button class="close">&times;</button>
    <h1><?php echo sprintf(__('Reactivate %s', 'rmcommon'), '<strong>' . $name . '</strong>'); ?></h1>
    <div class="form-group">
        <label for="reactivate-key"><?php _e('Your activation key:', 'rmcommon'); ?></label>
        <input type="text" class="form-control" name="key" id="reactivate-key">
    </div>
    <div class="form-group control text-center">
        <button type="button" class="btn btn-primary btn-lg btn-block"><?php _e('Reactivate Now!', 'rmcommon'); ?></button>
    </div>
    <input type="hidden" id="reactivate-item" value="<?php echo $item; ?>">
    <input type="hidden" id="reactivate-type" value="<?php echo $type; ?>">
    <input type="hidden" id="reactivate-email" value="<?php echo $email; ?>">
    <input type="hidden" id="reactivate-api" value="<?php echo $api; ?>">
    <input type="hidden" id="reactivate-license" value="<?php echo $license; ?>">
    <div class="help-block">
        <?php echo sprintf(
            __('If you have errors with the activation of your %s copy, and you have a valid activation key, please input here and the reactivation request will be sent inmediatly.', 'rmcommon'),
            '<strong>' . $name . '</strong>'); ?>
    </div>
</div>