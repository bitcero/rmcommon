<div class="d-flex align-items-center">
    <div class="form-check me-3">
        <input
                class="form-check-input"
                type="radio"
                name="<?php echo $name; ?>"
                id="<?php echo $id; ?>Yes"
                <?php echo $attributes; ?>
                value="yes"
                <?php echo in_array($value, ['yes', 1]) ? 'checked' : ''; ?>
        >
        <label class="form-check-label" for="<?php echo $id; ?>Yes">
          <?php _e('Yes', 'rmcommon'); ?>
        </label>
    </div>
    <div class="form-check">
        <input
                class="form-check-input"
                type="radio"
                name="<?php echo $name; ?>"
                id="<?php echo $id; ?>No"
                <?php echo $attributes; ?>
                <?php echo in_array($value, ['no', 0]) ? 'checked' : ''; ?>
        >
        <label class="form-check-label" for="<?php echo $id; ?>No">
          <?php _e('No', 'rmcommon'); ?>
        </label>
    </div>
</div>