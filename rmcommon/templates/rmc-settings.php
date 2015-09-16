<h1 class="cu-section-title">
    <?php _e('Configurable Items', 'rmcommon'); ?>
</h1>

<div class="cu-box">
    <div class="box-header">
        <h5 class="box-title">
            <?php _e('Elementos Configurables', 'rmcommon'); ?>
        </h5>
    </div>
    <div class="box-content">
        <p class="help-block">
            <?php _e('Click on any element to see its configuration options.', 'rmcommon'); ?>
        </p>
        <div class="text-center">
            <?php foreach( $modules as $module ): ?>

                <a href="settings.php?mod=<?php echo $module['id']; ?>&amp;action=configure" class="settings-item">
                    <img src="<?php echo $module['logo']; ?>" alt="<?php echo $module['name']; ?>">
                    <span><?php echo $module['name']; ?></span>
                </a>

            <?php endforeach; ?>
        </div>
    </div>
</div>
