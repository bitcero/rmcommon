<?php ob_start(); ?>

var advLang = {
    noIcon: '<?php _e('No icon has been detected!', 'advform-pro'); ?>',
    noParent: '<?php _e('Target field has not specified!', 'advform-pro'); ?>',
    noRepeatItems: '<?php _e('Repeater field does not have any field!', 'advform-pro'); ?>',
    delete: '<?php _e('Delete', 'advform-pro'); ?>',
    open: '<?php _e('Open', 'advform-pro'); ?>',
    item: '<?php _e('Item %u', 'advform-pro'); ?>',
    confirmDeletion: '<?php _e('Do your really want to delete this item?', 'advform-pro'); ?>',
};

<?php
$lang = ob_get_clean();

RMTemplate::getInstance()->add_inline_script($lang, 1);
