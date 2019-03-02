<?php
/**
* EXM System API for i10n
*
* This API provides support for internationalization for
* components of EXM System.
*/

require RMCPATH.'/class/gettext/gettext.php';
require RMCPATH.'/class/gettext/streams.php';

/**
* Provides a method to get the current locale
* If does not exists a locale then returns en_US
*/
function get_locale()
{
    static $exm_locale;
    global $cuSettings;

    if (!isset($exm_locale) || $exm_locale == '') {
        $exm_locale = RMEvents::get()->trigger('rmcommon.get.locale', $cuSettings->lang);
    }

    if ($exm_locale=='') {
        $exm_locale = 'en';
    }

    return $exm_locale;
}

/**
* Load the language files content in a single object and order these files
* by domain. The domain must be unique for each element. By example: Application
* MyWords can have "mywords" as domain. Domains 'global' and 'system' are reserved
* by EXM System and could not be used by other elements.
*
* @param string $domain Unique identifier for this file
* @param string Local path to file
*/
function load_locale_file($domain, $file)
{
    global $l10n;

    if (isset($l10n[$domain])) {
        return;
    }

    if (is_readable($file)) {
        $cache = new CachedFileReader($file);
    } else {
        return;
    }

    $gettext = new gettext_reader($cache);

    if (isset($l10n[$domain])) {
        $l10n[$domain]->load_tables();
        $gettext->load_tables();
        $l10n[$domain]->cache_translations = array_merge($gettext->cache_translations, $l10n[$domain]->cache_translations);
    } else {
        $l10n[$domain] = $gettext;
    }

    unset($input, $gettext);
}

/**
* Read the MO file for a specific domain
*
* @uses This function must be called from every application to load the respective language file (mo file)
*
* @param string Unique identifier for language file
* @param string prefix for file name
*/
function load_mod_locale($domain, $prefix='')
{
    $exm_locale = get_locale();

    if ($domain=='') {
        return;
    }

    $path = XOOPS_ROOT_PATH.'/modules/'.$domain.'/lang/'.$prefix.$exm_locale.'.mo';
    load_locale_file($prefix.$domain, $path);
}

/**
* Read a MO file for translate a plugin
*/
function load_plugin_locale($plugin, $prefix='', $module='rmcommon')
{
    $exm_locale = get_locale();

    if ($plugin=='') {
        return;
    }

    $path = XOOPS_ROOT_PATH.'/modules/'.$module.'/plugins/'.$plugin.'/lang/'.$prefix.$exm_locale.'.mo';

    load_locale_file($prefix.$plugin, $path);
}

/**
* Read the locale string for a theme
* @param string Theme name
* @param string Prefix for file
* @param bool True for admin section, false for front section
*/
function load_theme_locale($theme, $prefix='', $gui=false)
{
    $exm_locale = get_locale();
    if ($theme=='') {
        return;
    }

    if ($gui) {
        $path = RMCPATH.'/themes/'.$theme.'/lang/'.$prefix.$exm_locale.'.mo';
    } else {
        $path = XOOPS_THEME_PATH.'/'.$theme.'/lang/'.$prefix.$exm_locale.'.mo';
    }
    load_locale_file($prefix.$theme, $path);
}

/**
* Allows to translate string from exm system elements
*/
function translate($text, $domain = 'system')
{
    global $l10n;

    if (!class_exists('RMEvents')) {
        return $l10n[$domain]->translate($text);
    }

    if (isset($l10n[$domain])) {
        return RMEvents::get()->run_event('rmcommon.get_locale_text', $l10n[$domain]->translate($text), $text, $domain);
    } else {
        return RMEvents::get()->run_event('rmcommon.get_locale_text', $text, $text, $domain);
    }
}

/**
* Translate a text and print it
* @param string Text to translate
* @param string Domain name. Here you must specify the app name (eg. system), or plugin name (eg. rss) or theme Name.
* The teme name must be specified with prefix "theme_". eg. "theme_exm" or "theme_simplex"
* @return print string
*/
function _e($text, $domain='rmcommon')
{
    echo translate($text, $domain);
}

/**
* Translate a text and print it
* @param string Text to translate
* @param string Domain name. Here you must specify the app name (eg. system), or plugin name (eg. rss) or theme Name.
* The teme name must be specified with prefix "theme_". eg. "theme_exm" or "theme_simplex"
* @return string
*/
function __($text, $domain='rmcommon')
{
    return translate($text, $domain);
}
