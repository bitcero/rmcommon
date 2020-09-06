<div class="panel panel-cyan">
    <div class="panel-heading">
        <h3 class="panel-title"><?php _e('Polyglot Configuration', 'polyglot'); ?></h3>
    </div>

    <?php require 'polyglot-tabs.php'; ?>

    <div class="tab-content">
        <?php require $file; ?>
    </div>
    <div class="panel-footer text-right">
        <?php echo $cuIcons->getIcon('svg-rmcommon-star text-orange'); ?>
        <?php _e('Specify the base language for this site and it can not (and must not) be changed.', 'polyglot'); ?>
    </div>
</div>