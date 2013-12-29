<?php foreach($updates['updates'] as $i => $upd): ?>
    <div class="row upd-item" id="upd-<?php echo $i; ?>">
        <div>
            <div class="col-md-8 col-lg-8">
                <h4><?php echo $upd['data']['title']; ?></h4>
                <div class="rmc-upd-info">
                    <?php echo sprintf(__('Type: %s','rmcommon'), '<span class="label label-info">'.$upd['data']['type'].'</span>'); ?> |
                    <?php echo sprintf(__('Version: %s','rmcommon'), '<strong>'.$upd['data']['version'].'</strong>'); ?> |
                    <?php echo sprintf(__('Released on: %s','rmcommon'), '<strong>'.$upd['data']['released'].'</strong>'); ?>
                    <?php if($upd['data']['login']): ?>
                    | <i class="fa-lock"></i> <?php _e('Login required','rmcommon'); ?>
                    <?php endif; ?>
                </div>
                <div class="upd-progress">
                    <label><?php _e('Installing update...','rmcommon'); ?></label>
                    <div class="progress progress-striped active">
                        <div class="progress-bar"></div>
                    </div>
                    <span class="text-info status"><?php echo sprintf(__('Downloading %s','rmcommon'), $rmUtil->formatBytesSize($upd['data']['size'])); ?></span>
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-default" onclick="loadUpdateDetails(<?php echo $i; ?>);"><i class="fa-info-sign"></i> <?php _e('View details','rmcommon'); ?></button>
                    <button type="button" class="btn btn-default" onclick="installUpdate(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="fa-circle-arrow-down"></i> <?php _e('Update now!','rmcommon'); ?></button>
                    <button type="button" class="btn button-default btn-warning" onclick="installLater(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="fa-time"></i> <?php _e('Install Later','rmcommon'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

<div id="json-container">
    <?php echo json_encode($updates['updates']); ?>
</div>