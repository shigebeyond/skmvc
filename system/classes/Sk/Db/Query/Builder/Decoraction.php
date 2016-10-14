<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 修饰子句: 由修饰符where/group by/order by/limit来构建的子句
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

	public function __construct($db, $table = NULL)
	{
		parent::__construct($db, $table);
		
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_where = new Db_Query_Builder_Decoratoin_Expression_Group($this->_db, array('column', 'str', 'value'));
		// 字段数组
		$this->_group_by = new Db_Query_Builder_Decoratoin_Expression_Simple($this->_db, array('column'));
		// 条件数组, 每个条件 = 字段名 + 运算符 + 字段值
		$this->_having = new Db_Query_Builder_Decoratoin_Expression_Group($this->_db, array('column', 'str', 'value'));
		// 排序数组, 每个排序 = 字段+方向
		$this->_order_by = new Db_Query_Builder_Decoratoin_Expression_Simple($this->_db, array('column', 'order_direction'));
		// 行限数组 limit, offset
		$this->_limit = new Db_Query_Builder_Decoratoin_Expression_Simple($this->_db, array('int'));
		// 联表数组，每个联表 = 表名 + 联表方式
		$this->_join = new Db_Query_Builder_Decoratoin_Expression_Simple($this->_db, array('table', 'join_type'));
		// 联表条件数组，每个联表条件 = 字段 + 运算符 + 字段
		$this->_on = new Db_Query_Builder_Decoratoin_Expression_Group($this->_db, array('column', 'str', 'column'));
	}
	
	/**
	 * 编译修饰子句
	 * @return string
	 */
	public function compile_decoration()
	{
		$sql = '';
		$exps = array('where', 'group_by', 'having', 'order_by', 'limit', 'join', 'on');
		foreach ($exps as $exp)
		{
		    $exp = $this->{"_$exp"};
			if (is_array($exp)) 
				$sql .= implode('', $exp);
			else
				$sql .= $exp;
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
	
}