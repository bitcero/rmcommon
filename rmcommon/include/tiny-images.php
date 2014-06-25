<?php
// $Id: tiny-images.php 999 2012-07-02 03:53:17Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../../mainfile.php';
XoopsLogger::getInstance()->activated = false;
XoopsLogger::getInstance()->renderingEnabled = false;

function send_message($message){
    global $xoopsSecurity;
    
    echo $message;
    echo '<input type="hidden" id="ret-token" value="'.$xoopsSecurity->createToken().'" />';
    die();
    
}

$category = rmc_server_var($_POST,'category', 0);
$action = rmc_server_var($_POST,'action', '');
$cat = new RMImageCategory($category);
$type = rmc_server_var($_REQUEST, 'type', 'tiny');
$en = rmc_server_var($_REQUEST, 'name', '');

// Check if target is different from editor
$target = rmc_server_var($_REQUEST, 'target', '');
$container = rmc_server_var($_REQUEST, 'idcontainer', '');

if ($action==''){
	
	RMTemplate::get()->add_script('jquery.min.js', 'rmcommon', array('directory' => 'include'));
    RMTemplate::get()->add_script('jquery-ui.min.js', 'rmcommon', array('directory' => 'include'));
    RMTemplate::get()->add_script('images_editor.js', 'rmcommon', array('directory' => 'include'));
	
    if (!$cat->isNew()){
        $uploader = new RMFlashUploader('files-container', 'upload.php');
        $uploader->add_setting('scriptData', array(
            'action'=>'upload',
            'category'=>$cat->id(),
            'rmsecurity'=>TextCleaner::getInstance()->encrypt($xoopsUser->uid().'|'.RMCURL.'/images.php'.'|'.$xoopsSecurity->createToken(), true)
        ));
        $uploader->add_setting('multi', true);
        $uploader->add_setting('fileExt', '*.jpg;*.png;*.gif');
        $uploader->add_setting('fileDesc', __('All Images (*.jpg, *.png, *.gif)','rmcommon'));
        $uploader->add_setting('sizeLimit', $cat->getVar('filesize') * $cat->getVar('sizeunit'));
        $uploader->add_setting('buttonText', __('Browse Images...','rmcommon'));
        $uploader->add_setting('queueSizeLimit', 100);
        $uploader->add_setting('auto', true);
        $uploader->add_setting('onSelect', "function(file){
        	if (queuefiles[file]) return false;
        	queuefiles[file] = true;
        	\$('#upload-errors').html('');
        	return true;
        }");
        $uploader->add_setting('onUploadSuccess',"function(file, resp, data){
            eval('ret = '+resp);
            if (ret.error){
                \$('#upload-errors').append('<span class=\"failed\"><strong>'+file.name+'</strong>: '+ret.message+'</span>');
            } else {
                total++;
                ids[total-1] = ret.id;
                \$('#upload-errors').append('<span class=\"done\"><strong>'+file.name+'</strong>: ".__('Uploaded successfully!','rmcommon')."</span>');
            }
            return true;
        }");
        $uploader->add_setting('onQueueComplete', "function(event, data){
            if(total<=0) return;
                \$('.categories_selector').hide('slow');
                \$('#upload-errors').hide('slow');
                \$('#upload-errors').html('');
                \$('#upload-controls').hide('slow');
                \$('#resizer-bar').show('slow');
                \$('#resizer-bar').effect('highlight',{},1000);
                \$('#gen-thumbnails').show();
                    
                var increments = 1/total*100;
                url = '".RMCURL."/images.php".($target!=''?"?taget=$target&amp;id=$container":'')."';
                    
                params = '".TextCleaner::getInstance()->encrypt($xoopsUser->uid().'|'.RMCURL.'/images.php'.'|'.$xoopsSecurity->createToken(), true)."';
                resize_image(params);
                    
        }");
        
        RMTemplate::get()->add_head($uploader->render());
    }

    $categories = RMFunctions::load_images_categories("WHERE status='open' ORDER BY id_cat DESC", true);

    RMTemplate::get()->add_style('bootstrap.min.css', 'rmcommon');
    RMTemplate::get()->add_style('imgmgr.css', 'rmcommon');
    RMTemplate::get()->add_style('pagenav.css', 'rmcommon');
    RMTemplate::get()->add_style('editor_img.css', 'rmcommon');
    if($type=='tiny' && $target!='container'){
        RMTemplate::get()->add_script(RMCURL.'/api/editors/tinymce/tiny_mce_popup.js');
    } elseif($target!='container'&&$type!='external') {
        RMTemplate::get()->add_head('<script type="text/javascript">var exmPopup = window.parent.'.$en.';</script>');
    }

    RMEvents::get()->run_event('rmcommon.loading.editorimages', '');

    include RMTemplate::get()->get_template('rmc-editor-image.php', 'module', 'rmcommon');

} elseif($action=='load-images'){

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    if (!$xoopsSecurity->check()){
        _e('Sorry, unauthorized operation!','rmcommon');
        echo '<script type="text/javascript">window.location.href="tiny-images.php";</script>';
        die();
    }
    
    // Check if some category exists
    $catnum = RMFunctions::get_num_records("mod_rmcommon_images_categories");
    if ($catnum<=0){
        send_message(__('There are not categories yet! Please create one in order to add images.','rmcommon'));
        die();
    }
    
    if ($cat->isNew()){
        send_message(__('You must select a category before','rmcommon'));
        die();
    }
    
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_images");
    if (!$cat->isNew()) $sql .= " WHERE cat='".$cat->id()."'";
    /**
     * Paginacion de Resultados
     */
    $page = intval(rmc_server_var($_REQUEST, 'page', 1));
    $page = $page<=0 ? $page = 1 : $page;
    $limit = 30;
    list($num) = $db->fetchRow($db->query($sql));
    
    $tpages = ceil($num / $limit);
    $page = $page > $tpages ? $tpages : $page;

    $start = $num<=0 ? 0 : ($page - 1) * $limit;
    
    $nav = new RMPageNav($num, $limit, $page, 5);
    $nav->target_url('javascript:;" onclick="show_library({PAGE_NUM});');
    
    // Get categories
    $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_images")." ".(!$cat->isNew() ? "WHERE cat='".$cat->id()."'" : '')." ORDER BY id_img DESC LIMIT $start,$limit";

    $result = $db->query($sql);
    $images = array();
    $categories = array();
    $authors = array();
    
    $category = $cat;
    $sizes = $category->getVar('sizes');
    $current_size = array();
    
    foreach ($sizes as $size){
        if (empty($current_size)){
            $current_size = $size;
        } else {
            if ($current_size['width']>=$size['width'] && $size['width']>0){
                $current_size = $size;
            }
        }
    }

    $mimes = include(XOOPS_ROOT_PATH.'/include/mimetypes.inc.php');

    while($row = $db->fetchArray($result)){
        $img = new RMImage();
        $img->assignVars($row);
        if (!isset($categories[$img->getVar('cat')])){
            $categories[$img->getVar('cat')] = new RMImageCategory($img->getVar('cat'));
        }
        
        if (!isset($authors[$img->getVar('uid')])){
            $authors[$img->getVar('uid')] = new XoopsUser($img->getVar('uid'));
        }
        
        $fd = pathinfo($img->getVar('file'));
        $filesurl = XOOPS_UPLOAD_URL.'/'.date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date'));
        
        $thumb = date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/sizes/'.$fd['filename'].'_'.$current_size['width'].'x'.$current_size['height'].'.'.$fd['extension'];
        if(!file_exists(XOOPS_UPLOAD_PATH.'/'.$thumb)){
            $thumb = date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/'.$fd['filename'].'.'.$fd['extension'];
        }
        
        $ret = array(
            'id'        => $img->id(),
            'title'        => $img->getVar('title'),
            'date'        => formatTimestamp($img->getVar('date'), 'l'),
            'desc'      => $img->getVar('desc', 'n'),
            'cat'        => $categories[$img->getVar('cat')]->getVar('name'),
            'author'    => $authors[$img->getVar('uid')],
            'thumb'      => XOOPS_UPLOAD_URL.'/'.$thumb,
            'medium'    => $img->get_by_size( 300 ),
            'url'		=> $filesurl,
            'file'      => $fd['filename'],
            'extension' => $fd['extension'],
            'mime'      => isset($mimes[$fd['extension']]) ? $mimes[$fd['extension']] : 'application/octet-stream',
            'links'     => array(
                    'file'=>array('caption'=>__('File URL','rmcommon'),'value'=>XOOPS_UPLOAD_URL.'/'.date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/'.$img->getVar('file')),
                    'none'=>array('caption'=>__('None','rmcommon'),'value'=>'')
            )
        );
        
        $images[] = RMEvents::get()->run_event('rmcommon.loading.single.editorimgs', $ret, rmc_server_var($_REQUEST, 'url', ''));
    }

    include RMTemplate::get()->get_template('rmc-images-list-editor.php','module','rmcommon');
    
}