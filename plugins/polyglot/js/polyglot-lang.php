<?php
/**
 * Polyglot for Common Utilities
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
 * @package      polyglot
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

$polyglot = \Common\Core\Helpers\Plugins::getInstance()->load('polyglot');

ob_start();
?>

(function(){

    /**
    * Polyglot Language
    * @constructor
    */
    this.PolyglotLang = function(){
        this.current = '<?php echo $common->settings->lang; ?>';
        this.base = '<?php echo $polyglot->baseLanguage(); ?>';
    }

    PolyglotLang.prototype = {
        noFieldFilled: '<?php _e('You must complete the field %s before to continue!', 'polyglot'); ?>',
        langExists: '<?php _e('Language %s already exists!', 'polyglot'); ?>',
        noFoundLang: '<?php _e('No language data detected!', 'polyglot'); ?>',
        editionLang: '<?php _e('Edition Language:', 'polyglot'); ?>',
        noBaseEdition: '<?php _e('You are editing in a differnet language. Changes will be saved using the current one (%s), but if you need to edit in base language please click on the right button before to make any change.', 'polyglot'); ?>',
        confirmBase: '<?php _e('Do you really want to set this language as "Base Language"? Note that this action can not be undone after.','polyglot'); ?>',
        saveChanges: '<?php _e('Save Changes','polyglot'); ?>',
        confirmDelete: '<?php _e('Do you really wish to delete this language? \n\nNote that this action can not be undone and all translated strings from this language will be deleted too.','polyglot'); ?>',
        confirmDisabling: '<?php _e('Do you really wish to change the status for this language?','polyglot'); ?>',
    };

}());

<?php

$language = ob_get_clean();

unset($polyglot);

$common->template()->add_inline_script($language, 0);
