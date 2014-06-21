tinyMCEPopup.requireLangPack('exmsystem');

var descurl = '';
var xifile = '';
var xititulo = '';
var xidesc = 0;
var xiid = 0;
var xitype = '';

function init(url) {
	descurl = url;
}

function selectThumb(thumb, titulo, desc, id){
	xifile = thumb;
	xititulo = titulo;
	xidesc = desc;
	xiid = id;
	xitype = 'thumb';
	
	form = document.forms[0];
	// Titulo
	span = document.getElementById('imgtitle');
	span.innerHTML = '<img src="'+thumb+'" />';
	
	form.titulo.value = titulo;
	form.desc.value = titulo;
	
	mcTabs.displayTab('props_tab','xiProps');
}

function selectWithLinks(thumb, titulo, desc, id){
	xifile = thumb;
	xititulo = titulo;
	xidesc = desc;
	xiid = id;
	xitype = 'links';
	
	form = document.forms[0];
	// Titulo
	span = document.getElementById('imgtitle');
	span.innerHTML = '<img src="'+thumb+'" />';
	
	form.titulo.value = titulo;
	form.desc.value = titulo;
	
	mcTabs.displayTab('props_tab','xiProps');
}

function selectNormal(url, titulo, desc, id){
	xifile = url;
	xititulo = titulo;
	xidesc = desc;
	xiid = id;
	xitype = 'normal';
	
	form = document.forms[0];
	// Titulo
	span = document.getElementById('imgtitle');
	span.innerHTML = '<img src="'+url+'" width="150" />';
	
	form.titulo.value = titulo;
	form.desc.value = titulo;
	
	mcTabs.displayTab('props_tab','xiProps');
}

function insertImage() {
	var html = '<img src="';
	var width = '';
	var height = '';
	var clname = '';
	var titulo = '';
	var desc = '';
	
	form = document.forms[0];
	html += xifile + '" ';
	clname = form.imgclass.value;
	width = form.imgwidth.value;
	if (width!=''){
		html += 'width="' + width + '" ';
	}
	
	height = form.imgheight.value;
	if (height!=''){
		html += 'height="' + height + '" ';
	}
	
	if (form.titulo.value!='') titulo = form.titulo.value;
	desc = titulo;
	if (form.desc.value!='') desc = form.desc.value;
	
	if (desc!='') html += 'alt="' + desc + '" ';
	if (titulo!='') html += 'title="' + titulo + '" ';
	
	if (xidesc==1){ 
		html += ' longdesc="' + descurl + '?id=' + xiid + '" '; 
	}
	
	if (clname!=''){
		html += ' class="' + clname + '" ';
	}
	
	if (form.imgstyle.value!=''){
		html += 'style="' + form.imgstyle.value + '" ';
	}
	
	cl = form.imgalign;
	if (cl.options[cl.selectedIndex].value!=''){
		html += 'align="' + cl.options[cl.selectedIndex].value + '" ';
	}
    
    elem = form.imgborder.value;
    if (elem!='') html += ' border="'+elem+'"';
    elem = form.vspace.value;
    if (elem!='') html += ' vspace="'+elem+'"';
    elem = form.hspace.value;
    if (elem!='') html += ' hspace="'+elem+'"';
	
	html += '/>';
	
	tinyMCEPopup.execCommand("mceInsertContent", true, html);
	mcTabs.displayTab('general_tab','xiPanel');
}

function insertImageAndLinks() {
	var html = '<a href="';
	var width = '';
	var height = '';
	var clname = '';
	var titulo = '';
	var desc = '';
	
	var file = xifile.replace('type=t', 'type=n');
	file = file.replace('/ths/','/');
	
    elem = form.showas.value;
    if (elem==0){
	    html += xipage+'?id='+xiid+'"';
    } else {
        html += file + '"';
    }
    
    elem = form.itarget.options[form.itarget.selectedIndex].value;
    html += ' target="'+elem+'">';
	html += '<img src="';
	html += xifile + '" ';
    
	if (form.imgclass.value!=''){
		clname = clname!='' ? clname + ' ' + form.imgclass.value : form.imgclass.value;		
	}
	if (clname!=''){
		html += 'class="' + clname + '" ';
	}
	
	width = form.imgwidth.value;
	if (width!=''){
		html += 'width="' + width + '" ';
	}
	
	height = form.imgheight.value;
	if (height!=''){
		html += 'height="' + height + '" ';
	}
	
	if (form.titulo.value!='') titulo = form.titulo.value;
	desc = titulo;
	if (form.desc.value!='') desc = form.desc.value;
	
	if (desc!='') html += 'alt="' + desc + '" ';
	if (titulo!='') html += 'title="' + titulo + '" ';
	
	if (xidesc==1){ 
		html += ' longdesc="' + descurl + '?id=' + xiid + '" '; 
	}
	
	if (form.imgstyle.value!=''){
		html += 'style="' + form.imgstyle.value + '" ';
	}
	
	cl = form.imgalign;
	if (cl.options[cl.selectedIndex].value!=''){
		html += 'align="' + cl.options[cl.selectedIndex].value + '" ';
	}
    
    elem = form.imgborder.value;
    if (elem!='') html += ' border="'+elem+'"';
    elem = form.vspace.value;
    if (elem!='') html += ' vspace="'+elem+'"';
    elem = form.hspace.value;
    if (elem!='') html += ' hspace="'+elem+'"';
	
	html += ' /></a>';
	
	tinyMCEPopup.execCommand("mceInsertContent", true, html);
	mcTabs.displayTab('general_tab','xiPanel');
    window.close();
}

function doInsert(){
	if (xifile==''){
		alert(tinyMCE.getLang('lang_no_selected'));
		return false;
	}
	if (xitype=='thumb' || xitype=='normal'){
		insertImage();
	} else {
		insertImageAndLinks();
	}
	
	xifile = '';
	xititulo = '';
	xidesc = 0;
	xiid = 0;
	xitype = '';
	
}

function xiPreviewImage(img, titulo){
	var ele = document.getElementById('xiPreview');
	if (!ele) return false;
	ele.innerHTML = '<img src="'+img+'" alt="" /><div>'+titulo+'</div>';
	
	var ele = document.getElementById('xiTableBigs');
	if (!ele) return false;
	
	ele.style.width = '340px;';
}

function xiHidePreview(){
	var ele = document.getElementById('xiPreview');
	if (!ele) return false;
	ele.innerHTML = '';
	
	var ele = document.getElementById('xiTableBigs');
	if (!ele) return false;
	
	ele.style.width = '100%;';
}
