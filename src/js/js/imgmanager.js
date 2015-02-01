// $Id: imgmanager.js 183 2010-02-02 07:00:52Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$(document).ready(function(){
   $(".new-size-button").click(function(){
       var table = $("#sizes-container-all .single-size:last");
       var id = table.attr("id").replace("single-size-",'');
       var newid = id; newid++;
       var clone = table.clone();
       clone.attr("id", 'single-size-'+newid);
       //var repl = "replace(/sizes\["+id+"\]/g, \"sizes["+newid+"]\");";
       html = eval("clone.html().replace(/sizes\\["+id+"\\]/g, \"sizes["+newid+"]\");");
       html = html.replace('delete-'+id, 'delete-'+newid);
	   html = html.replace(/value="*"/g, 'value=""');
       clone.html(html);
       clone.insertAfter(table);
       clone.effect('highlight',{});
   });
   
});

function delete_size(e){

	var id = $(e).attr("id").substring(7);
       
	var tables = $("#sizes-container-all .single-size");
	if (tables.length<=1) return;
	$("#single-size-"+id).remove();

	return false;
   
}