<?php
// $Id: images.php 999 2012-07-02 03:53:17Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* This is the images manager file for RMCommon. It is based on EXM system
* and as a substitute for xoops image manager
*/

include_once '../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/rmcommon/admin-loader.php';
define('RMCLOCATION','imgmanager');

/**
* Show all images existing in database
*/
function show_images(){
    global $xoopsModule, $xoopsModuleConfig, $rmTpl;

    define('RMCSUBLOCATION','showimages');

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    // Check if some category exists
    $catnum = RMFunctions::get_num_records("mod_rmcommon_images_categories");
    if ($catnum<=0) {
        redirectMsg('images.php?action=newcat', __('There are not categories yet! Please create one in order to add images.','rmcommon'), 1);
        die();
    }

    $cat = rmc_server_var($_GET, 'category', 0);
    if ($cat<=0) {
        header('location: images.php?action=showcats');
        die();
    }
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_images");
    if ($cat>0) $sql .= " WHERE cat='$cat'";
    /**
	 * Paginacion de Resultados
	 */
    $page = rmc_server_var($_GET, 'page', 1);
    $limit = $xoopsModuleConfig['imgsnumber'];
    list($num) = $db->fetchRow($db->query($sql));

    $tpages = ceil($num / $limit);
    $page = $page > $tpages ? $tpages : $page;

    $start = $num<=0 ? 0 : ($page - 1) * $limit;

    $nav = new RMPageNav($num, $limit, $page, 5);
    $nav->target_url('images.php?'.($cat>0 ? 'category='.$cat : '').'&page={PAGE_NUM}');

    // Get categories
    $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_images")." ".($cat>0 ? "WHERE cat='$cat'" : '')." ORDER BY id_img DESC LIMIT $start,$limit";

    $result = $db->query($sql);
    $images = array();
    $categories = array();
    $authors = array();

    $category = new RMImageCategory($cat);
    $sizes = $category->getVar('sizes');
    $current_size = array();

    foreach ($sizes as $size) {
        if (empty($current_size)) {
            $current_size = $size;
        } else {
            if ($current_size['width']>=$size['width'] && $size['width']>0) {
                $current_size = $size;
            }
        }
    }

    while ($row = $db->fetchArray($result)) {
        $img = new RMImage();
        $img->assignVars($row);
        if (!isset($categories[$img->getVar('cat')])) {
            $categories[$img->getVar('cat')] = new RMImageCategory($img->getVar('cat'));
        }

        if (!isset($authors[$img->getVar('uid')])) {
            $authors[$img->getVar('uid')] = new XoopsUser($img->getVar('uid'));
        }

        $fd = pathinfo($img->getVar('file'));

        $images[] = array(
            'id'        => $img->id(),
            'title'        => $img->getVar('title'),
            'date'        => $img->getVar('date'),
            'desc'      => $img->getVar('desc', 'n'),
            'cat'        => $categories[$img->getVar('cat')]->getVar('name'),
            'author'    => $authors[$img->getVar('uid')],
            'file'      => XOOPS_UPLOAD_URL.'/'.date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/sizes/'.$fd['filename'].'-'.$current_size['name'].'.'.$fd['extension'],
            'big'        => XOOPS_UPLOAD_URL.'/'.date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/'.$fd['filename'].'.'.$fd['extension']
        );
    }

    $categories = RMFunctions::load_images_categories();

    if (RMFunctions::plugin_installed('lightbox')) {
        RMLightbox::get()->add_element('#list-images a.bigimages');
        RMLightbox::get()->render();
    }

    RMBreadCrumb::get()->add_crumb(__('Images Management','rmcommon'),'images.php');
    RMBreadCrumb::get()->add_crumb($category->getVar('name'));

    $rmTpl->assign('xoops_pagetitle', sprintf(__('Images Management: %s', 'rmcommon'), $category->getVar('name')));

    xoops_cp_header();
    RMFunctions::create_toolbar();
    RMTemplate::get()->add_style('imgmgr.css','rmcommon');
    RMTemplate::get()->add_style('general.min.css','rmcommon');
    RMTemplate::get()->add_script('jquery.checkboxes.js', 'rmcommon');
    include RMTemplate::get()->get_template('rmc-images-images.php','module','rmcommon');
    xoops_cp_footer();

}

