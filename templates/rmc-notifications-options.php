<hr>
<div class="cu-notifications panel panel-default" id="notification-<?php echo $this::$index; ?>">
    <div class="panel-heading">
        <?php _e('Notifications', 'rmcommon'); ?>
    </div>
    <form name="frmnotifications" id="notification-form-<?php echo $this::$index; ?>" method="post" action="<?php echo RMUris::relative_url(RMUris::current_url()); ?>">
        <div class="panel-body">
            <?php foreach ($items as $item): ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               value="<?php echo $item['params']; ?>"
                               name="notifications[<?php echo $item['event']; ?>]"
                               data-event="<?php echo $item['hash']; ?>"
                               class="notification-item"
                               <?php echo $item['subscribed'] ? 'checked' : ''; ?>>
                        <?php echo $item['caption']; ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>