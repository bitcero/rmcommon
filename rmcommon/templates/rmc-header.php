<?php

$tpl = RMTemplate::get();

$scripts = '';
$tscript = '';
$jquery_and_bootstrap = array();
$fscripts = '';
$temp = $tpl->get_scripts();

$script_tpl = '<script id="%s" type="%s" src="%s"%s></script>' . "\n";

foreach ($temp as $id => $script) {

    $type = $script['type'];
    $url = $script['url'];
    $footer = isset($script['footer']) ? $script['footer'] : 0;

    unset($script['type'], $script['url'], $script['footer']);

    $extra = '';
    foreach ($script as $name => $value) {
        $extra .= ' ' . $name . '="' . $value . '"';
    }

    if ( strpos($script['url'], 'jquery-latest.js')!==FALSE || strpos($script['url'], 'jquery.min.js')!==FALSE )

        $jquery_and_bootstrap[0] = sprintf( $script_tpl, $id, $type, $url, $extra ) . $tscript;

    elseif ( preg_match( "/bootstrap(\.min)?\.js/i", $script['url'] ) )

        $jquery_and_bootstrap[1] = sprintf( $script_tpl, $id, $type, $url, $extra ) . $tscript;

    elseif ( $footer )

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