function images_form($edit = 0){
    global $xoopsModule, $xoopsModuleConfig, $xoopsSecurity, $xoopsUser, $rmc_config, $common;

    define('RMCSUBLOCATION','addimages');

    $category = rmc_server_var($_GET, 'category', 0);
    $cat = new RMImageCategory($category);

    if (!$cat->isNew() && $cat->getVar('status')!='open') {
        showMessage(sprintf(__('Category %s is closed. Please, select another category.','rmcommon'), '"'.$cat->getVar('name').'"'), 1);
        $cat = new RMImageCategory();
    }

    /*$upload = new RMFlashUploader('images', 'images.php');*/
    if (!$cat->isNew()) {

        $uploader = new Common\Core\Helpers\Uploader('images-uploader');
        $uploader->includeDropzone();

        $script = "(function(){cuImagesManager.init('" . RMCURL . "/include/upload.php', " . (($cat->getVar('filesize') * $cat->getVar('sizeunit')) / 1000000) . ");}());";
        $common->template()->add_inline_script($script, true);

    }

    $common->template()->add_jquery(true, true);
    $common->template()->add_style('imgmgr.min.css', 'rmcommon');
    $common->template()->add_script('images-manager.min.js', 'rmcommon');
    $common->template()->add_script('images.min.js', 'rmcommon');

    // Load Categories
    $categories = RMFunctions::load_images_categories("WHERE status='open' ORDER BY id_cat DESC");

    RMFunctions::create_toolbar();

    RMBreadCrumb::get()->add_crumb(__('Images Manager','rmcommon'), 'images.php');
    if (!$cat->isNew()) {
        RMBreadCrumb::get()->add_crumb($cat->getVar('name'), 'images.php?category='.$cat->id());
        RMBreadCrumb::get()->add_crumb(__('Upload Images','rmcommon'));
    } else {
        RMBreadCrumb::get()->add_crumb(__('Upload Images','rmcommon'));
    }

    xoops_cp_header();
    $isupdate = false;
    include RMTemplate::get()->get_template('rmc-images-upload-images.php','module','rmcommon');

    xoops_cp_footer();

}

function show_categories(){
    global $xoopsModule, $xoopsModuleConfig, $xoopsConfig, $xoopsSecurity, $rmTpl;

    define('RMCSUBLOCATION','showcategories');

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_images_categories");
    /**
	 * Paginacion de Resultados
	 */
    $page = rmc_server_var($_GET, 'page', 1);
    $limit = $xoopsModuleConfig['catsnumber'];
    list($num) = $db->fetchRow($db->query($sql));

    $tpages = ceil($num / $limit);
    $page = $page > $tpages ? $tpages : $page;

    $start = $num<=0 ? 0 : ($page - 1) * $limit;

    $nav = new RMPageNav($num, $limit, $page, 5);
    $nav->target_url('images.php?action=showcats&page={PAGE_NUM}');

    // Get categories
    $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_images_categories")." ORDER BY id_cat DESC LIMIT $start,$limit";

    $result = $db->query($sql);
    $categories = array();

    while ($row = $db->fetchArray($result)) {
        $cat = new RMImageCategory();
        $cat->assignVars($row);
        $groups = $cat->getVar('groups');
        $categories[] = array(
            'id'        =>    $cat->id(),
            'name'        =>    $cat->getVar('name'),
            'status'    =>    $cat->getVar('status'),
            'gwrite'    =>    RMFunctions::get_groups_names($groups['write']),
            'gread'        =>    RMFunctions::get_groups_names($groups['read']),
            'sizes'        =>    $cat->getVar('sizes'),
            'images'    =>    RMFunctions::get_num_records('mod_rmcommon_images', 'cat='.$cat->id())
        );
    }

    RMTemplate::get()->add_style('general.min.css','rmcommon');
    RMTemplate::get()->add_style('imgmgr.css','rmcommon');
    RMTemplate::get()->add_script('jquery.checkboxes.js', 'rmcommon');
    RMFunctions::create_toolbar();

    RMBreadCrumb::get()->add_crumb(__('Images Manager','rmcommon'), 'images.php');
    RMBreadCrumb::get()->add_crumb(__('Categories','rmcommon'));

    $rmTpl->assign('xoops_pagetitle', __('Images Manager: Categories','rmcommon'));

    xoops_cp_header();

    include RMTemplate::get()->get_template('rmc-images-categories.php', 'module', 'rmcommon');

    xoops_cp_footer();

}

