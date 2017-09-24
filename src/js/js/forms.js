/**!
 * JavaScript script to manage form fields
 * Author: Eduardo Cortés
 * Author URI: https://www.eduardocortes.mx
 * License: GPL v2
 */

/**
* This file contains all javascript method to manage forms and form fields
*/

var absurl = '';

// Users field
var users_field_name = '';
var usersField = jQuery.extend({

	form_search_users: 	function(id, limit, multisel, baseurl){

		users_field_name = id;
		absurl = baseurl;
		var checks = $("#"+id+"-users-list input");
		var sel = '';
		for(i=0;i<checks.length;i++){
			sel += sel=='' ? $(checks[i]).val() : ','+$(checks[i]).val();
		}

		var title = $("#" + id + "-users-container button").data('title');

		// Add modal
        if($("#"+id+"-dialog-search .modal-body").length <= 0){
		    $("body").append('<div class="modal fade smartb-form-dialog users-form-selector" id="'+id+'-dialog-search">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
            '<h4 class="modal-title">'+title+'</h4></div>' +
            '<div class="modal-body"></div></div></div></div>')
        }

		token = $("#cu-token").val();

		// Update the users container
		var params = {
			type: multisel,
			limit: limit,
			field: id,
			s: sel,
			CUTOKEN_REQUEST: token
		}

		$.get(baseurl+'/modules/rmcommon/ajax/users.php', params, function(data){

		    if(false == cuHandler.retrieveAjax(data)){
		        return false;
            }

            $("#"+id+"-dialog-search .modal-body").html(data.content);
            $("#"+id+"-dialog-search").modal();

		}, 'json');

	},

	add_to_list: function(id){
		var cb = $("#"+users_field_name+"-user-"+id); // Checkbox
		var uc = $("#"+users_field_name+"-username-"+id); //Caption
		var ul = "#"+users_field_name+"-selected-list"; // Selected users list (only name)

		if (cb.is(":checked")){
			// We have to add a new user to list
			if ($(ul+" li.user_"+id).length > 0) return;

			$(ul).append("<li class='user_"+id+"'><label><input type='checkbox' value='"+id+"' checked='checked' onchange=\"usersField.remove_from_list("+id+")\" /> <span id='user-"+users_field_name+"-caption-"+id+"'>"+uc.text()+"</span></label></li>");
			$(ul+" li.user_"+id).effect('highlight',{},'1000');

		} else {
			// Delete a user from list
			if ($(ul+" li.user_"+id).length <= 0) return;

			$(ul+" li.user_"+id).remove();

		}

		usersField.change_title(users_field_name);

	},

	remove_from_list: function(id){
		var ul = "#"+users_field_name+"-selected-list"; // Selected users list (only name)
		var cb = "#"+users_field_name+"-user-"+id;

		if ($(ul+" li.user_"+id).length <= 0) return;
		$(ul+" li.user_"+id).remove();

		usersField.change_title(users_field_name);

		if ($(cb).length<=0) return;

		$(cb).removeAttr("checked");

	},

	change_title: function(){
		var span = $("#"+users_field_name+"-selected-title span");
		var ul = "#"+users_field_name+"-selected-list";
		span.text($(ul+" li").length);
	},

	submit_search: function(multisel){
		var ul = "#"+users_field_name+"-selected-list";
		var limit = $("#"+users_field_name+"-limit").val();
		limit = limit<=0?30:limit;
		var keyword = $("#"+users_field_name+"-kw").val();
		keyword = keyword==undefined ? '' : keyword;
		var order = $("#"+users_field_name+"-ord").val();

		sel = usersField.get_selected(users_field_name);

		usersField.show_waiting(1, users_field_name);
		// Update the users container
		var params = {
			type: multisel,
			limit: limit,
			field: users_field_name,
			kw: keyword,
			ord: order,
			s: sel,
            CUTOKEN_REQUEST: $("#cu-token").val()
		}

		usersField.show_waiting(0, users_field_name);

		$.get(xoUrl+'/modules/rmcommon/ajax/users.php', params, function(data){

		    if(false == cuHandler.retrieveAjax(data)){
		        return false;
            }

			$("#"+users_field_name+"-dialog-search .modal-body").html(data.content);
		},'json');

	},

	show_waiting: function(show){
		if (show)
			$("#"+users_field_name+"-dialog-search .modal-body").html('<img src="'+absurl+'/modules/rmcommon/images/wait.gif" width="16" height="16" alt="" class="form_user_waiting_img" />');
		else
			$("#"+users_field_name+"-dialog-search .modal-body").html('');
	},

	get_selected: function(users_field_name){
		// Get all selected users
		var checks = $("#"+users_field_name+"-selected-list input");
		var sel = '';

		for(i=0;i<checks.length;i++){
			sel += sel==''?$(checks[i]).val():','+$(checks[i]).val();
		}

		return sel;
	},

	goto_page: function(page, multisel){
		var sel = usersField.get_selected();
		var limit = $("#"+users_field_name+"-limit").val();
		limit = limit<=0?30:limit;
		var keyword = $("#"+users_field_name+"-kw").val();
		keyword = keyword==undefined ? '' : keyword;
		var order = $("#"+users_field_name+"-ord").val();
		var gpage = page<=0 ? 1 : page;
		usersField.show_waiting(1);

		var params = {
			type: multisel,
			limit: limit,
			field: users_field_name,
			kw: keyword,
			ord: order,
			s: sel,
			pag: gpage,
            XOOPS_TOKEN_REQUEST: $("#XOOPS_TOKEN_REQUEST").val()
		}

		$.get(absurl+'/modules/rmcommon/ajax/users.php', params, function(data){
			usersField.show_waiting(0);
			$("#"+users_field_name+"-dialog-search .modal-body").html(data);
		});

	},

	insert_users: function(multi, uid){

		var input = multi ? 'checkbox' : 'radio';
		var name = multi ? users_field_name+'[]' : users_field_name;

		if (multi){

			var checks = $("#"+users_field_name+"-selected-list input");
			var con = "#"+users_field_name+"-exmuser-";

			for(i=0;i<checks.length;i++){

				id = $(checks[i]).val();

				if ($(con+id).length>0) continue;

				li = "<li id='"+users_field_name+"-exmuser-"+id+"'>";
				li += "<label>";
                li += "<a href='#' onclick='usersField.remove("+id+");'>&times;</a>";
                li += "<input type='"+input+"' value='"+id+"' name='"+name+"' id='"+users_field_name+"-"+id+"' checked='checked' />";
				li += " "+$("#user-"+users_field_name+"-caption-"+id).text()+"</label></li>";
				$("#"+users_field_name+"-users-list").append(li);

			}

		} else {

			var ele = "#"+users_field_name+"-user-"+uid;
			$("#"+users_field_name+"-users-list li").remove();
			if ($(ele).length>0){
				li = "<li id='"+users_field_name+"-exmuser-"+uid+"'>";
				li += "<label>";
                li += "<a href='javascript:;' onclick='usersField.remove("+uid+");'>&times;</a>";
                li += "<input type='"+input+"' value='"+uid+"' name='"+name+"' id='"+users_field_name+"-"+uid+"' checked='checked' />";
				li += " "+$("#"+users_field_name+"-username-"+uid).text()+"</label></li>";
				$("#"+users_field_name+"-users-list").append(li);
			}

		}

		$("#"+users_field_name+"-dialog-search").modal('toggle');

	},

	remove: function(id){
		$("#"+users_field_name+"-exmuser-"+id).remove();
        return false;
	}

});

