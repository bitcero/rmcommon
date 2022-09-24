<?php
// $Id: textcleaner.php 891 2011-12-30 08:41:31Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$aprotocols = [];

/**
 * Handles many methods for formatting output.
 */
class TextCleaner
{
    private $protocols = [];
    /**
     * Stores all the emoticons used on system
     */
    private $emots = [];
    /**
     * Tags that will be disbled when dohtml is activated
     */
    private $disable_tags = [
        /*'@<iframe[^>]*?>.*?</iframe>@si',*/
        '@<script[^>]*?>.*?</script>@si',
        '@<style[^>]*?>.*?</style>@si',
        '@<html[^>]*?>.*?</html>@si',
        '@<body[^>]*?>.*?</body>@si',
        '@<meta[^>]*?>.*?</meta>@si',
    ];

    /**
     * Singleton
     * @return TextCleaner
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Get all emotions icons, including plugins with this capability
     * @return array
     */
    public function get_emotions()
    {
        $rmc_config = RMSettings::cu_settings();

        if (!$rmc_config->dosmileys) {
            return false;
        }

        if (!empty($this->emots)) {
            return $this->emots;
        }

        // Only a few icons due to these can be extended by plugins or modules
        $url = RMCURL . '/images/emots';
        $this->emots[] = ['code' => [':-)'], 'icon' => $url . '/smile.png'];
        $this->emots[] = ['code' => [':-S', 'O.o'], 'icon' => $url . '/confused.png'];
        $this->emots[] = ['code' => [":'("], 'icon' => $url . '/cry.png'];
        $this->emots[] = ['code' => [':->'], 'icon' => $url . '/grin.png'];
        $this->emots[] = ['code' => [':-D'], 'icon' => $url . '/happy.png'];
        $this->emots[] = ['code' => [':-O', ':-o'], 'icon' => $url . '/surprised.png'];
        $this->emots[] = ['code' => [':-p', ':-P'], 'icon' => $url . '/tongue.png'];
        $this->emots[] = ['code' => [':-('], 'icon' => $url . '/unhappy.png'];
        $this->emots[] = ['code' => [';-)'], 'icon' => $url . '/wink.png'];
        $this->emots[] = ['code' => [':-|'], 'icon' => $url . '/neutral.png'];
        $this->emots[] = ['code' => ['8-)', 'B-)'], 'icon' => $url . '/cool.png'];
        $this->emots[] = ['code' => ['>:(', '>:-('], 'icon' => $url . '/mad.png'];
        $this->emots[] = ['code' => [':oops:'], 'icon' => $url . '/red.png'];
        $this->emots[] = ['code' => [':roll:'], 'icon' => $url . '/roll.png'];
        $this->emots[] = ['code' => ['X-D', 'x-D'], 'icon' => $url . '/yell.png'];

        // Get another emoticons from plugins or modules
        $this->emots = RMEvents::get()->run_event('rmcommon.get_emotions', $this->emots);

        return $this->emots;
    }

    /**
     * Replace emots codes for ther image
     * @param    string $message
     * @return    string
     */
    public function smiley($message)
    {
        $emots = $this->get_emotions();
        $codes = [];
        $icons = [];
        foreach ($emots as $v) {
            foreach ($v['code'] as $code) {
                $codes[] = $code;
                $icons[] = '<img src="' . $v['icon'] . '" alt="">';
            }
        }
        $message = str_replace($codes, $icons, $message);

        return $message;
    }

