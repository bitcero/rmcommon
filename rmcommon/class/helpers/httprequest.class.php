<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Common Utilities
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: GPL 2.0
 * URI: http://www.redmexico.com.mx
 */
class RMHttpRequest
{
    use RMSingleton;

    public static function request($key, $type, $default = '')
    {
        return self::get_http_parameter('request', $key, $type, $default);
    }

    public static function get($key, $type = 'string', $default = '')
    {
        return self::get_http_parameter('get', $key, $type, $default);
    }

    public static function post($key, $type, $default = '')
    {
        return self::get_http_parameter('post', $key, $type, $default);
    }

    public static function put($key, $type, $default = '')
    {
        return self::get_http_parameter('put', $key, $type, $default);
    }

    public static function delete($key, $type, $default = '')
    {
        return self::get_http_parameter('delete', $key, $type, $default);
    }

    /**
     * Permite obtener un valor de un array y filtrarlo para un manejo seguro
     * @param mixed $key
     * @param mixed $haystack
     * @param mixed $type
     * @param mixed $default
     */
    public static function array_value($key, $haystack, $type, $default = '')
    {
        if (!is_array($haystack)) {
            return $default;
        }

        if (!isset($haystack[$key])) {
            return $default;
        }

        return self::clean_value($haystack[$key], $type);
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Gets the URL parameters passed trough _GET, _POST, _REQUEST methods
     *
     * The key must be a valid parameter name.
     * Type must be a valid data type, e.g. boolean, integer, float, string, number or array
     *
     * @param string $method Method to use (e.g. get, post, request)
     * @param string $key Key name to get
     * @param string $type Value type of the param to get
     * @param mixed $default Value to use when param is not found
     * @return mixed
     */
    protected static function get_http_parameter($method, $key, $type, $default = '')
    {
        if ('' == $key) {
            return null;
        }

        $method = mb_strtolower($method);

        if (!in_array($method, [ 'get', 'post', 'request', 'put', 'delete' ], true)) {
            return null;
        }

        if ('' == $type) {
            trigger_error(__('Get values from URL parameters without specify a valid type, can result in security issues. Please consider to specify a type before to get URL params.', 'rmcommon'), E_WARNING);
        }

        switch ($method) {
            case 'get':
                $_DATA = &$_GET;
                break;
            case 'post':
                $_DATA = &$_POST;
                break;
            case 'request':
                $_DATA = &$_REQUEST;
                break;
            case 'put':
            case 'delete':
                parse_str(file_get_contents('php://input'));
                if (isset(${$key})) {
                    return self::clean_value(${$key}, $type);
                }

                    return self::clean_value($default, $type);
                break;
        }

        if (isset($_DATA[$key])) {
            return self::clean_value($_DATA[$key], $type);
        }

        return self::clean_value($default, $type);
    }

    /**
     * Converts a value to the correct type
     * @param $value
     * @param $type
     * @return array|bool|float|int|string
     */
    public static function clean_value($value, $type)
    {
        $return = null;

        switch ($type) {
            case 'bool':
                $return = (bool) $value;
                break;
            case 'integer':
                $return = (int) $value;
                break;
            case 'float':
                $return = (float)$value;
                break;
            case 'number':
                $return = is_float($value) ? (float)$value : (int)$value;
                break;
            case 'string':
                $return = trim((string)$value);
                break;
            case 'array':
                $return = is_array($value) ? $value : (array) $value;
                break;
            default:
                $return = $value;
                break;
        }

        return $return;
    }

    public static function uri_parameters()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Gets values from a collection of parameters passes trough post, get or request method.
     *
     * <strong>How to use:</strong>
     *
     * <pre> $data = {@link RMHttpRequest}::collect_data( "post", array(
     *     "name" => "string",
     *     "qty"  => "integer"
     * ) );</pre>
     *
     * This function must extract two parameters from $_POST var: name and qty. Note that we have
     * specified the type of value for these two parameters: string and integer. You can get all
     * required values by specifying a list of name/value pairs in the array:
     *
     * <pre>array( 'var_name' => 'value_type[string, integer, array, float]' );</pre>
     *
     * You can use the obtained data as follow:
     * <pre>echo $data->name;</pre>
     *
     * @param string $source <p>Source for data. Can be 'post', 'get' or 'request'.</p>
     * @param array $parameters <p>List of parameters that will be loaded from any of previous three methods.</p>
     * @return stdClass
     */
    public static function collect_data($source = 'post', $parameters = [])
    {
        if (empty($parameters)) {
            return false;
        }

        $collected = new stdClass();

        foreach ($parameters as $var => $type) {
            if ('post' == $source) {
                $collected->$var = self::post($var, $type, '');
            } elseif ('request' == $source) {
                $collected->$var = self::request($var, $type, '');
            } else {
                $collected->$var = self::get($var, $type, '');
            }
        }

        return $collected;
    }

    /**
     * Get content from URL
     * @param        string $url
     * @param string string|array $query
     * @param bool   bool $post
     *
     * @return bool|mixed|string
     */
    public static function load_url($url, $query = '', $post = false)
    {
        // Form the query
        if (is_array($query)) {
            $query = http_build_query($query);
        }

        if ($post) {
            if ('' == $query) {
                $query = explode('?', $url);
                $url = $query[0];
                $query = $query[1];
            }

            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => $query,
                ],
            ];
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            return $result;
        }

        if ('' != $query) {
            $url .= '?' . $query;
        }

        return file_get_contents($url);
    }
}
