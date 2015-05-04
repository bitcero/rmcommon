<?php
// $Id$
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * This file contains all Javascript language and can be used
 * by including it in HEAD section of HTML code
 *
 * Example:
 * <code>include_once (RMCPATH . '/js/cu-js-language.php';</code>
 *
 * This, will include the next HTML script:
 * <code>
 * var cuLanguage = {};
 * stringIdentifier: 'Language String';
 * stringIdentifier: 'Language String';
 * </code>
 */

if ( !defined('RMCLOCATION') )
    define('RMLOCATION', '');

ob_start();
?>

var cuLanguage = {

<?php if ( RMCLOCATION == 'groups' ): ?>

    confirmDelete: '<?php _e('Do you really want to delete selected groups?\n\nPlease note that this action can not be undo.', 'rmcommon' ); ?>',

<?php elseif ( RMCLOCATION == 'blocks' ): ?>

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

<?php elseif ( RMCLOCATION == 'modules' ): ?>

    visitWeb: "<?php _e('Visit web site', 'rmcommon'); ?>",

<?php endif; ?>

    downloadNews: "<?php _e('Downloading News...', 'rmcommon' ); ?>",
    downloadNewsError: "<?php _e('Error ocurred while trying to load news.', 'rmcommon' ); ?>",

};

<?php

$script = ob_get_clean();
RMTemplate::get()->add_head_script( $script );