    /**
     * Make links in the text clickable
     *
     * @param   string $text
     * @param mixed $string
     * @return  string
     **/
    public function make_clickable($string)
    {
        $string = ' ' . $string;
        // in testing, using arrays here was found to be faster
        $string = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/\-=?@\[\](+]|[.,;:](?![\s<])|(?(1)\)(?![\s<])|\)))+)#is', 'url_clickable', $string);
        $string = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', 'ftp_clickable', $string);
        $string = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'mail_clickable', $string);
        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $string = preg_replace('#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', '$1$3</a>', $string);
        $string = trim($string);

        return $string;
    }

    public function nofollow($text)
    {
        global $wpdb;
        // This is a pre save filter, so text is already escaped.
        $text = stripslashes($text);
        $text = preg_replace_callback('|<a (.+?)>|i', 'rel_nofollow', $text);

        return $text;
    }

    public function popuplinks($text)
    {
        $text = preg_replace('/<a (.+?)>/i', "<a $1 target='_blank' rel='external'>", $text);

        return $text;
    }

    /**
     * Next set of three functions are a set of callbacks to manege url and uri matches
     *
     * @param array Matches
     * @param mixed $matches
     * @return string
     */
    public static function url_clickable($matches)
    {
        $url = $matches[2];
        $url = self::clean_url($url);
        if (empty($url)) {
            return $matches[0];
        }

        return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>";
    }

    public static function ftp_clickable($matches)
    {
        $ret = '';
        $dest = $matches[2];
        $dest = 'http://' . $dest;
        $dest = self::clean_url($dest);
        if (empty($dest)) {
            return $matches[0];
        }
        // removed trailing [,;:] from URL
        if (true === in_array(mb_substr($dest, -1), ['.', ',', ';', ':'], true)) {
            $ret = mb_substr($dest, -1);
            $dest = mb_substr($dest, 0, mb_strlen($dest) - 1);
        }

        return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
    }

    public static function mail_clickable($matches)
    {
        $email = $matches[2] . '@' . $matches[3];

        return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
    }

    /**
     * Checks and Clean a URL
     *
     * Taked from Wordpress
     *
     * @param string $url The URL to be cleaned.
     * @param array $protocols Optional. An array of acceptable protocols.
     *        Defaults to 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' if not set.
     * @param string $context Optional. How the URL will be used. Default is 'display'.
     * @return string The cleaned $url after the 'cleaned_url' filter is applied.
     */
    public function clean_url($url, $protocols = null, $context = 'display')
    {
        global $aprotocols;

        $original_url = $url;

        if ('' == $url) {
            return $url;
        }
        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
        $strip = ['%0d', '%0a', '%0D', '%0A'];
        $url = self::replace($strip, $url);
        $url = str_replace(';//', '://', $url);
        /* If the URL doesn't appear to contain a scheme, we
         * presume it needs http:// appended (unless a relative
         * link starting with / or a php file).
         */
        if (false === mb_strpos($url, ':') &&
            '/' != mb_substr($url, 0, 1) && '#' != mb_substr($url, 0, 1) && !preg_match('/^[a-z0-9-]+?\.php/i', $url)
        ) {
            $url = 'http://' . $url;
        }

        // Replace ampersands and single quotes only when displaying.
        if ('display' == $context) {
            $url = preg_replace('/&([^#])(?![a-z]{2,8};)/', '&#038;$1', $url);
            $url = str_replace("'", '&#039;', $url);
        }

        if (!is_array($protocols)) {
            $protocols = ['http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet'];
        }

        $aprotocols = $protocols;

        if (self::bad_protocol($url, $protocols) != $url) {
            return '';
        }

        return RMEvents::get()->run_event('rmcommon.clean_url', $url, $original_url, $context);
    }

    private function bad_protocol($string, $allowed_protocols)
    {
        $string = self::no_null($string);
        $string2 = $string . 'a';

        while ($string != $string2) {
            $string2 = $string;
            $string = self::bad_protocol_once($string, $allowed_protocols);
        } # while

        return $string;
    }

    private function bad_protocol_once($string, $allowed_protocols)
    {
        $string2 = preg_split('/:|&#58;|&#x3a;/i', $string, 2);
        if (isset($string2[1]) && !preg_match('%/\?%', $string2[0])) {
            $string = self::bad_protocol_once2($string2[0]) . trim($string2[1]);
        } else {
            $string = preg_replace_callback('/^((&[^;]*;|[\sA-Za-z0-9])*)' . '(:|&#58;|&#[Xx]3[Aa];)\s*/', 'bad_protocol_once2', $string);
        }

        return $string;
    }

    public static function bad_protocol_once2($matches)
    {
        global $aprotocols;
        $allowed_protocols = $aprotocols;

        if (is_array($matches)) {
            if (!isset($matches[1]) || empty($matches[1])) {
                return '';
            }

            $string = $matches[1];
        } else {
            $string = $matches;
        }

        $string2 = self::getInstance()->decode_entities($string);
        $string2 = preg_replace('/\s/', '', $string2);
        $string2 = self::getInstance()->no_null($string2);
        $string2 = mb_strtolower($string2);

        $allowed = false;
        foreach ((array)$allowed_protocols as $one_protocol) {
            if (mb_strtolower($one_protocol) == $string2) {
                $allowed = true;
                break;
            }
        }

        if ($allowed) {
            return "$string2:";
        }

        return '';
    }

    public function decode_entities($string)
    {
        $string = preg_replace_callback('/&#([0-9]+);/', 'decode_entities_chr', $string);
        $string = preg_replace_callback('/&#[Xx]([0-9A-Fa-f]+);/', 'decode_entities_chr_hexdec', $string);

        return $string;
    }

    public static function decode_entities_chr($match)
    {
        return chr($match[1]);
    }

    public static function decode_entities_chr_hexdec($match)
    {
        return chr(hexdec($match[1]));
    }

    public function no_null($string)
    {
        $string = preg_replace('/\0+/', '', $string);
        $string = preg_replace('/(\\\\0)+/', '', $string);

        return $string;
    }

    public function replace($search, $subject)
    {
        $found = true;
        while ($found) {
            $found = false;
            foreach ((array)$search as $val) {
                while (false !== mb_strpos($subject, $val)) {
                    $found = true;
                    $subject = str_replace($val, '', $subject);
                }
            }
        }

        return $subject;
    }

    /**
     * MyTextSanitizer::truncate()
     *
     * @param mixed $text
     * @param mixed $len
     * @param mixed $continue
     * @return
     */
    public static function truncate($text, $len, $continue = '[...]')
    {
        $text = preg_replace("[\n|\r|\n\r]", ' ', $text);
        $ret = mb_substr(strip_tags($text), 0, $len);

        if (mb_strlen($text) > $len) {
            $ret .= ' ' . $continue;
        }

        return $ret;
    }

    /**
     * Replace EXMCodes with their equivalent HTML formatting
     *
     * @param   string $text
     * @param   bool $allowimage Allow images in the text?
     *                              On FALSE, uses links to images.
     * @return  string
     **/
    public function codeDecode($text, $allowimage = 1)
    {
        $patterns = [];
        $replacements = [];
        //$patterns[] = "/\[code](.*)\[\/code\]/esU";
        //$replacements[] = "'<div class=\"exmCode\"><code><pre>'.wordwrap(MyTextSanitizer::htmlSpecialChars('\\1'), 100).'</pre></code></div>'";
        // RMV: added new markup for intrasite url (allows easier site moves)
        // TODO: automatically convert other URLs to this format if XOOPS_ROOT_PATH matches??
        $patterns['patterns'][] = "/\[siteurl=(['\"]?)([^\"'<>]*)\\1](.*)\[\/siteurl\]/sU";
        $patterns['replacements'][] = '<a href="' . XOOPS_ROOT_PATH . '/\\2">\\3</a>';
        $patterns['patterns'][] = "/\[url=(['\"]?)(http[s]?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $patterns['replacements'][] = '<a href="\\2" target="_blank">\\3</a>';
        $patterns['patterns'][] = "/\[url=(['\"]?)(ftp?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $patterns['replacements'][] = '<a href="\\2" target="_blank">\\3</a>';
        $patterns['patterns'][] = "/\[url=(['\"]?)([^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $patterns['replacements'][] = '<a href="http://\\2" target="_blank">\\3</a>';
        $patterns['patterns'][] = "/\[color=(['\"]?)([a-zA-Z0-9]*)\\1](.*)\[\/color\]/sU";
        $patterns['replacements'][] = '<span style="color: #\\2;">\\3</span>';
        $patterns['patterns'][] = "/\[size=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/size\]/sU";
        $patterns['replacements'][] = '<span style="font-size: \\2;">\\3</span>';
        $patterns['patterns'][] = "/\[font=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/font\]/sU";
        $patterns['replacements'][] = '<span style="font-family: \\2;">\\3</span>';
        $patterns['patterns'][] = "/\[email]([^;<>\*\(\)\"']*)\[\/email\]/sU";
        $patterns['replacements'][] = '<a href="mailto:\\1">\\1</a>';

        $patterns['patterns'][] = "/\[b](.*)\[\/b\]/sU";
        $patterns['replacements'][] = '<b>\\1</b>';
        $patterns['patterns'][] = "/\[i](.*)\[\/i\]/sU";
        $patterns['replacements'][] = '<i>\\1</i>';
        $patterns['patterns'][] = "/\[u](.*)\[\/u\]/sU";
        $patterns['replacements'][] = '<u>\\1</u>';
        $patterns['patterns'][] = "/\[d](.*)\[\/d\]/sU";
        $patterns['replacements'][] = '<del>\\1</del>';

        $patterns['patterns'][] = "/\[quote(=(.*)){0,1}\](.*)\[\/quote\]/";
        $patterns['replacements'][] = '<blockquote>$3<p class="citeby">$2</p></blockquote>';

        $patterns['patterns'][] = "/\[img align=(['\"]?)(left|center|right)\\1]([^\"\(\)'<>]*)\[\/img\]/sU";
        $patterns['patterns'][] = "/\[img]([^\"\(\)'<>]*)\[\/img\]/sU";
        $patterns['patterns'][] = "/\[img align=(['\"]?)(left|center|right)\\1 id=(['\"]?)([0-9]*)\\3]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";
        $patterns['patterns'][] = "/\[img id=(['\"]?)([0-9]*)\\1]([^\"\(\)\?\&'<>]*)\[\/img\]/sU";

        if (1 != $allowimage) {
            $patterns['replacements'][] = '<a href="\\3" target="_blank">\\3</a>';
            $patterns['replacements'][] = '<a href="\\1" target="_blank">\\1</a>';
            $patterns['replacements'][] = '<a href="' . XOOPS_ROOT_PATH . '/image.php?id=\\4" target="_blank">\\4</a>';
            $patterns['replacements'][] = '<a href="' . XOOPS_ROOT_PATH . '/image.php?id=\\2" target="_blank">\\3</a>';
        } else {
            $patterns['replacements'][] = '<img src="\\3" align="\\2" alt="">';
            $patterns['replacements'][] = '<img src="\\1" alt="">';
            $patterns['replacements'][] = '<img src="' . XOOPS_ROOT_PATH . '/image.php?id=\\4" align="\\2" alt="\\4">';
            $patterns['replacements'][] = '<img src="' . XOOPS_ROOT_PATH . '/image.php?id=\\2" alt="\\3">';
        }

        $text = str_replace("\x00", '', $text);
        $c = "[\x01-\x1f]*";
        $patterns['patterns'][] = "/j{$c}a{$c}v{$c}a{$c}s{$c}c{$c}r{$c}i{$c}p{$c}t{$c}:/si";
        $patterns['replacements'][] = '(script removed)';
        $patterns['patterns'][] = "/a{$c}b{$c}o{$c}u{$c}t{$c}:/si";
        $patterns['replacements'][] = 'about :';

        // More patterns with plugins
        $patterns = RMEvents::get()->run_event('rmcommon.get.replace.patterns', $patterns, $this);

        $text = preg_replace($patterns['patterns'], $patterns['replacements'], $text);

        $text = RMEvents::get()->run_event('rmcommon.code.decode', $text);

        return $text;
    }

    /**
     * Convert linebreaks to <br> tags
     *
     * @param    string $text
     *
     * @return    string
     */
    public function nl2Br($text)
    {
        return preg_replace("/(\015\012)|(\015)|(\012)/", '<br>', $text);
    }

    /**
     * Add slashes to the text if magic_quotes_gpc is turned off.
     *
     * @param   string $text
     * @return  string
     **/
    public function addslashes($text)
    {
        if (get_magic_quotes_gpc()) {
            $text = stripslashes($text);
        }

        return addslashes($text);
    }

    /*
    * if magic_quotes_gpc is on, stirip back slashes
    *
    * @param	string  $text
    *
    * @return	string
    */
    public static function stripslashes($text)
    {
        if (get_magic_quotes_gpc()) {
            $text = stripslashes($text);
        }

        return $text;
    }

    /*
    *  for displaying data in html textbox forms
    *
    * @param	string  $text
    *
    * @return	string
    */
    public function specialchars($string, $quote_style = ENT_NOQUOTES, $charset = 'UTF-8')
    {
        if (0 === mb_strlen($string)) {
            return '';
        }

        if (!preg_match('/[&<>"\']/', $string)) {
            return $string;
        }

        if (empty($quote_style)) {
            $quote_style = ENT_NOQUOTES;
        } elseif (!in_array($quote_style, [0, 2, 3, 'single', 'double'], true)) {
            $quote_style = ENT_QUOTES;
        }

        if (in_array($charset, ['utf8', 'utf-8', 'UTF8'], true)) {
            $charset = 'UTF-8';
        }

        if ('double' === $quote_style) {
            $quote_style = ENT_COMPAT;
            $_quote_style = ENT_COMPAT;
        } elseif ('single' === $quote_style) {
            $quote_style = ENT_NOQUOTES;
        }

        $string = @htmlspecialchars($string, $quote_style, $charset);

        //return preg_replace("/&amp;/i", '&', htmlspecialchars($text, ENT_QUOTES));
        return preg_replace(['/&amp;/i', '/&nbsp;/i'], ['&', '&amp;nbsp;'], htmlspecialchars($string, ENT_QUOTES));
    }

    /**
     * Reverses {@link htmlSpecialChars()}
     *
     * @param   string $text
     * @param mixed $string
     * @param mixed $quote_style
     * @return  string
     **/
    public function specialchars_decode($string, $quote_style = ENT_NOQUOTES)
    {
        if (0 === mb_strlen($string)) {
            return '';
        }

        // Don't bother if there are no entities - saves a lot of processing
        if (false === mb_strpos($string, '&')) {
            return $string;
        }

        if (empty($quote_style)) {
            $quote_style = ENT_NOQUOTES;
        } elseif (!in_array($quote_style, [0, 2, 3, 'single', 'double'], true)) {
            $quote_style = ENT_QUOTES;
        }

        // More complete than get_html_translation_table( HTML_SPECIALCHARS )
        $single = ['&#039;' => '\'', '&#x27;' => '\''];
        $single_preg = ['/&#0*39;/' => '&#039;', '/&#x0*27;/i' => '&#x27;'];
        $double = ['&quot;' => '"', '&#034;' => '"', '&#x22;' => '"'];
        $double_preg = ['/&#0*34;/' => '&#034;', '/&#x0*22;/i' => '&#x22;'];
        $others = ['&lt;' => '<', '&#060;' => '<', '&gt;' => '>', '&#062;' => '>', '&amp;' => '&', '&#038;' => '&', '&#x26;' => '&'];
        $others_preg = ['/&#0*60;/' => '&#060;', '/&#0*62;/' => '&#062;', '/&#0*38;/' => '&#038;', '/&#x0*26;/i' => '&#x26;'];

        if (ENT_QUOTES === $quote_style) {
            $translation = array_merge($single, $double, $others);
            $translation_preg = array_merge($single_preg, $double_preg, $others_preg);
        } elseif (ENT_COMPAT === $quote_style || 'double' === $quote_style) {
            $translation = array_merge($double, $others);
            $translation_preg = array_merge($double_preg, $others_preg);
        } elseif ('single' === $quote_style) {
            $translation = array_merge($single, $others);
            $translation_preg = array_merge($single_preg, $others_preg);
        } elseif (ENT_NOQUOTES === $quote_style) {
            $translation = $others;
            $translation_preg = $others_preg;
        }

        $string = preg_replace(array_keys($translation_preg), array_values($translation_preg), $string);

        return strtr($string, $translation);
    }

    /**
     * Replaces double line-breaks with paragraph elements.
     *
     * A group of regex replaces used to identify text formatted with newlines and
     * replace double line-breaks with HTML paragraph tags. The remaining
     * line-breaks after conversion become <<br>> tags, unless $br is set to '0'
     * or 'false'.
     *
     * Taked from Wordpress
     *
     * @license GPL 2
     * @param string $string The text which has to be formatted.
     * @param int|bool $br Optional. If set, this will convert all remaining line-breaks after paragraphing. Default true.
     * @return string Text which has been converted into correct paragraph tags.
     */
    public function double_br($string, $br = 1)
    {
        if ('' === trim($string)) {
            return '';
        }
        $string = $string . "\n"; // just to make things a little easier, pad the end
        $string = preg_replace('|<br>\s*<br>|', "\n\n", $string);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
        $string = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $string);
        $string = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $string);
        $string = str_replace(["\r\n", "\r"], "\n", $string); // cross-platform newlines
        if (false !== mb_strpos($string, '<object')) {
            $string = preg_replace('|\s*<param([^>]*)>\s*|', '<param$1>', $string); // no pee inside object/embed
            $string = preg_replace('|\s*</embed>\s*|', '</embed>', $string);
        }
        $string = preg_replace("/\n\n+/", "\n\n", $string); // take care of duplicates
        // make paragraphs, including one at the end
        $strings = preg_split('/\n\s*\n/', $string, -1, PREG_SPLIT_NO_EMPTY);
        $string = '';
        foreach ($strings as $tinkle) {
            $string .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }
        $string = preg_replace('|<p>\s*</p>|', '', $string); // under certain strange conditions it could create a P of entirely whitespace
        $string = preg_replace('!<p>([^<]+)</(div|address|form)>!', '<p>$1</p></$2>', $string);
        $string = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $string); // don't pee all over a tag
        $string = preg_replace('|<p>(<li.+?)</p>|', '$1', $string); // problem with nested lists
        $string = preg_replace('|<p><blockquote([^>]*)>|i', '<blockquote$1><p>', $string);
        $string = str_replace('</blockquote></p>', '</p></blockquote>', $string);
        $string = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', '$1', $string);
        $string = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $string);
        if ($br) {
            $string = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<EXMPreserveNewline>", $matches[0]);'), $string);
            $string = preg_replace('|(?<!<br>)\s*\n|', "<br>\n", $string); // optionally make line breaks
            $string = str_replace('<EXMPreserveNewline>', "\n", $string);
        }
        $string = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br>!', '$1', $string);
        $string = preg_replace('!<br>(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $string);
        if (false !== mb_strpos($string, '<pre')) {
            $string = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', 'TextCleaner::clean_pre', $string);
        }
        //$string = preg_replace( "|\n</p>$|", '</p>', $string );
        //$string = preg_replace('/<p>\s*?(' . get_shortcode_regex() . ')\s*<\/p>/s', '$1', $string); // don't auto-p wrap shortcodes that stand alone

        return $string;
    }

    /**
     * Filters textarea form data in DB for display
     *
     * based on Xoops displayTarea
     *
     * @param   string $text
     * @param   bool $dbr Disbale replace of breaklines when html is enabled
     * @param bool Clean disabled tags?
     * @param mixed $clean_tags
     * @param mixed $paragraph
     * @return  string
     **/
    public function to_display($text, $dbr = true, $clean_tags = true, $paragraph = true)
    {
        $rmc_config = RMSettings::cu_settings();

        $original_text = $text;

        // Custom Codes
        global $rmCodes;

        // Markdown
        if ($rmc_config->markdown) {
            $md = new RMParsedown();
            $text = $md->text($text);
        }

        if (!defined('XOOPS_CPFUNC_LOADED') && !defined('NO_CUSTOM_CODES')) {
            $text = $rmCodes->doCode($text);
        }

        if (1 != $rmc_config->dohtml) {
            $text = $this->specialchars($text);
        }

        // Convert [code] tag
        $text = $this->codePreConv($text, $rmc_config->doxcode && !defined('XOOPS_CPFUNC_LOADED'));

        if ($rmc_config->dosmileys && !defined('XOOPS_CPFUNC_LOADED')) {
            $text = $this->smiley($text);
        }

        // Decode exmcode
        if ($rmc_config->doxcode && !defined('XOOPS_CPFUNC_LOADED')) {
            $text = $this->codeDecode($text, $rmc_config->doimage);
        }

        // Replace breaklines
        if ($rmc_config->dobr) {
            $text = $this->nl2Br($text);
        }

        if ($clean_tags) {
            $text = $this->clean_disabled_tags($text);
        }
        $text = $this->make_clickable($text);
        //$text = $this->codeConv($text, $rmc_config->doxcode);	// Ryuji_edit(2003-11-18)
        if ($paragraph) {
            $text = $this->double_br($text);
        }

        // Before to send the formatted string we send it to interceptor methods
        return RMEvents::get()->run_event('rmcommon.text.todisplay', $text, $original_text);
    }

    public function clean_disabled_tags($text)
    {
        $this->disable_tags = RMEvents::get()->run_event('rmcommon.more.disabled.tags', $this->disable_tags);

        $text = preg_replace_callback($this->disable_tags, 'preg_striptags', $text);

        return $text;
    }

    /**
     * Sanitizing of [code] tag
     * from Xoops
     * @param mixed $text
     */
    public function codePreConv($text)
    {
        $patterns = "/\[code([^\]]*?)\](.*)\[\/code\]/sU";
        /*$replacements = "'[code$1]'.base64_encode('$2').'[/code]'";
        $text =  preg_replace($patterns, $replacements, $text);*/

        $text = preg_replace_callback($patterns, function ($m) {
            return '[code' . $m[1] . ']' . base64_encode($m[2]) . '[/code]';
        }, $text);

        return $text;
    }

    public function codeConv($text, $exmcode = 1)
    {
        if (0 == $exmcode) {
            return $text;
        }

        $patterns = "/\[code([^\]]*?)\](.*)\[\/code\]/sU";
        /*$replacements = "'<div class=\"xoopsCode\"><code>'.\$this->call_code_modifiers(\$this->specialchars(str_replace('\\\"', '\"', base64_decode('$2'))), '$1').'</code></div>'";
        $text =  preg_replace($patterns, $replacements, $text);*/

        $text = preg_replace_callback($patterns, function ($m) {
            return '<div class="xoopsCode"><code>' . $this->call_code_modifiers($this->specialchars(str_replace('"', '"', base64_decode($m[2], true))), $m[1]) . '</code></div>';
        }, $text);

        return $text;
    }

    /**
     * This function is only a holder for event exmevent_format_code
     *
     * @param string Code to format
     * @param string Language name
     * @param mixed $code
     * @param mixed $lang
     * @return srtring
     */
    public function call_code_modifiers($code, $lang)
    {
        // Event to call methods thatr can handle code to display (eg. highlighters)
        return RMEvents::get()->run_event('rmcommon.format.code', $code, $lang);
    }

    /**
     * Accepts matches array from preg_replace_callback in wpautop() or a string.
     *
     * Ensures that the contents of a <<pre>>...<</pre>> HTML block are not
     * converted into paragraphs or line-breaks.
     *
     * From Wordpress
     *
     * @param array|string $matches The array or string
     * @return string The pre block without paragraph/line-break conversion.
     */
    public function clean_pre($matches)
    {
        if (is_array($matches)) {
            $text = $matches[1] . $matches[2] . '</pre>';
        } else {
            $text = $matches;
        }

        $text = str_replace('<br>', '', $text);
        $text = str_replace('<p>', "\n", $text);
        $text = str_replace('</p>', '', $text);

        return $text;
    }

    /**
     * Encrypt a string
     *
     * @param string Text to crypt
     * @param bool Apply base64_encode?
     * @param mixed $string
     * @param mixed $encode64
     * @return string
     */
    public function encrypt($string, $encode64 = true)
    {
        $rmc_config = RMSettings::cu_settings();
        $crypt = new Crypt();
        $string = $crypt->encrypt($string);
        //if ($encode64) $string = base64_encode($string);
        return $string;
    }

    /**
     * Decrypt a string
     *
     * @param strign Text to decrypt
     * @param bool Apply base64_decode? default true
     * @param mixed $string
     * @param mixed $encode64
     */
    public static function decrypt($string, $encode64 = true)
    {
        $rmc_config = RMSettings::cu_settings();

        $crypt = new Crypt();
        $string = $crypt->decrypt($string);

        return $string;
    }

    /**
     * Clean a string by deleting all blank spaces and other
     * chars
     * @param mixed $value
     * @param mixed $lower
     */
    public static function sweetstring($value, $lower = true)
    {
        // Tranformamos todo a minusculas
        $rtn = $lower ? mb_strtolower(mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8')) : $value;

        //Rememplazamos caracteres especiales latinos
        $find = ['á', 'é', 'í', 'ó', 'ú', 'ñ'];
        $repl = ['a', 'e', 'i', 'o', 'u', 'n'];
        $rtn = str_replace($find, $repl, mb_convert_encoding($rtn, 'UTF-8', 'ISO-8859-1'));

        // Añadimos los guiones
        $find = [' ', '&', '\r\n', '\n', '+'];
        $rtn = str_replace($find, '-', $rtn);

        // Eliminamos y Reemplazamos demás caracteres especiales
        $find = ['/[^a-zA-Z0-9\-<>_]/', '/[\-]+/', '/<[^>]*>/'];
        $repl = ['', '-', ''];
        $url = preg_replace($find, $repl, $rtn);

        return $url;
    }
}

// For compatibility
function url_clickable($matches)
{
    return TextCleaner::url_clickable($matches);
}

function ftp_clickable($matches)
{
    return TextCleaner::ftp_clickable($matches);
}

function mail_clickable($matches)
{
    return TextCleaner::mail_clickable($matches);
}

function bad_protocol_once2($matches)
{
    return TextCleaner::bad_protocol_once2($matches);
}

function decode_entities_chr($matches)
{
    return TextCleaner::decode_entities_chr($matches);
}

function decode_entities_chr_hexdec($matches)
{
    return TextCleaner::decode_entities_chr_hexdec($matches);
}

function rel_nofollow($matches)
{
    $text = $matches[1];
    $text = str_replace([' rel="nofollow"', " rel='nofollow'"], '', $text);

    return "<a $text rel=\"nofollow\">";
}

function preg_striptags($match)
{
    //return TextCleaner::getInstance()->specialchars($match);
    $ret = '';
    if (is_array($match)) {
        foreach ($match as $i => $t) {
            $ret .= TextCleaner::getInstance()->specialchars($t);
        }
    } else {
        $match = TextCleaner::getInstance()->specialchars($match);
    }

    return $ret;
}
