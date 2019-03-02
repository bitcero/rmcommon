<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Schooler Pro
 * Description: A module for management of scores and notes for students
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: Private
 * URI: http://www.redmexico.com.mx
 * --------------------------------------------------------------
 */

/**
 * Class to include a &lt;select&gt; tag in a {@link RMActiveForm} form.
 *
 * <p>You must pass as parameters the HTML attributes of the tag, but it is neccesary to pass another parameters
 * additionally. Next are the available parameters:</p>
 * <ol>
 *  <li>All <strong>HTML5</strong> tag attributes, like autofocus, disabled, form, multiple, required, size, etc.</li>
 *  <li>All applicable <strong>HTML5</strong> global attributes, like accesskey, <em>class</em>, lang, etc.<br>A
 *  special case are the <em><strong>id</strong></em> and <em><strong>name</strong></em> attributes, that will be
 *  ignored because are auto-generated from the field name.</li>
 * </ol>
 * <h3>Reserved attributes and parameters:</h3>
 * <p>Following is the list of special attributes ans parameters tha will be used with this tag. Note that some of those
 * attributes/parameters could be replaced to previously provided parameters.</p>
 *
 * <ol>
 *  <li><strong>id</strong> and <strong>name</strong>. Both parameters are generated according to database table field.</li>
 *  <li><strong>value</strong>. The default selected option for select tag.</li>
 *  <li><strong>options</strong>. An array with options to be inserted in select tag.</li>
 * </ol>
 *
 * Class ActiveSelect
 */
class ActiveSelect
{
    use RMFormComponent;

    public function render()
    {
        $input = '<select name="' . $this->name . '" id="' . $this->id . '"';
        $class = $this->required ? 'required' : '';
        $selected = '';
        $options = array();
        $default_option = '';

        $parameters = $this->parameters;

        foreach ($parameters as $attr => $value) {
            if ('class' == $attr) {
                $class .= $class != '' ? ' '.$value : $value;
            } elseif ('value' == $attr) {
                $selected = $value;
            } elseif ('options' == $attr) {
                $options = $value;
            } elseif ('default' == $attr) {
                $default_option = $value;
            } else {
                $input .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $input .= '' != $class ? ' class="' . $class . '"' : '';
        $input .= $this->required ? ' required' : '';
        $input .= '>';

        if ($default_option != '') {
            $input .= '<option value=""' . ($selected == '' ? ' selected' : '').'>' . $default_option . '</option>';
        }

        foreach ($options as $value => $text) {
            $input .= '<option value="' . $value . '"' . ($selected == $value ? ' selected' : '').'>' . $text . '</option>';
        }

        $input .= '</select>';

        return $input;
    }
}
