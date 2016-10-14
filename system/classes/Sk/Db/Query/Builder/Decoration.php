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
abstract class Sk_Db_Query_Builder_Decoration extends Db_Query_Builder_Action
{
	/**
	 * 修饰词
	 * @var array
	 */
	public static $decorations = array(
		'where' => 'WHERE',
		'group_by' => 'GROUP BY',
		'having' => 'HAVING',
		'order_by' => 'ORDER BY',
		'limit' => 'LIMIT',
		'join' => 'JOIN'
	);
	
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
	 * 联表数组，每个联表 = 表名 + 联表方式
	 * @var array
	 */
	protected $_join;
	
	/**
	 * 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
	 * @var array
	 */
	protected $_on;

	public function __construct($action, $db, $table = NULL, $data = NULL)
	{
		parent::__construct($action, $db, $table, $data);
		
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_where = new Db_Query_Builder_Decoration_Expression_Group($this->_db, array('column', 'str', 'value'));
		// 字段数组
		$this->_group_by = new Db_Query_Builder_Decoration_Expression_Simple($this->_db, array('column'));
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_having = new Db_Query_Builder_Decoration_Expression_Group($this->_db, array('column', 'str', 'value'));
		// 排序数组, 每个排序 = 字段+方向
		$this->_order_by = new Db_Query_Builder_Decoration_Expression_Simple($this->_db, array('column', 'order_direction'));
		// 行限数组 limit, offset
		$this->_limit = new Db_Query_Builder_Decoration_Expression_Simple($this->_db, array('int'));
        // 联表数组，每个联表 = 表名 + 联表方式
        $this->_join = array();
        //$this->_join = new Db_Query_Builder_Decoration_Expression_Simple($this->_db, array('table', 'join_type'));
		// 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
		//$this->_on = new Db_Query_Builder_Decoration_Expression_Group($this->_db, array('column', 'str', 'column'));
	}
	
	/**
	 * 编译修饰子句
	 * @return string
	 */
	public function compile_decoration()
	{
		$sql = '';
		// 逐个处理修饰词及其表达式
		foreach (static::$decorations as $name => $title)
		{
			// 处理表达式
		    $exp = $this->{"_$name"};
			if (is_array($exp)) 
				$exp = implode('', $exp); // 表达式转字符串, 自动compile
			else 
				$exp = "$exp";
			// 添加修饰词
			if($exp)
				$sql .= " $title $exp";
		}
		return $sql;
	}

    /**
     * Alias of and_where()
     *
     * @param   mixed   $column  column name or array($column, $alias) or object
     * @param   string  $op      logic operator
     * @param   mixed   $value   column value
     * @return  $this
     */
    public function where($column, $op, $value)
    {
        return $this->and_where($column, $op, $value);
    }

    /**
     * Creates a new "AND WHERE" condition for the query.
     *
     * @param   mixed   $column  column name or array($column, $alias) or object
     * @param   string  $op      logic operator
     * @param   mixed   $value   column value
     * @return  $this
     */
    public function and_where($column, $op, $value)
    {
        $this->_where->add_subexp(func_get_args(), ' AND ');
        return $this;
    }

    /**
     * Creates a new "OR WHERE" condition for the query.
     *
     * @param   mixed   $column  column name or array($column, $alias) or object
     * @param   string  $op      logic operator
     * @param   mixed   $value   column value
     * @return  $this
     */
    public function or_where($column, $op, $value)
    {
        $this->_where->add_subexp(func_get_args(), ' OR ');
        return $this;
    }

    /**
     * Alias of and_where_open()
     *
     * @return  $this
     */
    public function where_open()
    {
        return $this->and_where_open();
    }

    /**
     * Opens a new "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function and_where_open()
    {
        $this->_where->open(' AND ');
        return $this;
    }

    /**
     * Opens a new "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function or_where_open()
    {
        $this->_where->open(' OR ');
        return $this;
    }

    /**
     * Closes an open "WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function where_close()
    {
        return $this->and_where_close();
    }

    /**
     * Closes an open "WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function and_where_close()
    {
        $this->_where->close();
        return $this;
    }

    /**
     * Closes an open "WHERE (...)" grouping.
     *
     * @return  $this
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
     * @return  $this
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
     * @return  $this
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
     * @return  $this
     */
    public function and_having($column, $op, $value)
    {
        $this->_having->add_subexp(func_get_args(), ' AND ');
        return $this;
    }

    /**
     * Creates a new "OR HAVING" condition for the query.
     *
     * @param   mixed   $column  column name or array($column, $alias) or object
     * @param   string  $op      logic operator
     * @param   mixed   $value   column value
     * @return  $this
     */
    public function or_having($column, $op, $value)
    {
        $this->_having->add_subexp(func_get_args(), ' OR ');
        return $this;
    }

    /**
     * Alias of and_having_open()
     *
     * @return  $this
     */
    public function having_open()
    {
        return $this->and_having_open();
    }

    /**
     * Opens a new "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function and_having_open()
    {
        $this->_where->open(' AND ');
        return $this;
    }

    /**
     * Opens a new "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function or_having_open()
    {
        $this->_where->open(' OR ');
        return $this;
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function having_close()
    {
        return $this->and_having_close();
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function and_having_close()
    {
        $this->_where->close();
        return $this;
    }

    /**
     * Closes an open "OR HAVING (...)" grouping.
     *
     * @return  $this
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
     * @return  $this
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
     * @return  $this
     */
    public function limit($limit, $offset = 0)
    {
        if($offset === 0)
            $this->_limit->add_subexp(array($limit));
        else
            $this->_limit->add_subexp(array($offset, $limit));
        return $this;
    }

    /**
     * Adds addition tables to "JOIN ...".
     *
     * @param   mixed   $table  column name or array($column, $alias) or object
     * @param   string  $type   join type (LEFT, RIGHT, INNER, etc)
     * @return  $this
     */
    public function join($table, $type = NULL)
    {
        $this->_join[] = $join = new Db_Query_Builder_Decoration_Expression_Simple($this->_db, array('table', 'join_type'));
        $join->add_subexp(array($table, $type));
        return $this;
    }

    /**
     * Adds "ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed   $c1  column name or array($column, $alias) or object
     * @param   string  $op  logic operator
     * @param   mixed   $c2  column name or array($column, $alias) or object
     * @return  $this
     */
    public function on($c1, $op, $c2)
    {
        $on = end($this->_join);
		if(!$on instanceof Db_Query_Builder_Decoration_Expression_Group)
		{
			$this->_join[] = ' ON ';
			$this->_join[] = $on = new Db_Query_Builder_Decoration_Expression_Group($this->_db, array('column', 'str', 'column'));
		}
		
        $on->add_subexp(array($c1, $op, $c2));
        return $this;
    }
}