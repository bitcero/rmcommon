<?php if( empty( $images ) ): ?>

    <div class="alert alert-info text-center">
        <?php _e('There are not images yet!','rmcommon'); ?>
    </div>

<?php endif; ?>

<?php foreach( $images as $image ): ?>

    <div id="thumbnail-<?php echo $image['id']; ?>" data-id="<?php echo $image['id']; ?>" class="thumbnail-item" style="background-image: url('<?php echo $image['thumb']; ?>');" alt="<?php echo $image['title']; ?>">
        <span class="thumbnail-cover"></span>
        <?php if( $multi ): ?>
        <a href="#" class="add"><?php echo $cuIcons->getIcon('svg-rmcommon-squares'); ?></a>
        <span class="check"><?php echo $cuIcons->getIcon('svg-rmcommon-ok'); ?></span>
        <?php endif; ?>
        <a href="#" class="insert"><?php echo $cuIcons->getIcon('svg-rmcommon-plus'); ?></a>
    </div>

<?php endforeach; ?>

<div id="inserter-blocker"></div>
<div id="image-inserter">
    <div class="row content">

        <div class="image-info">
            <span class="image"></span>

            <div class="media author-info">
                <a href="#" class="pull-left" target="_blank">
                    <img class="author-avatar img-thumbnail">
                </a>
                <div class="media-body">
                    <h5 class="media-heading"><strong>Uploaded by <a href="#" target="_blank"></a></strong></h5>
                    <small>on <span class="info-date"></span></small><br>
                </div>
            </div>
            <ul class="list-unstyled">
                <li>
                    <small>Title: <code><span class="info-title"></span></code></small>
                </li>
                <li>
                    <small>Description: <code><span class="info-description"></span></code></small>
                </li>
                <li>
                    <small>MIME type: <code><span class="info-mime"></span></code></small>
                </li>
                <li>
                    <small>Original: <code><span class="info-original"></span></code></small>
                </li>
                <li>
                    <small>File size: <code><span class="info-size"></span></code></small>
                </li>
                <li>
                    <small>Dimensions: <code><span class="info-dimensions"></span></code></small>
                </li>
            </ul>

        </div>

        <!-- Insert form -->
        <div class="image-form">
            <div class="form-group">
                <label><?php _e('Title:', 'rmcommon'); ?></label>
                <input class="form-control input-sm img-title" type="text" name="title">
            </div>
            <div class="form-group">
                <label><?php _e('Alternative text:','rmcommon'); ?></label>
                <input class="form-control input-sm img-alt" type="text" name="alt">
            </div>
            <div class="form-group">
                <label><?php _e('Description:','rmcommon'); ?></label>
                <textarea class="form-control input-sm img-description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label><?php _e('Link URL:','rmcommon'); ?></label>
                <input class="form-control input-sm img-link" type="text" name="link">
                <div class="btn-group btn-group-xs img-links">

                </div>
            </div>
            <div class="form-group">
                <label><?php _e('Alignment:','rmcommon'); ?></label><br>
                <label class="radio-inline">
                    <input class="img-align" type="radio" name="align" value="" checked="checked" /> <?php _e('None','rmcommon'); ?>
                </label>
                <label class="radio-inline">
                    <input class="img-align" type="radio" name="align" value="left" /> <?php _e('Left','rmcommon'); ?>
                </label>
                <label class="radio-inline">
                    <input class="img-align" type="radio" name="align" value="center" /> <?php _e('Center','rmcommon'); ?>
                </label>
                <label class="radio-inline">
                    <input class="img-align" type="radio" name="align" value="right" /> <?php _e('Right','rmcommon'); ?>
                </label>
            </div>
            <div class="form-group img-sizes">

            </div>
            <?php if( $type == 'markdown' ): ?>
            <div class="form-group" id="md-as">
                <label><?php _e('Insert as:', 'rmcommon'); ?></label><br>
                <label class="radio-inline">
                    <input type="radio" name="insertas" value="md" checked> Markdown
                </label>
                <label class="radio-inline">
                    <input type="radio" name="insertas" value="html"> HTML
                </label>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <input type="hidden" name="id" value="" class="img-id">
                <input type="hidden" name="type" value="<?php echo $type!=''?$type:'tiny'; ?>" id="insert-type">
                <input type="hidden" name="target" value="<?php echo $target; ?>" id="insert-target">
                <input type="hidden" name="container" value="<?php echo $container; ?>" id="insert-container">
                <button type="button" class="btn btn-primary btn-insert"><?php _e('Send Image', 'rmcommon'); ?></button>
                <button type="button" class="btn btn-primary btn-edit"><span class="glyphicon glyphicon-ok"></span> <?php _e('Set Changes', 'rmcommon'); ?></button>
                <button type="button" class="btn btn-success btn-edit-next"><?php _e('Save and Next', 'rmcommon'); ?> <span class="glyphicon glyphicon-chevron-right"></span></button>
                <button type="button" class="btn btn-default btn-close"><?php _e('Close', 'rmcommon'); ?></button>
            </div>
        </div>
        <!-- Insert form /-->
    </div>
</div>

<div id="images-tray">
    <div class="tray-added">
        <div class="images">

        </div>
    </div>
    <div class="tray-commands">
        <button type="button" class="btn btn-primary btn-sm btn-insert"><span class="glyphicon glyphicon-ok"></span> <?php _e('Insert Images', 'rmcommon'); ?></button>
        <button type="button" class="btn btn-warning btn-sm btn-clear"><span class="glyphicon glyphicon-trash"></span> <?php _e('Clear Selected', 'rmcommon'); ?></button>
    </div>
</div>

<input type="hidden" name="token" id="ret-token" value="<?php echo $xoopsSecurity->createToken(); ?>" />
<?php echo $nav->display( false ); ?>
<input type="hidden" id="filesurl" value="<?php echo $filesurl; ?>" />
