<?php
// $Id: loader.php 1041 2012-09-09 06:23:26Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include RMCPATH . '/class/exception.class.php';
include RMCPATH . '/class/ar/db.class.php';

/**
 * Class RMActiveRecord
 */
abstract class RMActiveRecord
{
    private $_properties = array(
        'db'                => '',
        'system_tables'     => array(
            'avatar',
            'config',
            'groups',
            'modules',
            'newblocks',
            'ranks',
            'smiles',
            'users',
            'xoopscomments'
        ),
        'results_x_page'    => 20,
        'order_results'     => 'ASC',
        'template'          => '',
        'data'              => array(),
        'errors'            => array(),
        'columns'           => array(),
        'relations'         => array(),
        'excluded_fields'   => array(),
        'groupby'           => '',
        'results'           => '',
        'attributes'        => array(),
    );

    /**
     * Constructor
     * The $objects param must correspond to a database table that will be used as objects container.
     * Example:
     *
     * <pre>
     * $ar = new RMActiveRecord('posts', 'mywords');
     * </pre>
     *
     * This instruction will search for table:
     * <pre>
     * mod_mywords_posts.
     * </pre>
     *
     * A special case is when you need to get objects from system tables. In this case the XOOPS
     * tables not follow the standar naming conventions. You can search those objects only in a
     * limited number of XOOPS tables, determined by 'system_tables' property:
     *
     * Example:
     * <pre>
     * $ar = new RMActiveRecord('users');
     * </pre>
     *
     * In this case, AR will search objects in table:
     * <pre>
     * users
     * </pre>
     *
     * @param string $objects Object name related to database tables
     * @param string $owner Object (e.g. rmcommon, mywords, rmcommon_plugin)
     */
    public function __construct( $objects = null, $owner = null ){

        $objects = trim( $objects );

        $this->db = new RMdb();
        $this->xdb = XoopsDatabaseFactory::getDatabaseConnection();

        if ( '' == $objects || null == $objects ){
            $this->add_error( __('No objects name has been provided!','rmcommon') );
            return false;
        }

        if ( ( ''==$owner || null==$owner ) & !in_array($objects, $this->system_tables) ){
            $this->add_error( __('No owner has been specified!','rmcommon') );
            return false;
        }

        if ( in_array( $objects, $this->system_tables ) )
            $this->db->table = $objects;
        else
            $this->db->table = 'mod_'.$owner.'_'.$objects;

        $this->columns = $this->db->load_columns();

    }

    /**
     * Returns a property value, or execute a method based on its name.
     *
     * <pre>
     * $ar = new RMActiveRecord('posts','mywords');
     * $var = $ar->property;
     * </pre>
     *
     * @param string $name the property name or method name
     * @return mixed the property value or method return
     * @throws RMException if the property or event is not defined
     * @see __set
     */
    public function __get($name){

        $method = 'get_' . $name;

        if( method_exists( $this, $method ) )
            return $this->$method();
        elseif( isset( $this->_properties[$name] ) )
            return $this->_properties[$name];
        throw new RMException( sprintf( __( 'Property "%s.%s" is not defined.', 'rmcommon' ), get_class( $this ), $name ) );

    }

    /**
     * Sets value of a component property.
     *
     * @param string $name the property name or the method
     * @param mixed $value the property value
     * @return mixed
     * @throws RMException if the property/method is not defined or the property is read only.
     * @see __get
     */
    public function __set($name,$value){

        $method = 'set' . $name;

        if ( method_exists( $this, $method ) )
            return $this->$method( $value );
        elseif( method_exists( $this,'get_'.$name ) )
            throw new RMException( sprintf( __( 'Property "%s.%s" is read only.', 'rmcommon' ), get_class( $this ), $name ) );
        else
            $this->_properties[$name] = $value;

    }


