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
abstract class Sk_Db_Query_Builder_Decoration extends Db_Query_Builder_Action implements Interface_Db_Query_Builder_Decoration
{
	/**
	 * sql参数
	 * @var array
	 */
	protected $_params = array();
	
	/**
	 * 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
	 * @var array
	 */
	protected $_where;
	
	/**
	 * 字段数组
	 * @var array
	 */
	protected $_group_by;
	
	/**
	 * 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
	 * @var array
	 */
	protected $_having;
	
	/**
	 * 排序数组, 每个排序 = 字段+方向
	 * @var array
	 */
	protected $_order_by;
	
	/**
	 * 行限数组 limit, offset
	 * @var array
	 */
	protected $_limit;
	
	/**
	 * 联表数组
	 * join： 每个联表 = 表名 + 联表方式
	 * on: 每个联表条件 = 字段 + 运算符 + 字段
	 * @var array
	 */
	protected $_join;
	
	/**
	 * 构造函数
	 *
	 * @param Db|Callable $db 数据库连接|回调
	 * @param string $table 表名
	 * @param string $data 数据
	 */
	public function __construct($db, $table = NULL, $data = NULL)
	{
		parent::__construct($db, $table, $data);
		
		$column_quoter = array(&$this->_db, 'quote_column'); //　转义列：&$this->_db　此时未赋值，故引用
		$value_quoter = array($this, 'quote'); //　转移值
		
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_where = new Db_Query_Builder_Decoration_Clauses_Group('WHERE', array($column_quoter, 'str', $value_quoter));
		// 字段数组
		$this->_group_by = new Db_Query_Builder_Decoration_Clauses_Simple('GROUP BY', array($column_quoter));
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_having = new Db_Query_Builder_Decoration_Clauses_Group('HAVING', array($column_quoter, 'str', $value_quoter));
		// 排序数组, 每个排序 = 字段+方向
		$this->_order_by = new Db_Query_Builder_Decoration_Clauses_Simple('ORDER BY', array($column_quoter, 'order_direction'));
		// 行限数组 limit, offset
		$this->_limit = new Db_Query_Builder_Decoration_Clauses_Simple('LIMIT', array('int'));
        // 联表数组，每个联表join = 表名 + 联表方式 | 每个联表条件on = 字段 + 运算符 + 字段, 都写死在Db_Query_Builder_Decoration_Clauses_Join类
//  $this->_join = new Db_Query_Builder_Decoration_Clauses_Join($table, $type);
		$this->_join = array();
	}
	
	/**
	 * 编译修饰子句
	 * @return string
	 */
	public function compile_decoration()
	{
		$sql = '';
		$this->_params = array(); // 清空参数
		// 逐个处理修饰词及其表达式
		foreach (array('join', 'where', 'group_by', 'having', 'order_by', 'limit') as $name)
		{
			// 处理表达式
		    $exp = $this->{"_$name"};
		    // 表达式转字符串, 自动compile
			if (is_array($exp)) 
				$exp = implode(' ', $exp); 
			$sql .= ' '.$exp;
		}
		return array($sql, $this->_params);
	}
	
	/**
	 * 清空条件
	 * @return Db_Query_Builder
	 */
	public function clear()
	{
		foreach (array('where', 'group_by', 'having', 'order_by', 'limit') as $name)
			$this->$name->clear();
		
		$this->_join = $this->_params = array();
		
		return parent::clear();
	}
	
	/**
	 * 改写转义值的方法，搜集sql参数
	 * 
	 * @param mixed $value
	 * @return string
	 */
	public function quote($value)
	{
		// 1 将参数值直接拼接到sql
		//return $this->_db->quote($value);
		
		// 2 sql参数化: 将参数名拼接到sql, 独立出参数值, 以便执行时绑定参数值
		$this->_params[] = $value;
		return '?';
	}
	
	/**
	 * 多个where条件
	 * @param array $conditions
	 * @return Db_Query_Builder
	 */
	public function wheres(array $conditions)
	{
		foreach ($conditions as $column => $value)
			$this->where($column, '=', $value);
		return $this;
	}
	
	/**
	 * 多个on条件
	 * @param array $conditions
	 * @return Db_Query_Builder
	 */
	public function ons(array $conditions)
	{
		foreach ($conditions as $column => $value)
			$this->on($column, '=', $value);
		return $this;
	}
	
	/**
	 * 多个having条件
	 * @param array $conditions
	 * @return Db_Query_Builder
	 */
	public function havings(array $conditions)
	{
		foreach ($conditions as $column => $value)
			$this->having($column, '=', $value);
		return $this;
	}
	
