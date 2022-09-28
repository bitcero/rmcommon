<div class="d-flex align-items-center">
    <div class="form-check me-3">
        <input
                class="form-check-input"
                type="radio"
                name="<?php echo $this->get('name'); ?>"
                id="<?php echo $this->get('id'); ?>Yes"
                <?php echo $this->get('attributes'); ?>
                value="1"
                <?php echo in_array($this->get('value'), ['yes', 1]) ? 'checked' : ''; ?>
        >
        <label class="form-check-label" for="<?php echo $this->get('id'); ?>Yes">
          <?php _e('Yes', 'rmcommon'); ?>
        </label>
    </div>
    <div class="form-check">
        <input
                class="form-check-input"
                type="radio"
                name="<?php echo $this->get('name'); ?>"
                id="<?php echo $this->get('id'); ?>No"
                value="0"
                <?php echo $this->get('attributes'); ?>
                <?php echo in_array($this->get('value'), ['no', 0]) ? 'checked' : ''; ?>
        >
        <label class="form-check-label" for="<?php echo $this->get('id'); ?>No">
          <?php _e('No', 'rmcommon'); ?>
        </label>
    </div>
</div>