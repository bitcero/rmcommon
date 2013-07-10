/**
* $id$
*/

var delmes = '';

function confirm_delete(id){
	
	var con = confirm(delmes);
	
	if(!con) return false;

	$("#com-"+id).attr('checked','checked');
	$("#action-select").val('delete');
	$("#list-comments").submit();
	
}

function approve_action(id, w){
	
	if(w!='approve' && w!='unapprove' && w!='spam') return;

	$("#com-"+id).attr('checked','checked');
	$("#action-select").val(w);
	$("#list-comments").submit();
	
}