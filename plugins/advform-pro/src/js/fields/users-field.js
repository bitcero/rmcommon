/*!
 * AdvForm Pro for Common Utilities
 *
 * Copyright © 2015 - 2017 Eduardo Cortés http://www.eduardocortes.mx
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
 * @copyright    Eduardo Cortés (http://www.eduardocortes.mx)
 * @license      GNU GPL 2
 * @package      advform
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.eduardocortes.mx
 */

var usersField;

(function($){

    this.UsersField = function(){

        this.formatData = function(data){

            if(data.loading) return data.text;

            var markup = '<div class="adv-users-field-result clearfix">' +
                '<img src="%avatar">' +
                '<div class="user-meta">' +
                '<span class="user-name">%uname</span>' +
                '<span class="user-uid">%uid</span>' +
                '<span class="user-email">%email</span>' +
                '</div></div>';

            var name = '' != data.name ? data.name + ' (' + data.uname + ')' : data.uname;

            markup = markup.replace('%avatar', data.avatar)
                .replace('%uname', name)
                .replace('%uid', advFormLang.uid.replace('%uid', data.id))
                .replace('%email', advFormLang.email.replace('%email', data.email));

            return markup;

        };

        this.formatSelection = function(data){
            return data.text;
        };

    };

    this.UsersField.prototype.init = function(id){

        var props = {};

        props.ajax = {
            url: xoUrl + '/modules/rmcommon/plugins/advform-pro/includes/users.php',
            dataType: 'json',
            delay: 300,
            data: function(params){
                return {
                    search: params.term,
                    page: params.page,
                    CUTOKEN_REQUEST: $("#cu-token").val()
                }
            },
            processResults: function(data, params){
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            }

        };

        props.templateResult = this.formatData;
        props.escapeMarkup = function(markup){return markup;};
        props.templateSelection = this.formatSelection;

        id = undefined == id ? "select[data-advf-field='users-field']" : id;

        $(id).each(function(){

            var placeholder = $(this).attr('placeholder');

            // Set properties
            if(undefined != placeholder && '' != placeholder){
                props.placeholder = placeholder;
            }

            $(this).select2(props);

        });

    };

    usersField = new UsersField();
    usersField.init();

})(jQuery);
