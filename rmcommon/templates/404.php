<h2 class="text-center"><?php _e('Error 404', 'rmcommon'); ?></h2>

<?php if('' != $message): ?>
    <h3 class="text-center"><?php echo $message; ?></h3>
<?php else: ?>
    <h3 class="text-center"><?php _e('The document that you trying to reach could not be found in server', 'rmcommon'); ?></h3>
<?php endif; ?>