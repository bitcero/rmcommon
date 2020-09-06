<?php ob_start(); ?>
var advFormLang = {
    edit: '<?php _e('Edit','advform-pro'); ?>',
    delete: '<?php _e('Delete','advform-pro'); ?>',
    close: '<?php _e('Close','advform-pro'); ?>',
    insertImage: '<?php _e('Insert Image','advform-pro'); ?>',
    uid: '<?php _e('<strong>ID:</strong> %uid', 'advform-pro'); ?>',
    email: '<?php _e('<strong>Email:</strong> %email', 'advform-pro'); ?>',
};
var imgmgr_title = '<?php _e('Image Manager','advform-pro'); ?>';
var mgrURL = '<?php echo RMCURL.'/include/tiny-images.php'; ?>';
<?php
    $lang = ob_get_clean();
    return $lang;
    