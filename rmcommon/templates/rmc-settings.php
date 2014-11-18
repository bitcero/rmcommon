<h1 class="cu-section-title">
    <?php _e('Configurable Items', 'rmcommon'); ?>
</h1>

<div class="text-center">
<?php foreach( $modules as $module ): ?>

    <a href="settings.php?mod=<?php echo $module['id']; ?>&amp;action=configure" class="settings-item">
        <img src="<?php echo $module['logo']; ?>" alt="<?php echo $module['name']; ?>">
        <span><?php echo $module['name']; ?></span>
    </a>

<?php endforeach; ?>
</div>
