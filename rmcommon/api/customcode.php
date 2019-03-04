<?php

/**
 * This code is a modified fragment taken from Wordpress.
 * The idea is to provide the functionallity of shortcodes
 * directly on XOOPS using rmcommon
 */
class RMCustomCode
{
    /**
     * Contains all custom codes registered
     */
    private $custom_codes = [];

    /**
     * @return RMCustomCode
     */
    public static function get()
    {
        static $instance;

        if (isset($instance)) {
            return $instance;
        }

        $instance = new self();

        return $instance;
    }

    /**
     * Add hook for customcode tag.
     *
     * There can only be one hook for each customcode. Which means that if another
     * plugin has a similar customcode, it will override yours or yours will override
     * theirs depending on which order the plugins are included and/or ran.
     *
     * Simplest example of a customcode tag using the API:
     *
     * <code>
     * // [footag foo="bar"]
     * function footag_func($atts) {
     *    return "foo = {$atts[foo]}";
     * }
     * add_customcode('footag', 'footag_func');
     * </code>
     *
     * Example with nice attribute defaults:
     *
     * <code>
     * // [bartag foo="bar"]
     * function bartag_func($atts) {
     *    extract(customcode_atts(array(
     *        'foo' => 'no foo',
     *        'baz' => 'default baz',
     *    ), $atts));
     *
     *    return "foo = {$foo}";
     * }
     * add_customcode('bartag', 'bartag_func');
     * </code>
     *
     * Example with enclosed content:
     *
     * <code>
     * // [baztag]content[/baztag]
     * function baztag_func($atts, $content='') {
     *    return "content = $content";
     * }
     * add_customcode('baztag', 'baztag_func');
     * </code>
     *
     * @since 2.5
     * @uses $customcode_tags
     *
     * @param string $tag customcode tag to be searched in post content.
     * @param callable $func Hook to run when customcode is found.
     */
    public function add($tag, $func)
    {
        if (is_callable($func)) {
            $this->custom_codes[$tag] = $func;
        }
    }

    /**
     * Removes hook for customcode.
     *
     * @since 2.5
     * @uses $customcode_tags
     *
     * @param string $tag customcode tag to remove hook for.
     */
    public function remove($tag)
    {
        unset($this->custom_codes[$tag]);
    }

    /**
     * Clear all customcodes.
     *
     * This function is simple, it clears all of the customcode tags by replacing the
     * customcodes global by a empty array. This is actually a very efficient method
     * for removing all customcodes.
     *
     * @since 2.5
     * @uses $customcode_tags
     */
    public function removeAll()
    {
        $this->custom_codes = [];
    }

    /**
     * Search content for customcodes and filter customcodes through their hooks.
     *
     * If there are no customcode tags defined, then the content will be returned
     * without any filtering. This might cause issues when plugins are disabled but
     * the customcode will still show up in the post or content.
     *
     * @since 2.5
     * @uses $customcode_tags
     * @uses get_customcode_regex() Gets the search pattern for searching customcodes.
     *
     * @param string $content Content to search for customcodes
     * @return string Content with customcodes filtered out.
     */
    public function doCode($content)
    {
        if (empty($this->custom_codes) || !is_array($this->custom_codes)) {
            return $content;
        }
        $pattern = $this->getRegex();

        return preg_replace_callback("/$pattern/s", [$this, 'doTag'], $content);
    }

