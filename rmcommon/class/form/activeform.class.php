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

/**
 * Class RMActiveForm
 */
class RMActiveForm
{
    use RMProperties;

    private $model;

    /**
     * Class constructor for the &lt;form&gt; tag. This method accept a list of attributes and parameters that
     * could be inserted in form.
     *
     * The attributes could be any of html form tag accepted attributes like method, action, etc. The attributes
     * must be passed as an associative array:
     *
     * <pre>$form = new RMActiveForm(array(
     *      'action'            => 'http://...',
     *      'method'            => 'post',
     *      'data-something'    => 'something',
     *      ...
     * ));</pre>
     * <h3>Predefined attributes</h3>
     * There exists a list of predefined attributes for this class. Next is a list of them:
     * <ul>
     * <li>
     *      <strong>action</strong>. [string] Default: current URL
     * </li>
     * <li>
     *      <strong>method</strong>. [string:get|post] Default: post
     * </li>
     * </ul>
     * Additionally, there exists a list of parameters that can be passed in order to add other functionality to the
     * form:
     *
     * <ul>
     *      <li>
     *          <strong>id</strong>. <em>string</em> [optional]. A string with the id used in from tag. This id must
     *          not contain spaces or invalid chars.
     *      </li>
     *      <li>
     *          <strong>submit-via</strong>. <em>string</em> [optional:ajax|normal] Default: normal. A key with
     *          validation method type, that could be <em>ajax</em> or <em>normal</em>.
     *      </li>
     *      <li>
     *          <strong>validation</strong>. <em>string</em> [optional:local|remote] Default: local. Determines if the
     *          form data is checked locally or remotely.
     *      </li>
     * </ul>
     *
     * @param array $attributes <p>Array of attributes to be generated with/in the form</p>
     */
    public function __construct($attributes = [])
    {
        // Default 'method' is post
        if (!isset($attributes['method'])) {
            $attributes['method'] = 'post';
        }

        // Default 'action' is the current URL
        if (!isset($attributes['action'])) {
            $attributes['action'] = RMUris::current_url();
        }

        // Default submission method is normal
        if (!isset($attributes['submit-via'])) {
            $attributes['submit-via'] = 'normal';
        }

        // Default validation is in local client
        if (!isset($attributes['validation'])) {
            $attributes['validation'] = 'local';
        }

        if (array_key_exists('model', $attributes) && is_a($attributes['model'], 'RMActiveRecord')) {
            $this->model = $attributes['model'];
            unset($attributes['model']);
        }

        $this->attributes = $attributes;
    }

    public function __call($object, $arguments)
    {
        if (count($arguments) <= 0) {
            throw new RMException(sprintf(__('Form element "%s" must be called providing required parameters.', 'rmcommon'), $object));
        }

        $model = $arguments[0];
        $field = $arguments[1];
        $parameters = isset($arguments[2]) ? $arguments[2] : [];

        $file = __DIR__ . '/form-objects/' . mb_strtolower($object) . '.php';

        if (!file_exists($file)) {
            throw new RMException(sprintf(__('Form element of type "%s" does not exists.', 'rmcommon'), $object));
        }

        $class = 'Active' . ucfirst($object);

        if (!class_exists($class)) {
            include_once($file);
        }

        if (!class_exists($class)) {
            throw new RMException(sprintf(__('The form element "%s" is not valid.', 'rmcommon'), $object));
        }

        $element = new $class($model, $field, $parameters);
        $element->open();
    }

    /**
     * Open the &lt;form&gt; tag with all specified parameters.
     */
    public function open()
    {
        $form = '<form ';
        $class = 'active-form';

        RMTemplate::get()->add_script(
            'forms/active-form.js',
            'rmcommon',
            [
                'location' => 'footer',
            ]
        );

        RMTemplate::get()->add_style(
            'active-form.css',
            'rmcommon',
            [
                'location' => 'footer',
            ]
        );

        foreach ($this->attributes as $attr => $value) {
            switch ($attr) {
                case 'submit-via':
                    $form .= 'ajax' == $value ? ' data-type="ajax"' : '';
                    break;
                case 'validation':
                    $class .= 'local' == $value ? ' validate-form' : '';
                    RMTemplate::getInstance()->add_script('jquery.validate.min.js', 'rmcommon', ['directory' => 'include', 'location' => 'footer', 'id' => 'validate-js']);
                    break;
                case 'class':
                    $class .= ' ' . $value;
                    break;
                default:
                    $form .= $attr . '="' . $value . '"';
                    break;
            }
        }

        $form .= ' class="' . $class . '">';

        echo $form;
    }

    /**
     * Close the &lt;form&gt; tag
     */
    public function close()
    {
        echo '</form>';
    }
}