    /**
     * Allows to stabling a custom titles for table columns
     * The array must be passed in next way:
     * <pre>
     * $assignments = array(
     *      'column_name_1' => "The title",
     *      'column_name_2' => "Title 2"
     * );
     * </pre>
     * @param $assignments
     * @return bool
     * @throws RMException
     */
    public function setTitles($assignments){

        if ( empty( $assignments ) )
            throw new RMException( __( 'You must provide an array with columns names and titles as values pair!', 'rmcommon' ) );


        foreach ( $assignments as $column => $title ){

                $this->_properties['columns'][$column]['title'] = $title;

        }

        return true;

    }

    /**
     * Get the title for a specific column
     * @param string $field Field/Column name
     * @return mixed
     */
    public function title( $field ){

        if($field=='')
            return;

        if(isset($this->_properties['columns'][$field]))
            return $this->_properties['columns'][$field]['title'];
        else
            return;

    }

    /**
     * Set relationships for current table. Use this method only when you are working with MyISAM
     * The relations parameter must be an array with the information for relations:
     * <pre>
     * array(
     *      [type of relations] => value,
     *      'foreign-table'     => table name,
     *      'foreign-key'       => key:key 2,
     *      'use-keys'          => array(key 1,key 2[,...]);
     *      'method'            => CASCADE|RESTRICT|NO_ACTION|SET_NULL
     * );
     * </pre>
     *
     * <strong>Example 1:</strong>
     * Illustrate a relation based on a table with ids for users and groups.
     * The table with ids is groups_users_link and the ids are related to users and groups tables.
     * With CASCADE method we force to delete related rows in both tables
     * <pre>
     *      $ar->setRelations(array(
     *          'relations-table'   => 'groups_users_link',
     *          'foreign-table'     => 'groups',
     *          'foreign-key'       => 'uid:groupid',
     *          'use-keys'          => array('groupid','name'),
     *          'method'            => 'CASCADE'
     *      ));
     * </pre>
     *
     * <strong>Example 2:</strong>
     * In this example, we only specify a relations based on key "author" from current table
     * with key "id_editor" from foreign table.
     * When a post is deleted no action (NO_ACTION) is executed on editors table.
     * <pre>
     *      $ar->setRelations(array(
     *          'foreign-table'     => 'mod_mywords_editors',
     *          'foreign-key'       => 'author:id_editor',
     *          'use-keys'          => array('id_editor','uid', 'name'),
     *          'method'            => 'NO_ACTION'
     *      ));
     * </pre>
     *
     * <strong>Example 3:</strong>
     * Similar to example 2, but now the current table is for editors.
     * When a editor is deleted, CASCADE force to delete posts that belongs to editor
     * <pre>
     *      $ar->setRelations(array(
     *          'foreign-table'     => 'mod_mywords_posts',
     *          'foreign-key'       => 'id_editor:author',
     *          'method'            => 'CASCADE'
     *      ));
     * </pre>
     *
     * @param $relations
     */
    public function setRelations( $relations ){

        $this->_properties['relations'][] = $relations;

    }

    /**
     * Exclude specific keys from results
     * @param array $excluded
     */
    public function setExcludedFields( $excluded = array() ){

        if( empty( $excluded ) )
            return;

        foreach( $excluded as $field ){
            if( !isset( $this->_properties['columns'][$field] ) )
                continue;

            $this->_properties['excluded_fields'][] = $field;

        }

    }

    public function setGroupby( $fields ){

        $this->_properties['groupby'] = $fields;
        $this->db->groupby = $fields;

    }

    /**
     * Retrieves all results from a database query and assign all data to a stdClass object.
     * @param database results $results
     * @return array
     */
    public function walk_results( $results = null ){

        if ( $results == null )
            $results = $this->results;

        $data = array();

        while( $row = $this->db->database->fetchArray( $results ) ){

            $data[] = (object) $row;

        }

        return $data;

    }

