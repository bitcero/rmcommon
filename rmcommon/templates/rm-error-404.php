<?php
$GLOBALS['rmTpl']->header();
?>

<div style="text-align: center">
    <h1><?php _e('ERROR 404. Document not Found!', 'rmcommon'); ?></h1>
    <p><strong><?php echo sprintf(__('Request document %s could not be found in this server!', 'rmcommon'), $controller . '/' . $action); ?></strong></p>
</div>

<?php
$GLOBALS['rmTpl']->footer();
?>
