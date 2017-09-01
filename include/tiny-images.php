<?php
// $Id: tiny-images.php 999 2012-07-02 03:53:17Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMC_POPUP_IMAGES', 1);
include '../../../mainfile.php';
XoopsLogger::getInstance()->activated = false;
XoopsLogger::getInstance()->renderingEnabled = false;

function send_message($message){
    global $xoopsSecurity;

    echo $message;
    echo '<input type="hidden" id="ret-token" value="'.$xoopsSecurity->createToken().'" />';
    die();

}

$category = RMHttpRequest::post('category', 'integer', 0);
$action = RMHttpRequest::post( 'action', 'string', '' );
$cat = new RMImageCategory($category);
$type = RMHttpRequest::request( 'type', 'string', 'tiny' );
$multi = RMHttpRequest::request( 'multi', 'string', 'yes' );

$multi = $multi == 'yes' ? true : false;
$en = RMHttpRequest::request( 'name', 'string', '' );

// Check if target is different from editor
$target = RMHttpRequest::request( 'target', 'string', '' );
// Used when tiny must be loaded
$editor = RMHttpRequest::request( 'editor', 'string', '' );
$container = RMHttpRequest::request( 'idcontainer', 'string', '' );

include RMCPATH . '/js/cu-js-language.php';

if ($action=='') {

    RMTemplate::getInstance()->add_jquery(true, true);
    $common->template()->add_script('dropzone.min.js', 'rmcommon', ['id' => 'dropzone-js', 'footer' => 1]);
    RMTemplate::getInstance()->add_script('images-manager.min.js', 'rmcommon', ['id' => 'img-manager-js', 'footer' => 1] );
    RMTemplate::getInstance()->add_script('popup-images-manager.min.js', 'rmcommon', ['id' => 'popup-images-js', 'footer' => 1] );

    if (!$cat->isNew()) {

        $script = "(function(){cuImagesManager.init('" . RMCURL . "/include/upload.php', " . (($cat->getVar('filesize') * $cat->getVar('sizeunit')) / 1000000) . ");}());";
        $common->template()->add_inline_script($script, true);

    }

    $categories = RMFunctions::load_images_categories("WHERE status='open' ORDER BY id_cat DESC", true);

    RMTemplate::getInstance()->add_style('bootstrap.min.css', 'rmcommon');
    RMTemplate::getInstance()->add_style('imgmgr.css', 'rmcommon');
    RMTemplate::getInstance()->add_style('pagenav.css', 'rmcommon');
    RMTemplate::getInstance()->add_style('popup-images.min.css', 'rmcommon');

    if ($type=='tiny' && $target!='container') {

    } elseif ($target!='container'&&$type!='external'&&$type!='markdown') {
        RMTemplate::getInstance()->add_inline_script('var exmPopup = window.parent.exmCode'.ucfirst($container).';');
    }

    RMEvents::get()->trigger('rmcommon.loading.editorimages', '');

    include RMTemplate::getInstance()->path('rmc-editor-image.php', 'module', 'rmcommon');

} elseif ($action=='load-images') {

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    if (!$xoopsSecurity->check()) {
        send_message(__('Sorry, unauthorized operation!','rmcommon'));
        die();
    }

    // Check if some category exists
    $catnum = RMFunctions::get_num_records("mod_rmcommon_images_categories");
    if ($catnum<=0) {
        send_message(__('There are not categories yet! Please create one in order to add images.','rmcommon'));
        die();
    }

    if ($cat->isNew()) {
        send_message(__('You must select a category before','rmcommon'));
        die();
    }

    $sql = "SELECT COUNT(*) FROM ".$db->prefix("mod_rmcommon_images");
    if (!$cat->isNew()) $sql .= " WHERE cat='".$cat->id()."'";
    /**
     * Paginacion de Resultados
     */
    $page = (int)rmc_server_var($_REQUEST, 'page', 1);
    $page = $page<=0 ? $page = 1 : $page;
    $limit = 35;
    list($num) = $db->fetchRow($db->query($sql));

    $tpages = ceil($num / $limit);
    $page = $page > $tpages ? $tpages : $page;

    $start = $num<=0 ? 0 : ($page - 1) * $limit;

    $nav = new RMPageNav($num, $limit, $page, 5);
    $nav->target_url('#" onclick="show_library({PAGE_NUM}); return false;');

    // Get categories
    $sql = "SELECT * FROM ".$db->prefix("mod_rmcommon_images")." ".(!$cat->isNew() ? "WHERE cat='".$cat->id()."'" : '')." ORDER BY id_img DESC LIMIT $start,$limit";

    $result = $db->query($sql);
    $images = array();
    $categories = array();
    $authors = array();

    $category = $cat;
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

        $fd = pathinfo($img->getVar('file'));
        $filesurl = XOOPS_UPLOAD_URL.'/'.date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date'));

        $thumb = date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/sizes/'.$fd['filename'].'-'.$current_size['name'].'.'.$fd['extension'];
        if (!file_exists(XOOPS_UPLOAD_PATH.'/'.$thumb)) {
            $thumb = date('Y',$img->getVar('date')).'/'.date('m',$img->getVar('date')).'/'.$fd['filename'].'.'.$fd['extension'];
        }

        $images[] = array(
            'id'        => $img->id(),
            'title'        => $img->getVar('title'),
            'thumb'      => XOOPS_UPLOAD_URL.'/'.$thumb,

        );

    }

    include RMTemplate::getInstance()->path('rmc-images-list-editor.php','module','rmcommon');

} elseif ($action == 'image-details') {

    function images_send_json( $data ){

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode( $data );
        die();

    }

    // @todo: Make this
    /*if (!$xoopsSecurity->check()) {
        images_send_json(array(
            'message'   => __('Sorry, unauthorized operation!','rmcommon'),
            'error'     => 1
        ));
    }*/

    $id = RMHttpRequest::post( 'id', 'integer', 0 );
    if ( $id <= 0 )
        images_send_json( array(
            'message'   => __('No image specified', 'rmcommon'),
            'error'     => 1,
            'token'     => $xoopsSecurity->createToken()
        ) );

    $image = new RMImage( $id );
    if ( $image->isNew() )
        images_send_json( array(
            'message'   => __('Specified image does not exists', 'rmcommon'),
            'error'     => 1,
            'token'     => $xoopsSecurity->createToken()
        ) );

    $author = new RMUser( $image->uid );
    $original = pathinfo( $image->get_files_path() . '/' . $image->file );
    $dimensions = getimagesize( $image->get_files_path() . '/' . $image->file );
    $mimes = include XOOPS_ROOT_PATH . '/include/mimetypes.inc.php';

    $category_sizes = $cat->getVar('sizes');
    $sizes = array();
    foreach ($category_sizes as $i => $size) {
        if($size['width']<=0) continue;
        $tfile = $image->get_files_path() . '/sizes/' . $original['filename'].'-'.$size['name'].'.'.$original['extension'];
        if(!is_file($tfile)) continue;

        $t_dim = getimagesize( $tfile );

        $sizes[] = array(
            'width' => $t_dim[0],
            'height' => $t_dim[1],
            'url'   => $image->get_files_url() . '/sizes/' . $original['filename'].'-'.$size['name'].'.'.$original['extension'],
            'name'  => $size['name'],
        );
    }

    $sizes[] = array(
        'width' => $dimensions[0],
        'height' => $dimensions[1],
        'url'   => $image->getOriginal(),
        'name'  => __('Original', 'rmcommon')
    );

    $links = array(
        'none'=>array('caption'=>__('None','rmcommon'),'value'=>''),
        'file'=>array('caption'=>__('File URL','rmcommon'),'value'=>XOOPS_UPLOAD_URL.'/'.date('Y',$image->getVar('date')).'/'.date('m',$image->getVar('date')).'/'.$image->getVar('file'))
    );
    $links = RMEvents::get()->run_event( 'rmcommon.image.insert.links', $links, $image, RMHttpRequest::post( 'url', 'string', '' ) );

    // Image data
    $data = array(
        'id'        => $image->id(),
        'title'        => $image->title,
        'date'        => formatTimestamp($image->date, 'l'),
        'description'      => $image->getVar('desc', 'n'),
        'author'    => array(
            'uname' => $author->uname,
            'uid' => $author->uid,
            'avatar' => RMEvents::get()->run_event( 'rmcommon.get.avatar', $author->email, 40 ),
            'url'   => XOOPS_URL . '/userinfo.php?uid=' . $author->email
        ),
        'medium'    => $image->get_by_size( 300 ),
        'url'        => $image->get_files_url(),
        'original'  => array(
            'file'      => $original['basename'],
            'url'       => $image->getOriginal(),
            'size'      => RMFormat::bytes_format( filesize( $image->get_files_path() . '/' . $image->file ) ),
            'width'     => $dimensions[0],
            'height'    => $dimensions[1]
        ),
        'mime'      => isset($mimes[$original['extension']]) ? $mimes[$original['extension']] : 'application/octet-stream',
        'sizes'     => $sizes,
        'links'     => $links
    );

    $data = RMEvents::get()->run_event('rmcommon.loading.image.details', $data, $image, RMHttpRequest::request( 'url', 'string', '' ) );
    $data['token'] = $xoopsSecurity->createToken();

    images_send_json(
        $data
    );

}
