<div id="rmc-register-overlay"></div>
<div id="rmc-register-form">
    <button class="close">&times;</button>
    <h1><?php echo sprintf(__('Activate %s', 'rmcommon'), '<strong>' . $name . '</strong>'); ?></h1>
    <div class="form-group">
        <label for="register-email"><?php _e('Email:', 'rmcommon'); ?></label>
        <input type="email" class="form-control" name="email" id="register-email" placeholder="<?php _e('email@domain.com', 'rmcommon'); ?>">
    </div>
    <div class="form-group">
        <label for="register-api"><?php _e('Your API key:', 'rmcommon'); ?></label>
        <input type="text" class="form-control" name="api" maxlength="40" id="register-api" placeholder="<?php _e('40 chars length key', 'rmcommon'); ?>">
    </div>
    <div class="form-group">
        <label for="register-key"><?php _e('Your license key:', 'rmcommon'); ?></label>
        <input type="text" class="form-control" name="key" id="register-key" placeholder="<?php _e('Received license key', 'rmcommon'); ?>">
    </div>
    <div class="form-group control">
        <button type="button" class="btn btn-primary btn-block btn-lg"><?php echo sprintf(__('Activate %s', 'rmcommon'), $name); ?></button>
    </div>
    <input type="hidden" id="register-item" value="<?php echo $item; ?>">
    <input type="hidden" id="register-type" value="<?php echo $type; ?>">
</div>