    /**
     * Get array values and format as pairs.
     * Example:
     * <pre>
     * $fruits = array(
     *  array(
     *      'id' => 'one',
     *      'name' => 'apple',
     *      'qty' => 15,
     *  ),
     *  array(
     *      'id' => 'two',
     *      'name' => 'orange',
     *      'qty' => 8,
     *  ),
     *  array(
     *      'id' => 'three',
     *      'name' => 'lemon',
     *      'qty' => 18
     *  ),
     * );
     *
     * $pairs = $this->do_pairs($fruits, 'id', 'name');
     * print_r($pairs);
     * </pre>
     * The result will be:
     * <pre>
     *  Array
     *  (
     *      [one] => apple
     *      [two] => orange
     *      [three] => lemon
     *  )
     * </pre>
     * <strong>Tip:</strong> This function could be used with select fields.
     * @param $array
     * @param string $key Name of the array key that will be used as key in the new array.
     * @param string $value Name of the array key that will be used for values in the new array.
     * @return array
     */
    public function do_pairs($array, $key, $value){

        if ( !is_array( $array ) || empty( $array ) ){
            trigger_error( __('Invalid argument in RMActiveRecord::do_pairs. Array is required.', 'rmcommon'), E_USER_ERROR);
            return;
        }

        if ( $key == '' || $value == '' ){
            trigger_error( __('Invalid argument in RMActiveRecord::do_pairs. A key and value are required.', 'rmcommon'), E_USER_ERROR);
            return;
        }

        $new_array = array();

        foreach( $array as $item ){

            if ( is_object($item) ){

                if ( isset( $item->$key ) && isset( $item->$value ) )
                    $new_array[$item->$key] = $item->$value;

            } else {

                if ( isset( $item[$key] ) && isset( $item[$value] ) )
                    $new_array[$item[$key]] = $item[$value];

            }

        }

        return $new_array;

    }

    /**
     * Perform a query over a database.
     *
     * This method could be used in conjunction with {@link create_select_statement}.
     *
     * @param string $sql
     * @return result of the database query
     */
    public function query( $sql = '' ){

        if ( $sql == '' )
            $sql = $this->create_select_statement();

        $this->results = $this->db->database->query( $sql );

        return $this->results;

    }

    /**
     * Create de SELECT statement for query based on excluded fields and relations
     *
     * String mode can be used for complex WHERE filters. For basic filters is very encourage to use
     * array mode.
     *
     * @param array|string filters
     * @param array $options Array with options that will overwrite existing parameters
     * @return array
     */
    protected function create_select_statement( $filters = '', $options = array() ){

        // If specified fields to exclude from results...
        $excluded_fields = isset( $options['exluded_fields'] ) ? $options['excluded_fields'] : (empty($options) ? $this->excluded_fields : array());
        if( !empty( $excluded_fields ) ){

            $selected_fields = array();

            foreach( $this->_properties['columns'] as $field => $structure ){

                if ( in_array( $field, $excluded_fields ))
                    continue;

                $selected_fields[] = $field;

            }

        } else
            $selected_fields[] = '*';

        // Tables where registers reside in
        $table = isset( $options['table'] ) ? $options['table'] : $this->db->table;
        $from_tables[] = '`' . $table . '`';

        // Is there exists foreign keys
        $relations = isset( $options['relations'] ) ? $options['relations'] : ( $table == $this->db->table ? $this->relations : array() );

        if( $relations ){

            if ( !empty( $selected_fields ) ){

                foreach ( $selected_fields as $id => $field ){
                    $selected_fields[$id] = '`' . $table . '`.' . ( $field == '*' ? $field : '`' . $field . '`');
                }

            }

            foreach ( $relations as $relation ){

                // When relations for both tables are stored in a different table (many-to-many)
                if ( isset( $relation['relations-table'] ) && '' != $relation['relations-table'] )
                    //$from_tables[] = '`' . $this->db->prefix( $relation['relations-table'] ) . '`';
                    $this->db->innerJoin( $this->db->prefix($relation['relations-table']), $relation['foreign-keys'][$relation['relations-table']]);

                //$from_tables[] = '`' . $this->db->prefix( $relation['foreign-table'] ) . '`';
                $this->db->innerJoin( $this->db->prefix($relation['foreign-table']), $relation['foreign-keys'][$relation['foreign-table']]);

                foreach( $relation['use-keys'] as $key ){
                    $selected_fields[] = '`'.$this->db->prefix($relation['foreign-table']).'`.`'.$key.'`';
                }

            }

        }

        $this->db->filters = $filters;
        $this->db->select = $selected_fields;
        $this->db->from = $from_tables;

        $select_sql = $this->db->select;

        return $select_sql;

    }

