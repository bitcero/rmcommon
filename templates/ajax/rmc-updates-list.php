<?php foreach($updates['updates'] as $i => $upd): ?>
    <div class="row upd-item" id="upd-<?php echo $i; ?>">
        <div>
            <div class="col-md-7">
                <h4><?php echo $upd['data']['title']; ?></h4>
                <div class="rmc-upd-info">
                    <?php echo sprintf(__('Type: %s','rmcommon'), '<span class="label label-info">'.$upd['data']['type'].'</span>'); ?> |
                    <?php echo sprintf(__('Version: %s','rmcommon'), '<strong>'.$upd['data']['version'].'</strong>'); ?> |
                    <?php echo sprintf(__('Released on: %s','rmcommon'), '<span class="label label-green">'.$upd['data']['released'].'</span>'); ?>
                    <?php if($upd['data']['login']): ?>
                    | <?php echo $cuIcons->getIcon('svg-rmcommon-lock', ['class' => 'text-warning']); ?> <?php _e('Login required','rmcommon'); ?>
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
            <div class="col-md-5">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-info" onclick="loadUpdateDetails(<?php echo $i; ?>);"><i class="fa fa-info-circle"></i> <?php _e('Details','rmcommon'); ?></button>
                    <button type="button" class="btn btn-warning" onclick="installUpdate(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="fa fa-arrow-circle-down"></i> <?php _e('Update now!','rmcommon'); ?></button>
                    <button type="button" class="btn btn-default" onclick="installLater(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="fa fa-clock-o"></i> <?php _e('Later','rmcommon'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

<div id="json-container">
    <?php echo json_encode($updates['updates']); ?>
</div>
