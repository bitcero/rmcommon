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

trait RMFormComponent
{
    use RMProperties;

    /**
     * The class constructor. It's recommended to use this class trough {@link RMActiveForm}.
     * Example:
     * <pre>$form = new RMActiveForm($attributes);
     * $form->label($model, $element, array());</pre>
     *
     * @param  RMActiveRecord $model      <p>The object Model must be passed in order to perform operations with database.</p>
     * @param  string         $element    <p>Name of the element to this label will be assigned. This element must correspond to
     *                                    an existing database table field. e.g. 'name'</p>
     * @param  array          $parameters <p>An array with label attributes. Example:
     *                                    <pre>array(
     *                                    'class' => 'label-class',
     *                                    'data-something' => 'something',
     *                                    ...
     *                                    );</pre>
     * @throws RMException
     */
    public function __construct($model, $element, $parameters){
        $this->model = $model;
        $this->element = $element;
        $this->parameters = $parameters;
        $this->model_name = get_class($model);

        if ( 'ActiveButton' == __CLASS__ )
            return;

        $columns = $model->columns;

        /*if ( !isset( $columns[$element] ) && 'ActiveLabel' != __CLASS__ )
            throw new RMException( sprintf( __( 'The element "%s" does not exists.', 'rmcommon' ), $element ) );*/

        $columns = $model->columns;

        if ( isset( $columns[$element] ) && 1 != $columns[$element]['null'] ) {
            $this->required = true;

            if ( !isset($parameters['data-msg-required']) ) {
                $parameters['data-msg-required'] = __('This field is required.','rmcommon');
                $this->parameters = $parameters;
            }

        }else
            $this->required = isset( $parameters['required'] ) ? $parameters['required'] : false;

    }

    /**
     * Gets the correct name of a form element based in 2 aspects:
     * <ol>
     * <li>The name of the model</li>
     * <li>The name of the element</li>
     * </ol>
     * Example of the return:
     * <pre>Model[element]</pre>
     * @return string
     */
    final protected function get_name(){
        return str_replace('Admin', '', $this->model_name ) . '[' . $this->element . ']';

    }

    final protected function get_id(){
        return strtolower( str_replace('Admin', '', $this->model_name ) ) . '-' . $this->element;

    }

    public function open(){

        echo $this->render();

    }

}