	/**
	 * Alias of and_where()
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function where($column, $op, $value) {
		return $this->and_where ( $column, $op, $value );
	}
	
	/**
	 * Creates a new "AND WHERE" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function and_where($column, $op, $value)
	{
		if($value === NULL && $op == '=')
			$op = 'IS';
		$this->_where->add_subexp(array($column, $op, $value), 'AND');
		return $this;
	}
	
	/**
	 * Creates a new "OR WHERE" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function or_where($column, $op, $value)
	{
		if($value === NULL && $op == '=')
			$op = 'IS';
		$this->_where->add_subexp(array($column, $op, $value), 'OR');
		return $this;
	}
	
	/**
	 * Alias of and_where_open()
	 *
	 * @return Db_Query_Builder
	 */
	public function where_open()
	{
		return $this->and_where_open();
	}
	
	/**
	 * Opens a new "AND WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_where_open()
	{
		$this->_where->open('AND');
		return $this;
	}
	
	/**
	 * Opens a new "OR WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_where_open()
	{
		$this->_where->open('OR');
		return $this;
	}
	
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function where_close()
	{
		return $this->and_where_close();
	}
	
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_where_close()
	{
		$this->_where->close();
		return $this;
	}
	
	/**
	 * Closes an open "WHERE (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_where_close()
	{
		$this->_where->close();
		return $this;
	}
	
	/**
	 * Creates a "GROUP BY ..." filter.
	 *
	 * @param   mixed   $columns  column name or array($column, $alias) or object
	 * @return Db_Query_Builder
	 */
	public function group_by($columns)
	{
		$columns = func_get_args();
		$this->_group_by = array_merge($this->_group_by, $columns);
		return $this;
	}
	
	/**
	 * Alias of and_having()
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function having($column, $op, $value = NULL)
	{
		return $this->and_having($column, $op, $value);
	}
	
	/**
	 * Creates a new "AND HAVING" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function and_having($column, $op, $value)
	{
		$this->_having->add_subexp(func_get_args(), 'AND');
		return $this;
	}
	
	/**
	 * Creates a new "OR HAVING" condition for the query.
	 *
	 * @param   mixed   $column  column name or array($column, $alias) or object
	 * @param   string  $op      logic operator
	 * @param   mixed   $value   column value
	 * @return Db_Query_Builder
	 */
	public function or_having($column, $op, $value)
	{
		$this->_having->add_subexp(func_get_args(), 'OR');
		return $this;
	}
	
	/**
	 * Alias of and_having_open()
	 *
	 * @return Db_Query_Builder
	 */
	public function having_open()
	{
		return $this->and_having_open();
	}
	
	/**
	 * Opens a new "AND HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_having_open()
	{
		$this->_where->open('AND');
		return $this;
	}
	
	/**
	 * Opens a new "OR HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_having_open()
	{
		$this->_where->open('OR');
		return $this;
	}
	
	/**
	 * Closes an open "AND HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function having_close()
	{
		return $this->and_having_close();
	}
	
	/**
	 * Closes an open "AND HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function and_having_close()
	{
		$this->_where->close();
		return $this;
	}
	
	/**
	 * Closes an open "OR HAVING (...)" grouping.
	 *
	 * @return Db_Query_Builder
	 */
	public function or_having_close()
	{
		$this->_where->close();
		return $this;
	}
	
	/**
	 * Applies sorting with "ORDER BY ..."
	 *
	 * @param   mixed   $column     column name or array($column, $alias) or object
	 * @param   string  $direction  direction of sorting
	 * @return Db_Query_Builder
	 */
	public function order_by($column, $direction = NULL)
	{
		$this->_order_by->add_subexp(array($column, $direction));
		return $this;
	}
	
	/**
	 * Return up to "LIMIT ..." results
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return Db_Query_Builder
	 */
	public function limit($limit, $offset = 0)
	{
		if($offset === 0)
			$this->_limit->add_subexp(array((int)$limit));
		else
			$this->_limit->add_subexp(array((int)$offset, (int)$limit));
		return $this;
	}
	
	/**
	 * Adds addition tables to "JOIN ...".
	 *
	 * @param   mixed   $table  column name or array($column, $alias) or object
	 * @param   string  $type   join type (LEFT, RIGHT, INNER, etc)
	 * @return Db_Query_Builder
	 */
	public function join($table, $type = NULL)
	{
		$column_quoter = array(&$this->_db, 'quote_column'); //　转义列
		$this->_join[] = new Db_Query_Builder_Decoration_Clauses_Join($table, $type, 'ON', array($column_quoter, 'str', $column_quoter));
		return $this;
	}
	
	/**
	 * Adds "ON ..." conditions for the last created JOIN statement.
	 *
	 * @param   mixed   $c1  column name or array($column, $alias) or object
	 * @param   string  $op  logic operator
	 * @param   mixed   $c2  column name or array($column, $alias) or object
	 * @return Db_Query_Builder
	 */
	public function on($c1, $op, $c2)
	{
		end($this->_join)->add_subexp(func_get_args(), 'AND');
		return $this;
	}
}