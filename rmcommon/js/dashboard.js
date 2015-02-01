function get_mods_page(o){$("#loading-mods").show(),$.post("index.php",{action:"list",page:o},function(o){$("#ajax-mods-list").html(o),$("#loading-mods").hide()},"html")}