/**
* Show form to create categories
*/
function new_category($edit = 0){
    global $rmTpl;

    define('RMCSUBLOCATION','newcategory');

    $active = 'open';
    $name = '';

    extract($_GET);

    // Check category to edit
    if ($edit) {
        if ($id<=0) {
            redirectMsg('images.php?action=showcats', __('You must specify a category id to edit!','rmcommon'), 1);
            die();
        }

        $cat = new RMImageCategory($id);

        if ($cat->isNew()) {
            redirectMsg('images.php?action=showcats', __('The specified category does not exist!','rmcommon'), 1);
            die();
        }

        // Write and read permissions
        $perms = $cat->getVar('groups');
        $write = isset($write) ? $write : $perms['write'];
        $read = isset($read) ? $read : $perms['read'];
        // Active
        $active = $cat->getVar('status');

    }

    RMFunctions::create_toolbar();

    RMBreadCrumb::get()->add_crumb(__('Images Manager','rmcommon'),'images.php');
    RMBreadCrumb::get()->add_crumb($edit ? __('Edit Category','rmcommon') : __('Add Category','rmcommon'));

    $rmTpl->assign('xoops_pagetitle', sprintf(__('Images Manager: %s','rmcommon'), $edit ? __('Edit Category','rmcommon') : __('Add category','rmcommon')));

    xoops_cp_header();

    $form = new RMForm('','','');
    $fwrite = new RMFormGroups('','write',true,1, 3, isset($write) ? $write : array(XOOPS_GROUP_ADMIN));
    $fread = new RMFormGroups('','read',true,1, 3, isset($read) ? $read : array(0));

    RMTemplate::get()->add_script('imgmanager.js', 'rmcommon');
    include RMTemplate::get()->get_template('rmc-categories-form.php', 'module', 'rmcommon');
    RMTemplate::get()->add_style('imgmgr.css','rmcommon');
    xoops_cp_footer();

}

/**
* Stores data for new categories
*/
function save_category($edit = 0){
    global $xoopsDB, $xoopsModuleConfig, $xoopsModule;

    $q = 'images.php?action='.($edit ? 'editcat' : 'newcat');
    foreach ($_POST as $k => $v) {
        if ($k=='action' || $k=='XOOPS_TOKEN_REQUEST') continue;
        if (is_array($v)) {
            $q .= '&'.RMFunctions::urlencode_array($v, $k);
        } else {
            $q .= '&'.$k.'='.urlencode($v);
        }

    }
    extract($_POST);

    if ($edit) {

        if ($id<=0) {
            redirectMsg('images.php?action=showcats', __('Specify a valid category id','rmcommon'), 1);
            die();
        }

        $cat = new RMImageCategory($id);
        if ($cat->isNew()) {
            redirectMsg('images.php?action=showcats', __('Specified category does not exists!','rmcommon'), 1);
            die();
        }

    } else {
        $cat = new RMImageCategory();
    }

    if ($name=='') {
        redirectMsg($q, __('Please specify a category name','rmcommon'), 1);
        die();
    }

    if (empty($read)) $read = array(0);
    if (empty($write)) $write = array(0);

    // Check if resize data is correct
    $schecked = array();
    foreach ($sizes as $size) {
        if (trim($size['name'])=='') continue;
        if ($size['type']!='none' && $size['width']<=0 && $size['height']<=0) continue;
        $schecked[] = $size;
    }

    if (empty($schecked)) {
        redirectMsg($q, __('You must create one size for this category at least!','rmcommon'), 1);
        die();
    }

    // Check if there are a category with same name
    $num = RMFunctions::get_num_records('mod_rmcommon_images_categories', "name='$name'".($edit ? " AND id_cat<>'".$cat->id()."'" : ''));
    if ($num>0) {
        redirectMsg($q, __('There is already a category with the same name!','rmcommon'), 1);
        die();
    }

    $cat->setVar('name', $name);
    $cat->setVar('status', $status);
    $cat->setVar('groups', array('write'=>$write,'read'=>$read));
    $cat->setVar('sizes',$schecked);
    $cat->setVar('filesize', $filesize<=0 ? '50' : $filesize);
    $cat->setVar('sizeunit', $sizeunit<=0 ? '1024' : $sizeunit);

    if ($cat->save()) {
        RMUris::redirect_with_message(__($edit ? 'Category updated successfully!' : 'Category saved successfully!','rmcommon'), 'images.php?action=showcats', RMMSG_SUCCESS);
    } else {
        RMUris::redirect_with_message(__('There were some erros while trying to save this category.','rmcommon').'<br />'.$cat->errors(), $q, RMMSG_ERROR);
    }

}

