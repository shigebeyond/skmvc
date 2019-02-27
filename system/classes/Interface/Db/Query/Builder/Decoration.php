<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 修饰子句: 由修饰词where/group by/order by/limit来构建的子句
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
interface Interface_Db_Query_Builder_Decoration
{
	/**
	 * 编译修饰子句
	 * @return string
	 */
	public function compile_decoration();
	
	/**
	 * 改写转义值的方法，搜集sql参数
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public function quote($value);
	
	/**
	 * 多个where条件
	 * @param array $conditions
	 * @return Sk_Db_Query_Builder_Decoration
	 */
	public function wheres(array $conditions);
	
	/**
	 * 多个on条件
	 * @param array $conditions
	 * @return Sk_Db_Query_Builder_Decoration
	 */
	public function ons(array $conditions);
		
	/**
	 * 多个having条件
	 * @param array $conditions
	 * @return Sk_Db_Query_Builder_Decoration
	 */
	public function havings(array $conditions);
	
	/**
	 * Alias of and_where()
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function where($column, $op, $value);
	
	/**
	 * Creates a new "AND WHERE" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function and_where($column, $op, $value);
	
	/**
	 * Creates a new "OR WHERE" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function or_where($column, $op, $value);
	
	/**
	 * Alias of and_where_open()
	 *
	 * @return Db_Query_Builder
	 */
	public function where_open();
	
	/**
	 * Opens a new "AND WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_where_open();
	
	/**
	 * Opens a new "OR WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_where_open();
	
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function where_close();
	
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_where_close();
	
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_where_close();
	
	/**
	 * Creates a "GROUP BY ..." filter.
	 *
	 * @param   mixed   $columns  column name or array($column, $alias) or object
	 * @return Db_Query_Builder
	 */
	public function group_by($columns);
	
	/**
	 * Alias of and_having()
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function having($column, $op, $value = NULL);
	
	/**
	 * Creates a new "AND HAVING" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function and_having($column, $op, $value);
	
	/**
	 * Creates a new "OR HAVING" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function or_having($column, $op, $value);
	
	/**
	 * Alias of and_having_open()
	 *
	 * @return Db_Query_Builder
	 */
	public function having_open();
	
	/**
	 * Opens a new "AND HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_having_open();
	
	/**
	 * Opens a new "OR HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_having_open();
	
	/**
	 * Closes an open "AND HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function having_close();
	
	/**
	 * Closes an open "AND HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_having_close();
	
	/**
	 * Closes an open "OR HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_having_close();
	
	/**
	 * Applies sorting with "ORDER BY ..."
	 *
	 * @param   mixed   $column     column name or array($column, $alias) or object
	 * @param   string  $direction  direction of sorting
	 * @return Db_Query_Builder
	 */
	public function order_by($column, $direction = NULL);
	
	/**
	 * Return up to "LIMIT ..." results
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return Db_Query_Builder
	 */
	public function limit($limit, $offset = 0);
	
	/**
	 * Adds addition tables to "JOIN ...".
	 *
	 * @param   mixed   $table  column name or array($column, $alias) or object
	 * @param   string  $type   join type (LEFT, RIGHT, INNER, etc)
	 * @return Db_Query_Builder
	 */
	public function join($table, $type = NULL);
	
	/**
	 * Adds "ON ..." conditions for the last created JOIN statement.
	 *
	 * @param   mixed   $c1  column name or array($column, $alias) or object
	 * @param   string  $op  logic operator
	 * @param   mixed   $c2  column name or array($column, $alias) or object
	 * @return Db_Query_Builder
	 */
	public function on($c1, $op, $c2);
}