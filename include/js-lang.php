<?php
// $Id: blocks.php 1037 2012-09-07 21:19:12Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

ob_start();
?>

var rmJsLang = {

    deleteBlockMessage: "<?php _e('Do you really want to delete this block?','rmcommon'); ?>",
    deleteBlock: "<?php _e('Delete Block','rmcommon'); ?>",
    hideBlock: "<?php _e('Hide Block','rmcommon'); ?>",
    showBlock: "<?php _e('Show Block','rmcommon'); ?>",
    blockSettings: "<?php _e('Block Settings','rmcommon'); ?>",
    save: "<?php _e('Save','rmcommon'); ?>",
    cancel: "<?php _e('Cancel','rmcommon'); ?>",
    showPositions: "<?php _e('Show Positions','rmcommon'); ?>",
    showPositions: "<?php _e('Show Positions','rmcommon'); ?>",
    showBlocks: "<?php _e('Show Blocks','rmcommon'); ?>",
    errorShowInPosition: "<?php _e('An error occurred while trying to add block to its position!','rmcommon'); ?>",
    errorNoPosition: "<?php _e('The position specified for this block does not exists!','rmcommon'); ?>",
    modulePages: "<?php _e('%s Pages','rmcommon'); ?>",
    confirmPositionDeletion: "<?php _e('Do you really want to delete selected positions?','rmcommon'); ?>",
    selectBefore: "<?php _e('You must select at least one position before you can do this action!','rmcommon'); ?>",

};

<?php

return ob_get_clean();