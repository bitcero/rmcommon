<div id="json-container">
    <?php echo json_encode($updates['updates']); ?>
</div>
<?php foreach ($updates['updates'] as $i => $upd): ?>
    <div class="upd-item <?php echo $upd['data']['type']; ?><?php echo $upd['data']['api'] ? ' apikey' : ''; ?>" id="upd-<?php echo $i; ?>" data-dir="<?php echo $upd['data']['dir']; ?>">
        <span class="type"><?php echo 'module' == $upd['data']['type'] ? __('Module', 'rmcommon') : ('plugin' == $upd['data']['type'] ? __('Plugin', 'rmcommon') : __('Theme', 'rmcommon')); ?></span>
        <div class="row">
            <div class="col-md-7">
                <h5><?php echo $upd['data']['title']; ?></h5>
                <div class="rmc-upd-info">
                    <?php echo sprintf(__('Type: %s', 'rmcommon'), '<span class="label-type">' . ('module' == $upd['data']['type'] ? __('Module', 'rmcommon') : ('plugin' == $upd['data']['type'] ? __('Plugin', 'rmcommon') : __('Theme', 'rmcommon'))) . '</span>'); ?> |
                    <?php echo sprintf(__('Version: %s', 'rmcommon'), '<strong>' . $upd['data']['version'] . '</strong>'); ?> |
                    <?php echo sprintf(__('Released on: %s', 'rmcommon'), '<strong>' . $upd['data']['released'] . '</strong>'); ?>
                    <?php if ($upd['data']['api']): ?>
                        | <span class="text-danger"><?php echo $cuIcons->getIcon('svg-rmcommon-key', ['class' => 'text-warning']); ?> <?php _e('License required', 'rmcommon'); ?></span>
                    <?php elseif ($upd['data']['login']): ?>
                        | <?php echo $cuIcons->getIcon('svg-rmcommon-lock', ['class' => 'text-warning']); ?> <?php _e('Login required', 'rmcommon'); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-5 update-buttons">
                <div class="btn-group">
                    <button type="button" class="btn btn-default button-details" data-id="<?php echo $i; ?>">
                        <?php echo $common->icons()->getIcon('fa fa-info-circle text-info'); ?> <?php _e('Details', 'rmcommon'); ?></button>
                    <button type="button" class="btn btn-default btn-install" data-id="<?php echo $i; ?>"><?php echo $common->icons()->getIcon('fa fa-arrow-circle-down', ['class' => 'text-warning']); ?> <?php _e('Update now!', 'rmcommon'); ?></button>
                    <button type="button" class="btn btn-default btn-later" data-id="<?php echo $i; ?>" onclick="installLater(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="fa fa-clock-o"></i> <?php _e('Later', 'rmcommon'); ?></button>
                </div>
            </div>
        </div>
        <div class="upd-progress">
            <label><?php _e('Installing update...', 'rmcommon'); ?></label>
            <div class="progress progress-striped active">
                <div class="progress-bar"></div>
            </div>
            <span class="text-info status"><?php echo sprintf(__('Downloading %s', 'rmcommon'), $rmUtil->formatBytesSize($upd['data']['size'])); ?></span>
        </div>
    </div>
    <?php endforeach; ?>