$(document).ready(function(){
    $("form").validate();

    /**
     * Modules field control
     */
    $("body").on('change', ".modules-field :checkbox", function(){
        var $field = $(this).parents(".modules-field"); // Main container

        var parent = $(this).data('parent'); // Undefined if it is not a subpage
        var module = $(this).data('module'); // Undefined if it is not a module or a check all option
        var all = $(this).data('all'); // Undefined if it is a module or a subpage

        /**
         * This is a subpage, then we need to verify if all subpages
         * has been checked or not
         */
        if(undefined != parent){
            var checked = $($field).find("[data-parent='" + parent + "']:checked").length;

            if(checked > 0){
                $($field).find("[data-module='" + parent + "']").prop('checked', true);
            } else {
                $($field).find("[data-module='" + parent + "']").prop('checked', false);
            }

            /**
             * Verify if all subpages has been checked.
             * If not, "check all" option must not be checked
             */
            var totalPages = $($field).find("[data-parent]:checkbox").length;
            var checkPages = $($field).find("[data-parent]:checked").length;
            if(checkPages < totalPages){
                $($field).find("[data-all]").prop('checked', false);
            } else {
                $($field).find("[data-all]").prop('checked', true);
            }

            return;
        }

        /**
         * This is a module checkbox, then we need to check or uncheck
         * all its subpages
         */
        if(undefined != module){
            // Switch all subpages
            $($field).find("[data-parent='" + module + "']").prop('checked', $(this).prop('checked'));

            // Switch check all option
            var total = $($field).find("[data-module]:checkbox").length;
            var checked = $($field).find("[data-module]:checked").length;

            if(checked < total){
                $($field).find("[data-all]").prop('checked', false);
            } else {

                /**
                 * Verify if all subpages has been checked.
                 * If not, "check all" option must not be checked
                 */
                var totalPages = $($field).find("[data-parent]:checkbox").length;
                var checkPages = $($field).find("[data-parent]:checked").length;
                if(checkPages < totalPages){
                    $($field).find("[data-all]").prop('checked', false);
                } else {
                    $($field).find("[data-all]").prop('checked', true);
                }

            }

            return;
        }

        /**
         * It is "check all" option
         */
        if(undefined != all){
            $($field).find("[data-module]:checkbox").prop('checked', $(this).prop('checked'));
            $($field).find("[data-parent]:checkbox").prop('checked', $(this).prop('checked'));
        }

        return;

    });

});
