<?php
$tpl = RMTemplate::get();

$htmlScripts = $tpl->get_scripts(true);
$htmlScripts['inlineHeader'] = $tpl->inline_scripts();
$htmlScripts['inlineFooter'] = $tpl->inline_scripts(1);
$htmlStyles = $tpl->get_styles(true);

$metas = $tpl->get_metas();