    /**
     * Constructs the ORDER BY statement according to order_results property
     * @return string
     */
    protected function order_statement(){

        if( $this->order_results=='' )
            return;

        $sql = " ORDER BY ";

        foreach ( $this->order_results as $column => $sort ){
            $sql .= $sql == '' ? "`$column` $sort" : ", `$column` $sort";
        }

        return $sql;

    }

    protected function limit_statement( $start = null ){

        if ( $this->results_x_page <= 0 )
            return;

        if ( $start == null )
            $start = RMHttpRequest::request( 'start', 'integer', 0 );

        $sql = " LIMIT $start, ".$this->results_x_page;

        return $sql;

    }

    /**
     * @param string $error_message Error message
     * @param int $error_number Optional error number
     * @param bool $trigger Trigger a PHP error
     */
    public function add_error( $error_message, $error_number=0, $trigger = true ){
        if($error_number>0)
            $this->_properties['errors'][$error_number] = $error_message;
        else
            $this->_properties['errors'][] = $error_message;

        if ( $trigger )
            trigger_error( $error_message, E_USER_WARNING );
    }

    /**
     * Returns the errors occurred in this class
     * @param bool $as_html Returns HTML or array
     * @return array or string
     */
    public function get_errors($as_html = true){

        if(false == $as_html)
            return $this->_properties['errors'];

        return implode( "<br>\n", $this->_properties['errors']);
    }

    protected function rules( $action = 'new' ){}

    /**
     * Save current model data with a new registry
     */
    public function save(){

        $attributes = $this->attributes;
        $fields = array();

        /**
         * Name to get post/get/put vars
         */
        $vars_container = get_class($this);

        /**
         * Determinamos si se trata de un objeto existente
         * o un objeto nuevo en base a la clave principal.
         * Si el contenedor de la variables cuenta con un
         * índice nombrado igual que la clave principal entonces
         * es un objeto existente, si no, se trata de un objeto
         * nuevo.
         * Importante: el valor del índice nombrado como la clave
         * principal siempre debe ser numérico.
         */
        if ( isset( $vars_container[$this->db->primary_key] ) )
            $action_type = 'update';
        else
            $action_type = 'new';

        $this->data = RMHttpRequest::post( $vars_container, 'array', array() );

        foreach ( $this->columns as $column => $data ){

            /**
             * Si se trata de un objeto nuevo evitamos la verificación
             * de la clave principal.
             */
            if ( $column == $this->db->primary_key && $action_type == 'new' )
                continue;

            /**
             * Verificamos la integridad de los datos proporcionados.
             */
            if ( !$this->verify_http_data( $column ) )
                return false;

            $fields[$column] = isset( $rules[$column] ) ? $rules[$column]( $attributes ) : ( isset( $attributes[$column] ) ? $attributes[$column] : null );

        }

        return $this->db->database->queryF($this->db->getInsert( $fields ));

    }

    /**
     * Permite comprobar que los datos que han sido proporcionados vía
     * HTTP correspondan con los datos requridos por la base de datos
     * para poder insertarlos adecuadamente.
     *
     * @param string $column <p>Nombre del campo</p>
     * @return bool
     */
    private function verify_http_data( $column ){

        $values = $this->data;
        $columns = $this->columns;

        if ( !isset( $columns[$column] ) )
            return false;

        if ( $columns[$column]['null'] != 'null' && !isset( $values[$column] ) ){

            if ( $columns['default'] != '' )
                $values[$column] = $columns[$column]['default'];
            else {
                $this->add_error( sprintf(__('The field "%s" could not be empty.', 'rmcommon'), $columns[$column]['title'] ) );
                return false;
            }

        }

        return true;

    }

}