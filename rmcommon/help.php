<?php

require dirname(__DIR__) . '/../mainfile.php';

$common->ajax()->prepare();

$doc = RMHttpRequest::get('doc', 'string', '');

if ('' == trim($doc)) {
    exit();
}

$doc = explode(':', $doc);

// Check if doc is valid
if (count($doc) < 2 || count($doc) > 3) {
    exit(__('No valid help data!', 'rmcommon'));
}

// Check type
if (array_key_exists(2, $doc)) {
    $type = $doc[2]; // module, plugin, theme or gui
} else {
    $type = 'module';
}

if (!in_array($type, ['module', 'plugin', 'theme', 'gui'], true)) {
    exit(__('Not valid help component type!', 'rmcommon'));
}

$src = XOOPS_ROOT_PATH;
switch ($type) {
    case 'module':
        $src .= '/modules/';
        break;
    case 'plugin':
        $src .= '/modules/rmcommon/plugins/';
        break;
    case 'theme':
        $src .= '/themes/';
        break;
    case 'gui':
        $src .= '/modules/rmcommon/themes/';
        break;
}

$headerFile = $src . $doc[0] . '/help/help-header.php';
$footerFile = $src . $doc[0] . '/help/help-footer.php';

$src .= $doc[0] . '/help/' . $doc[1];

// Check if document exists
if (!file_exists($src)) {
    die(__('Not valid help document!', 'rmcommon'));
}

$content = file_get_contents($src);

if (file_exists($headerFile)) {
    include $headerFile;
}

echo TextCleaner::getInstance()->to_display($content);

if (file_exists($footerFile)) {
    include $footerFile;
}
