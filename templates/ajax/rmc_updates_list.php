<?php foreach($updates['updates'] as $i => $upd): ?>
    <div class="row-fluid upd-item" id="upd-<?php echo $i; ?>">
        <div>
            <div class="span9">
                <h4><?php echo $upd['data']['title']; ?></h4>
                <div class="rmc-upd-info">
                    <?php echo sprintf(__('Type: %s','rmcommon'), '<span class="label label-inverse">'.$upd['data']['type'].'</span>'); ?> |
                    <?php echo sprintf(__('Version: %s','rmcommon'), '<strong>'.$upd['data']['version'].'</strong>'); ?> |
                    <?php echo sprintf(__('Released on: %s','rmcommon'), '<strong>'.$upd['data']['released'].'</strong>'); ?>
                    <?php if($upd['data']['login']): ?>
                    | <i class="icon-lock"></i> <?php _e('Login required','rmcommon'); ?>
                    <?php endif; ?>
                </div>
                <div class="upd-progress">
                    <label><?php _e('Installing update...','rmcommon'); ?></label>
                    <div class="progress progress-striped active">
                        <div class="bar"></div>
                    </div>
                    <span class="text-info status"><?php echo sprintf(__('Downloading %s','rmcommon'), $rmUtil->formatBytesSize($upd['data']['size'])); ?></span>
                </div>
            </div>
            <div class="span3">
                <div class="btn-group pull-right">
                    <button type="button" class="btn" onclick="loadUpdateDetails(<?php echo $i; ?>);"><i class="icon-info-sign"></i> <?php _e('View details','rmcommon'); ?></button>
                    <button type="button" class="btn" onclick="installUpdate(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="icon-circle-arrow-down"></i> <?php _e('Update now!','rmcommon'); ?></button>
                    <button type="button" class="btn button-later" onclick="installLater(<?php echo $i; ?>);" data-id="<?php echo $i; ?>"><i class="icon-time"></i> <?php _e('Install Later','rmcommon'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

<div id="json-container">
    <?php echo json_encode($updates['updates']); ?>
</div>