<?php
// $Id$
// --------------------------------------------------------------
// Common Utilities
// A modules framework by Red Mexico
// Author: Eduardo Cortes
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// URI: http://www.redmexico.com.mx
// --------------------------------------------------------------

/**
 * This class allow work with SQL statements in order to provide functionality to ActiveRecord
 */
class RMDb
{

    private $_properties = array(
        // XOOPS database object
        'database'      => '',
        // Main table to use
        'table'         => '',
        // Table columns
        'columns'       => array(),
        // Primary key
        'primary_key'   => '',
        // Relations to other tables
        'relations'     => array(),
        // Inner Joins sentences
        'inner_joins'   => array(),
        // Group by instruction
        'groupby'       => '',
        'filters'       => array(),
    );

    public function __construct(){
        global $xoopsDB;

        $this->database = $xoopsDB;

    }

    /**
     * Returns a property value, or execute a method based on its name.
     *
     * @param string $name the property name or method name
     * @return mixed the property value or method return
     * @throws RMException if the property or event is not defined
     * @see __set
     */
    public function __get($name){

        $method = 'get' . $name;

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
        elseif ( isset( $this->_properties[$name] ) )
            return $this->_properties[$name] = $value;

        if(method_exists($this,'get'.$name))
            throw new RMException( sprintf( __( 'Property "%s::%s" is read only.', 'rmcommon' ), get_class( $this ), $name ) );
        else
            throw new RMException( sprintf( __( 'Property "%s::%s" is not defined.', 'rmcommon' ), get_class( $this ), $name ) );

    }

    /**
     * Returns the table name without prefix
     * If you need to get real table name use:
     * <pre>$this->table</pre>
     * @return string
     */
    public function getTableName(){

        return str_replace( $this->database->prefix(), '', $this->table );

    }

    /**
     * Set the table name and add automatically the table prefix.
     * @param string $name Table name
     * @throws RMException if name is empty
     */
    public function setTable( $name ){

        if( $name == '' )
            throw new RMException( __('Table name could not be empty.','rmcommon' ) );

        $this->table = $this->database->prefix( $name );
        $this->_properties['from'][] = '`' . $this->table . '`';

    }

    /**
     * Load table columns
     *
     * @param bool $force Load columns even if they loaded previously
     * @return mixed
     */
    public function load_columns( $force = false ){

        if ( !$force && !( empty($this->columns ) ) )
            return null;

        // Get columns
        $columns_result = $this->database->queryF( "SHOW COLUMNS IN ".$this->table );

        while ( $column = $this->database->fetchArray( $columns_result ) ){

            $this->_properties['columns'][$column['Field']] = array(
                'primary'   => $column['Key']=='PRI' ? true : false,
                'type'      => preg_replace( "/\([0-9]+\)/", '', $column['Type'] ),
                'len'       => preg_replace("/[^0-9]/", '', $column['Type']),
                'null'      => $column['Null']=='NO' ? false : true,
                'default'   => $column['Default'],
                'extra'     => $column['Extra'],
                'unique'    => 0,
                'index'     => false,
                'title'     => $column['Field'],
            );

            if ( $column['Key'] == 'PRI' )
                $this->primary_key = $column['Field'];

        }

        unset ( $columns_result );

        // Get indexes
        $indexes = $this->database->queryF( "SHOW INDEXES IN ".$this->table );
        while ( $row = $this->database->fetchArray( $indexes ) ) {

            $this->_properties['columns'][$row['Column_name']]['unique'] = $row['Non_unique']==0 ? 1 : 0;
            $this->_properties['columns'][$row['Column_name']]['index'] = true;

        }

        unset($indexes);

        return $this->columns;

    }

    /**
     * Add a new INNER JOIN instruction to SQL statement
     * @param string $table Table name to join
     * @param string $on_field Field to use in join
     * @throws RMException
     */
    public function innerJoin( $table, $on_field ){

        if( empty( $table ) || $on_field == '' )
            throw new RMException( __('No INNER JOIN parameters has been provided.', 'rmcommon') );

        $this->_properties['inner_joins'][] = 'INNER JOIN `' . $table . '` ON `' . $table . '`.`' . $on_field . '`';

    }

    /**
     * Set the tables where results will be searched
     * @param array $tables
     */
    public function setFrom( $tables ){

        if ( !is_array( $tables ) || empty( $tables ) )
            return;

        $this->_properties['from'] = $tables;

    }

    public function setWhere( $filters ){

        if ( !is_array( $filters ) || empty( $filters ))
            return;

        $this->_properties['filters'] = $filters;

    }

    /**
     * Set fields to use in SELECT sentence
     * @param array $fields Fields names
     */
    public function setSelect( $fields ){

        if ( empty( $fields )  )
            $this->_properties['select'] = '*';

        $this->_properties['select'] = implode( ", ", $fields );

    }

    public function getSelect(){

        $select = "SELECT ".$this->_properties['select']." FROM ";

        if ( !empty( $this->_properties['inner_joins'] ) ){

            $select .= '(' . implode( ", ", $this->from );
            $select .= implode( " ", $this->inner_joins ) . ')';

        } else
            $select .= implode(", ", $this->from);

        $filters = $this->filters;

        if ( !empty( $filters ) ){

            $conditions = array();

            foreach ( $filters as $field => $filter ){

                $condition = '';
                $condition .= isset($filter['table']) ? '`'.$this->prefix($filter['table']).'`.' : '';
                $condition .= '`'.$field.'`' . $filter['operator'] . $this->escape( $filter['value'] );
                $conditions[] = $condition;

            }

            $select .= " WHERE " . implode( " ", $conditions );

        }

        if( $this->groupby != '' )
            $select .= " GROUP BY " . $this->groupby;

        return $select;

    }

    public function getInsert( $data ){

        if( empty( $data ) )
            return null;

        $fields = array();
        $values = array();
        foreach ( $data as $field => $value ){
            $fields[] = $field;
            $values[] = $value;
        }

        $insert = "INSERT INTO " . $this->table . ' (`' . implode("`,`", $fields) . "`) VALUES ('" . implode("','", $values) . "');";

        return $insert;

    }

    /**
     * Escape a string for secure use
     * @param string String to escape
     * @return string Escaped string
     */
    public function escape($string){

        return mysql_real_escape_string($string);

    }

    /**
     * Escape an array
     * @param array $array
     * @return array
     */
    public function escape_array( $array ){

        foreach( $array as $index => $value){

            if (is_array($value))
                $array[$index] = $this->escape_array($value);
            else
                $array[$index] = $this->escape( $value );

        }

        return $array;

    }

    public function prefix( $name ){

        return $this->database->prefix($name);

    }

}