    /**
     * Retrieve the customcode regular expression for searching.
     *
     * The regular expression combines the customcode tags in the regular expression
     * in a regex class.
     *
     * The regular expression contains 6 different sub matches to help with parsing.
     *
     * 1 - An extra [ to allow for escaping customcodes with double [[]]
     * 2 - The customcode name
     * 3 - The customcode argument list
     * 4 - The self closing /
     * 5 - The content of a customcode when it wraps some content.
     * 6 - An extra ] to allow for escaping customcodes with double [[]]
     *
     * @since 2.5
     * @uses $customcode_tags
     *
     * @return string The customcode search regular expression
     */
    public function getRegex()
    {
        $tagnames = array_keys($this->custom_codes);
        $tagregexp = implode('|', array_map('preg_quote', $tagnames));

        // WARNING! Do not change this regex without changing do_customcode_tag() and strip_customcode_tag()
        // Also, see customcode_unautop() and customcode.js.
        return
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping customcodes: [[tag]]
            . "($tagregexp)"                     // 2: customcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening customcode tag
            . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            . '(?:'
            . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            . '[^\\]\\/]*'               // Not a closing bracket or forward slash
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'                        // 4: Self closing tag ...
            . '\\]'                          // ... and closing bracket
            . '|'
            . '\\]'                          // Closing bracket
            . '(?:'
            . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing customcode tags
            . '[^\\[]*+'             // Not an opening bracket
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing customcode tag
            . '[^\\[]*+'         // Not an opening bracket
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'             // Closing customcode tag
            . ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping customcodes: [[tag]]
    }

    /**
     * Regular Expression callable for do_customcode() for calling customcode hook.
     * @see  get_customcode_regex for details of the match array contents.
     *
     * @since 2.5
     * @access private
     * @uses $customcode_tags
     *
     * @param array $m Regular expression match array
     * @return mixed False on failure.
     */
    public function doTag($m)
    {
        // allow [[foo]] syntax for escaping a tag
        if ('[' == $m[1] && ']' == $m[6]) {
            return mb_substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = $this->parseAtts($m[3]);

        if (isset($m[5])) {
            // enclosing tag - extra parameter
            return $m[1] . call_user_func($this->custom_codes[$tag], $attr, $m[5], $tag) . $m[6];
        }
        // self-closing tag
        return $m[1] . call_user_func($this->custom_codes[$tag], $attr, null, $tag) . $m[6];
    }

    /**
     * Retrieve all attributes from the customcodes tag.
     *
     * The attributes list has the attribute name as the key and the value of the
     * attribute as the value in the key/value pair. This allows for easier
     * retrieval of the attributes, since all attributes have to be known.
     *
     * @since 2.5
     *
     * @param string $text
     * @return array List of attributes and their value.
     */
    public function parseAtts($text)
    {
        $atts = [];
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $atts[mb_strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $atts[mb_strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $atts[mb_strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) and mb_strlen($m[7])) {
                    $atts[] = stripcslashes($m[7]);
                } elseif (isset($m[8])) {
                    $atts[] = stripcslashes($m[8]);
                }
            }
        } else {
            $atts = ltrim($text);
        }

        return $atts;
    }

    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     *
     * The pairs should be considered to be all of the attributes which are
     * supported by the caller and given as a list. The returned attributes will
     * only contain the attributes in the $pairs list.
     *
     * If the $atts list has unsupported attributes, then they will be ignored and
     * removed from the final returned list.
     *
     * @since 2.5
     *
     * @param array $pairs Entire list of supported attributes and their defaults.
     * @param array $atts User defined attributes in customcode tag.
     * @return array Combined and filtered attribute list.
     */
    public function atts($pairs, $atts)
    {
        $atts = (array)$atts;
        $out = [];
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }

        return $out;
    }

    /**
     * Remove all customcode tags from the given content.
     *
     * @since 2.5
     * @uses $customcode_tags
     *
     * @param string $content Content to remove customcode tags.
     * @return string Content without customcode tags.
     */
    public function strip($content)
    {
        if (empty($this->custom_codes) || !is_array($this->custom_codes)) {
            return $content;
        }

        $pattern = $this->getRegex();

        return preg_replace_callback("/$pattern/s", [$this, 'strip'], $content);
    }

    public function stripTag($m)
    {
        // allow [[foo]] syntax for escaping a tag
        if ('[' == $m[1] && ']' == $m[6]) {
            return mb_substr($m[0], 1, -1);
        }

        return $m[1] . $m[6];
    }
}
