<?php
//error_reporting(0);
$xoopsOption['nocommon'] = 1;

require_once dirname(__DIR__) . '/../../../../mainfile.php';

foreach ($_GET as $k => $v) {
    $$k = $v;
}

$path = pathinfo(str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $url));
$base = str_replace($path['filename'] . '.' . $path['extension'], '', $url);
$base = str_replace('tpls/', '', $base);
$path['dirname'] = str_replace('/tpls', '', $path['dirname']);
if (is_file($path['dirname'] . '/lang/' . $lang . '.php')) {
    include $path['dirname'] . '/lang/' . $lang . '.php';
} else {
    @include $path['dirname'] . '/lang/en.php';
}

define('RMCPATH', XOOPS_ROOT_PATH . '/modules/rmcommon');
define('RMCURL', XOOPS_URL . '/modules/rmcommon');

ob_start();
include str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $url);
$content = ob_get_clean();

ob_start();
?>
<script type="text/javascript">
    var exmPopup = window.parent.<?php echo $name; ?>;
    $(document).ready(function(){
        $("head").prepend('<link rel="stylesheet" type="text/css" href="<?php echo XOOPS_URL; ?>/modules/rmcommon/css/editor-popups.css">');
    });
</script>
<?php
    $script = ob_get_clean();

    echo str_replace('</body>', $script . "\n" . '</body>', $content);
