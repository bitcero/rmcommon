<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés https://bitcero.dev
 * -------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

/**
 * This file contains all Javascript language and can be used
 * by including it in HEAD section of HTML code
 *
 * Example:
 * <code>require_once (RMCPATH . '/js/cu-js-language.php';</code>
 *
 * This, will include the next HTML script:
 * <code>
 * var cuLanguage = {};
 * stringIdentifier: 'Language String';
 * stringIdentifier: 'Language String';
 * </code>
 */
global $common;

ob_start();
?>

var cuLanguage = {

<?php if ('groups' == $common->location): ?>

    confirmDelete: '<?php _e('Do you really want to delete selected groups? Please note that this action can not be undo.', 'rmcommon'); ?>',

<?php elseif ('blocks' == $common->location): ?>

    deleteBlockMessage: "<?php _e('Do you really want to delete this block?', 'rmcommon'); ?>",
    deleteBlock: "<?php _e('Delete Block', 'rmcommon'); ?>",
    hideBlock: "<?php _e('Hide Block', 'rmcommon'); ?>",
    showBlock: "<?php _e('Show Block', 'rmcommon'); ?>",
    blockSettings: "<?php _e('Block Settings', 'rmcommon'); ?>",
    save: "<?php _e('Save', 'rmcommon'); ?>",
    cancel: "<?php _e('Cancel', 'rmcommon'); ?>",
    showPositions: "<?php _e('Show Positions', 'rmcommon'); ?>",
    showPositions: "<?php _e('Show Positions', 'rmcommon'); ?>",
    showBlocks: "<?php _e('Show Blocks', 'rmcommon'); ?>",
    errorShowInPosition: "<?php _e('An error occurred while trying to add block to its position!', 'rmcommon'); ?>",
    errorNoPosition: "<?php _e('The position specified for this block does not exists!', 'rmcommon'); ?>",
    modulePages: "<?php _e('%s Pages', 'rmcommon'); ?>",
    confirmPositionDeletion: "<?php _e('Do you really want to delete selected positions?', 'rmcommon'); ?>",
    selectBefore: "<?php _e('You must select at least one position before you can do this action!', 'rmcommon'); ?>",

<?php elseif ('modules' == $common->location): ?>

    visitWeb: "<?php _e('Visit web site', 'rmcommon'); ?>",

<?php elseif ('updates' == $common->location): ?>

    version: '<?php _e('Version:', 'rmcommon'); ?>',
    author: '<?php _e('Author:', 'rmcommon'); ?>',
    published: '<?php _e('Published:', 'rmcommon'); ?>',
    requirements: '<?php _e('Requirements:', 'rmcommon'); ?>',
    xoops: '<?php _e('XOOPS version:', 'rmcommon'); ?>',
    loginRequired: '<?php _e('Login required', 'rmcommon'); ?>',
    loginVerify: '<?php _e('Verifying credentials...', 'rmcommon'); ?>',
    requestFile: '<?php _e('Requesting update package...', 'rmcommon'); ?>',
    waitingResponse: '<?php _e('Waiting server response...', 'rmcommon'); ?>',
    installed: '<?php _e('Update installed successfully!', 'rmcommon'); ?>',

<?php endif; ?>

    downloadNews: "<?php _e('Downloading News...', 'rmcommon'); ?>",
    downloadNewsError: "<?php _e('Error ocurred while trying to load news.', 'rmcommon'); ?>",
    inProgress: "<?php _e('Operation in progress...', 'rmcommon'); ?>",
    searchResults: "<?php _e('Search Results (%u)', 'rmcommon'); ?>",
    modules: "<?php _e('Modules', 'rmcommon'); ?>",
    resizingLegend: "<?php _e('Resizing %1 from %2', 'rmcommon'); ?>",
    noItemRegister: "<?php _e('Provided item is not valid', 'rmcommon'); ?>",
    noTypeRegister: "<?php _e('Unrecognized item type', 'rmcommon'); ?>",
    dataInvalid: "<?php _e('Provided data is not valid!', 'rmcommon'); ?>",
    thereAreErrors: "<?php _e('Errors were found! Please check fields in red.', 'rmcommon'); ?>",
    invalidResponse: "<?php _e('Invalid response from server', 'rmcommon'); ?>",

    <?php /* DROPZONE messages */?>
    dzDefault: "<?php _e('Drop files here to upload', 'rmcommon'); ?>",
};

<?php

$script = ob_get_clean();
RMTemplate::getInstance()->add_inline_script($script);
