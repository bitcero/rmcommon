<?php
$tpl = RMTemplate::get();

$scripts = '';
$tscript = '';
$fscripts = '';
$temp = $tpl->get_scripts();

$script_tpl = '<script id="%s" type="%s" src="%s"%s></script>' . "\n";

$jquery_and_bootstrap = array();

if ( isset( $temp['jquery'] ) )
    $tscript .= '<script id="jquery" type="'.$temp['jquery']['type'].'" src="'.$temp['jquery']['url'].'"></script>'."\n";

if ( isset( $temp['jqueryui'] ) )
    $tscript .= '<script id="jqueryui" type="'.$temp['jqueryui']['type'].'" src="'.$temp['jqueryui']['url'].'"></script>'."\n";

if ( isset( $temp['jsbootstrap'] ) )
    $fscripts .= '<script id="jsbootstrap" type="'.$temp['jsbootstrap']['type'].'" src="'.$temp['jsbootstrap']['url'].'"></script>'."\n";

unset( $temp['jquery'],$temp['jqueryui'], $temp['jsbootstrap'] );


foreach ($temp as $id => $script) {

    $type = $script['type'];
    $url = $script['url'];
    $footer = isset($script['footer']) ? $script['footer'] : 0;

    unset($script['type'], $script['url'], $script['footer']);

    $extra = '';
    foreach ($script as $name => $value) {
        $extra .= ' ' . $name . '="' . $value . '"';
    }

    if ( $footer )

        $fscripts .= sprintf( $script_tpl, $id, $type, $url, $extra );

    else

        $tscript .= sprintf( $script_tpl, $id, $type, $url, $extra );

}

$tscript = implode("\n", $jquery_and_bootstrap) . $tscript;

$scripts = $tscript.$scripts;
unset($tscript);

$styles = '';
$fstyles = '';
$temp = $tpl->get_styles();

$style_tpl = '<link id="%s" type="text/css" rel="stylesheet" href="%s"%s>';

foreach ($temp as $id => $style) {

    $url = $style['url'];
    $footer = isset($style['footer']) ? $style['footer'] : 0;
    unset($style['url'], $style['footer'], $style['type']);

    $extra = '';
    foreach ($style as $name => $value) {
        $extra .= ' ' . $name . '="' . $value . '"';
    }

    if( $footer )
        $fstyles .= sprintf($style_tpl, $id, $url, $extra)."\n";
    else
        $styles .= sprintf($style_tpl, $id, $url, $extra)."\n";
}

$heads = '';
foreach ($tpl->tpl_head as $head) {
    $heads .= $head."\n";
}
$heads .= $tpl->head_scripts();

$metas = $tpl->get_metas();