/**
* This functions allows to modify the status of categories
*/
function category_status($action='open'){

    $cats = rmc_server_var($_REQUEST, 'cats', array());

    if (empty($cats)) {
        $id = rmc_server_var($_GET, 'id', 0);

        if ($id<=0) {
            redirectMsg('images.php?action=showcats', __('Specify one category at least to change status!','rmcommon'), 1);
            die();
        }

        $cats[] = $id;

    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "UPDATE ".$db->prefix("mod_rmcommon_images_categories")." SET status='".$action."' WHERE id_cat IN(".implode(',',$cats).")";

    if ($db->queryF($sql)) {
        redirectMsg('images.php?action=showcats', __('Database updated successfully!','rmcommon'), 0);
        die();
    } else {
        redirectMsg('images.php?action=showcats', __('There were some erros while updating database:','rmcommon').'<br />'.$db->error(), 1);
        die();
    }

}

function send_error($message){
    $data['error'] = 1;
    $data['message'] = $message;
    echo json_encode($data);
    die();
}

function resize_images(){
    global $xoopsUser, $xoopsLogger, $common;

    $common->ajax()->prepare();

    if(!$common->security()->check(false, false, 'CUTOKEN')){
        $common->ajax()->notifyError(__('Session token invalid!', 'rmcommon'), 0);
    }

    $id = $common->httpRequest()->get('img', 'integer', 0);

    if ($id<=0) {
        $common->ajax()->notifyError(__('Image not valid!','rmcommon'), 0);
    }

    $image = new RMImage($id);
    if ($image->isNew()) {
        $common->ajax()->notifyError(__('Image not found!','rmcommon'), 0);
    }

    // Resize image
    $cat = new RMImageCategory($image->getVar('cat'));
    if (!$cat->user_allowed_toupload($xoopsUser)) {
        $common->ajax()->notifyError(__('You have not authorization to resize images!','rmcommon'), 0);
    }

    $sizes = $cat->getVar('sizes');
    $updir = XOOPS_UPLOAD_PATH.'/'.date('Y', $image->getVar('date')).'/'.date('m',time());
    $upurl = XOOPS_UPLOAD_URL.'/'.date('Y', $image->getVar('date')).'/'.date('m',time());;
    $width = 0;
    $tfile = '';

    $ret['sizes'] = array();
    foreach ($sizes as $size) {

        if ($size['width']<=0 && $size['height']<=0) continue;

        $fd = pathinfo($updir.'/'.$image->getVar('file'));

        $name = $updir.'/sizes/'.$fd['filename'].'-'.$size['name'].'.'.$fd['extension'];

        $ret['sizes'][$size['name']] = str_replace(XOOPS_UPLOAD_PATH, XOOPS_UPLOAD_URL, $name);;

        list($currentWidth, $currentHeight) = getimagesize($updir.'/'.$image->getVar('file'));

        $sizer = new RMImageResizer($updir.'/'.$image->getVar('file'), $name);

        $resize = true;

        // TODO 5: Verificar que funcione correctamente
        if ($size['type']=='crop') {

            if($size['width'] > $currentWidth || $size['height'] > $currentHeight)
                $resize = false;

        } else {

            if($size['width']>$currentWidth && $size['height']>$currentHeight)
                $resize = false;

            if($size['width']>$currentWidth)
                $size['width'] = $currentWidth;

            if($size['height']>$currentHeight)
                $size['height'] = $currentHeight;

        }

        if ($resize) {
            switch ($size['type']) {
                case 'crop':
                    $sizer->resizeAndCrop($size['width'], $size['height']);
                    break;
                default:
                    if ($size['width']<=0 || $size['height']<=0) {
                        $sizer->resizeWidth($size['width']);
                    } else {
                        $sizer->resizeWidthOrHeight($size['width'], $size['height']);
                    }
                    break;
            }
        }
        if ($size['width']<=$width || $width==0) {
            $width = $size['width'];
            $tfile = str_replace(XOOPS_UPLOAD_PATH, XOOPS_UPLOAD_URL, $name);
        }

    }

    $common->ajax()->response(
        sprintf(__('%s done!', 'rmcommon'), $image->getVar('file')), 0, 1, [
            'file' => $tfile,
            'title' => $image->title,
        ]
    );

}

/**
* Function to edit images
*/
function edit_image(){
    global $xoopsUser, $xoopsSecurity, $rmTpl, $rmEvents;

    $id = rmc_server_var($_GET, 'id', 0);
    $page = rmc_server_var($_GET, 'page', '');
    if ($id<=0) {
        redirectMsg('images.php', __('Invalid image ID', 'rmcommon'), 1);
        die();
    }

    $image = new RMImage($id);
    if ($image->isNew()) {
        redirectMsg('images.php', __('Image not found!', 'rmcommon'), 1);
        die();
    }

    $cat = new RMImageCategory($image->getVar('cat'));
    $sizes = $cat->getVar('sizes');
    $current_size = array();

    $fd = pathinfo($image->getVar('file'));
    $updir = '/'.date('Y', $image->getVar('date')).'/'.date('m', $image->getVar('date'));

    foreach ($sizes as $size) {
        if ($size['width']<=0) continue;
        if (empty($current_size)) {
            $current_size = $size;
        } else {
            if ($current_size['width']>=$size['width'] && $size['width']>0) {
                $current_size = $size;
            }
        }

        if(!file_exists(XOOPS_UPLOAD_PATH.$updir.'/sizes/'.$fd['filename'].'-'.$size['name'].'.'.$fd['extension']))
            continue;

        $image_data['sizes'][] = array(
            'file' => XOOPS_UPLOAD_URL.$updir.'/sizes/'.$fd['filename'].'-'.$size['name'].'.'.$fd['extension'],
            'name' => $size['name']
        );
    }

    $image_data['thumbnail'] = XOOPS_UPLOAD_URL.$updir.'/sizes/'.$fd['filename'].'-'.$current_size['name'].'.'.$fd['extension'];
    $mimes = include(XOOPS_ROOT_PATH.'/include/mimetypes.inc.php');
    $image_data['mime'] = isset($mimes[$fd['extension']]) ? $mimes[$fd['extension']] : 'application/octet-stream';
    $image_data['file'] = $image->getVar('file');
    $image_data['date'] = $image->getVar('date');
    $image_data['title'] = $image->getVar('title');
    $image_data['desc'] = $image->getVar('desc', 'e');
    $image_data['url'] = XOOPS_UPLOAD_URL.$updir.'/'.$image->getVar('file');

    $categories = RMFunctions::load_images_categories("WHERE status='open' ORDER BY id_cat DESC");

    RMFunctions::create_toolbar();
    RMBreadCrumb::get()->add_crumb(__('Images Manager','rmcommon'),'images.php');
    RMBreadCrumb::get()->add_crumb(__('Edit Image','rmcommon'));

    $rmTpl->assign('xoops_pagetitle', __('Edit Image','rmcommon'));

    xoops_cp_header();

    RMTemplate::get()->add_script('images.min.js', 'rmcommon');
    RMTemplate::get()->add_script('include/js/jquery.validate.min.js');
    RMTemplate::get()->add_style('imgmgr.css', 'rmcommon');
    include RMTemplate::get()->get_template('rmc-images-edit.php','module','rmcommon');

    xoops_cp_footer();
}

/**
* Update image data
*/
function update_image(){
    global $xoopsUser, $xoopsSecurity;

    set_time_limit(0);

    $title = rmc_server_var($_POST, 'title', '');
    $category = rmc_server_var($_POST, 'cat', '');
    $desc = rmc_server_var($_POST, 'desc', '');
    $page = rmc_server_var($_POST, 'page', 1);
    $id = rmc_server_var($_POST, 'id', 0);

    if (!$xoopsSecurity->check()) {
        redirectMsg('images.php', __('Operation not allowed!','rmcommon'), 1);
        die();
    }

    if ($id<=0) {
        redirectMsg("images.php?category=$cat&page=$page", __('Image ID not provided!','rmcommon'), 1);
        die();
    }

    if (trim($title)=='') {
        redirectMsg("images.php?action=edit&id=$id&page=$page", __('You must  provide a title for this image','rmcommon'), 1);
        die();
    }

    $image = new RMImage($id);
    if ($image->isNew()) {
        redirectMsg("images.php?category=$cat&page=$page", __('Image not exists!','rmcommon'), 1);
        die();
    }

    $cat = new RMImageCategory($category);
    if ($cat->isNew()) {
        redirectMsg("images.php", __('Category not exist!','rmcommon'), 1);
        die();
    }

    if ($cat->id()!=$image->getVar('cat')) {
        $pcat = new RMImageCategory($image->getVar('cat'));
    }

    $image->setVar('title', $title);
    $image->setVar('desc', $desc);
    if (isset($pcat)) $image->setVar('cat', $cat->id());

    if (!$image->save()) {
        redirectMsg("images.php?action=edit&id=$id&page=$page", __('the image could not be updated!','rmcommon').'<br />'.$image->errors(), 1);
        die();
    }

    // Modify image dimensions if category has changed
    if (!isset($pcat)) {
        redirectMsg("images.php?category=".$cat->id()."&page=$page", __('Image updated succesfully!','rmcommon'), 0);
        die();
    }

    $fd = pathinfo($image->getVar('file'));
    $updir = XOOPS_UPLOAD_PATH.'/'.date('Y', $image->getVar('date')).'/'.date('m',time());

    // Delete current image files
    foreach ($pcat->getVar('sizes') as $size) {
        if ($size['width']<=0) continue;
        $file = $updir.'/sizes/'.$fd['filename'].'-'.$size['name'].'.'.$fd['extension'];
        @unlink($file);

    }

    // Create new image files
    foreach ($cat->getVar('sizes') as $size) {
        if ($size['width']<=0 && $size['height']<=0) continue;

        $name = $updir.'/sizes/'.$fd['filename'].'-'.$size['name'].'.'.$fd['extension'];
        $sizer = new RMImageResizer($updir.'/'.$image->getVar('file'), $name);

        switch ($size['type']) {
            case 'crop':
                $sizer->resizeAndCrop($size['width'], $size['height']);
                break;
            default:
                if ($size['width']<=0 || $size['height']<=0) {
                    $sizer->resizeWidth($size['width']);
                } else {
                    $sizer->resizeWidthOrHeight($size['width'], $size['height']);
                }
                break;
        }

        $width = $width==0 ? $size['width'] : $width;
        if ($width<$size['width']) {
            $with = $size['width'];
            $tfile = str_replace(XOOPS_UPLOAD_PATH, XOOPS_UPLOAD_URL, $name);
        }
    }

    redirectMsg('images.php?category='.$cat->id(), __('Image updated successfully!', 'rmcommon'), 0);

}

/**
* Delete an image
*/
function delete_image(){

    $ids = rmc_server_var($_REQUEST, 'imgs', array());
    $page = rmc_server_var($_REQUEST, 'page', 0);
    $category = rmc_server_var($_REQUEST, 'category', 0);

    if (count($ids)<=0) {
        redirectMsg('images.php?category='.$category.'&page='.$page, __('Please, speciy an image at least!','rmcommon'), 1);
        die();
    }

    $errors = '';

    foreach ($ids as $id) {

        $image = new RMImage($id);
        if ($image->isNew()) {
            redirectMsg('images.php', __('Image not exists!','rmcommon'), 1);
            die();
        }

        $cat = new RMImageCategory($image->getVar('cat'));

        $fd = pathinfo($image->getVar('file'));
        $updir = XOOPS_UPLOAD_PATH.'/'.date('Y', $image->getVar('date')).'/'.date('m',time());

        // Delete current image files
        foreach ($cat->getVar('sizes') as $size) {
            if ($size['width']<=0) continue;
            $file = $updir.'/sizes/'.$fd['filename'].'-'.$size['name'].'.'.$fd['extension'];
            @unlink($file);
        }

        $file = $updir.'/'.$image->getVar('file');
        @unlink($file);

        if (!$image->delete()) {
            $errors .= $image->errors();
        }

    }

    if ($errors!='') {
        redirectMsg('images.php?category='.$cat->id().'&page='.$page, __('Errors ocurred during images deletion!', 'rmcommon').'<br />'.$errors, 0);
    } else {
        redirectMsg('images.php?category='.$cat->id().'&page='.$page, __('Images deleted successfully!', 'rmcommon'), 0);
    }

}

/**
* This function deletes all images in a category and the category
*/
function delete_category(){
    global $xoopsSecurity;

    $id = rmc_server_var($_GET, 'id', 0);

    if (!$xoopsSecurity->check()) {
        redirectMsg('images.php?action=showcats', __('Operation not allowed!', 'rmcommon'), 1);
        die();
    }

    if ($id<=0) {
        redirectMsg('images.php?action=showcats', __('Category ID not provided', 'rmcommon'), 1);
        die();
    }

    $cat = new RMImageCategory($id);
    if ($cat->isNew()) {
        redirectMsg('images.php?action=showcats', __('Category not found', 'rmcommon'), 1);
        die();
    }

    $sizes = array();
    foreach ($cat->getVar('sizes') as $size) {
        if ($size['width']<=0) continue;

        $sizes[] = '-'.$size['name'];

    }

    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_images")." WHERE cat='".$cat->id()."'";
    $result = $db->query($sql);

    while ($row = $db->fetchArray($result)) {
        $image = new RMImage();
        $image->assignVars($row);

        $updir = XOOPS_UPLOAD_PATH.'/'.date('Y', $image->getVar('date')).'/'.date('m',time());
        $fd = pathinfo($image->getVar('file'));

        foreach ($sizes as $size) {
            $file = $updir.'/sizes/'.$fd['filename'].$size.'.'.$fd['extension'];
            @unlink($file);
        }
        $file = $updir.'/'.$image->getVar('file');
        @unlink($file);
        $image->delete();
    }

    if ($cat->delete()) {
        redirectMsg('images.php?action=showcats', __('Category deleted successfully!', 'rmcommon'), 0);
    } else {
        redirectMsg('images.php?action=showcats', __('Errors ocurred while deleting the category', 'rmcommon').'<br />'.$cat->errors(), 0);
    }

}

/**
* Update image thumbnails
*/
function update_thumbnails(){
    global $xoopsUser, $xoopsSecurity;
    $cat = new RMImageCategory(rmc_server_var($_POST, 'category', 0));
    $imgs = rmc_server_var($_POST, 'imgs', array());

    $ids = implode(',', $imgs);

    RMTemplate::getInstance()->add_jquery(true, true);
    RMTemplate::get()->add_style('imgmgr.css', 'rmcommon');
    RMTemplate::get()->add_script('images.min.js', 'rmcommon');

    xoops_cp_header();
    RMFunctions::create_toolbar();

    $isupdate = true;
    RMTemplate::get()->add_head("<script type='text/javascript'>
    \$(document).ready(function(){
        ids = [$ids];
        total = ".count($imgs).";
        \$('#resizer-bar').show('slow');
        \$('#resizer-bar').effect('highlight',{},1000);
        \$('#gen-thumbnails').show();

        var increments = 1/total*100;
        url = '".RMCURL."/images.php';

        params = '".TextCleaner::getInstance()->encrypt($xoopsUser->uid().'|'.RMCURL.'/images.php'.'|'.$xoopsSecurity->createToken(), true)."';
        resize_image(params);
    });</script>");

    include RMTemplate::get()->get_template('rmc-images-upload-images.php','module','rmcommon');

    xoops_cp_footer();

}

$action = rmc_server_var($_REQUEST, 'action', '');

switch ($action) {
    case 'showcats':
        show_categories();
        break;
    case 'newcat':
        new_category();
        break;
    case 'editcat':
        new_category(1);
        break;
    case 'delcat':
        delete_category();
        break;
    case 'save':
        save_category();
        break;
    case 'saveedit':
        save_category(1);
        break;
    case 'opencat':
        category_status('open');
        break;
    case 'closecat':
        category_status('close');
        break;
    case 'new':
        images_form(0);
        break;
    case 'resize':
        resize_images();
        break;
    case 'edit':
        edit_image();
        break;
    case 'update':
        update_image();
        break;
    case 'delete':
        delete_image();
        break;
    case 'thumbs':
        update_thumbnails();
        break;
    default:
        show_images();
        break;
}
