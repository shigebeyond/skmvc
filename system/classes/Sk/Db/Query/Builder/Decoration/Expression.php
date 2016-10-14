<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql修饰子句的表达式模拟构建
 *     每个修饰符是一个表达式(如where/group by), 其包含多个子表达式(如where可以有多个条件, 如name='shi', age=1), 每个子表达式有多个元素组成(如name/=/'shi')
 *     每个元素有对应的处理函数
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
abstract class Sk_Db_Query_Builder_Decoratoin_Expression
{
	/**
	 * 
	 * @var Db
	 */
	protected $_db;
	
	/**
	 * 子表达式, 可视为行
	 * @var array
	 */
	protected $_subexps = array();
	
	/**
	 * 每个元素的处理器, 可视为列的处理
	 * @var array
	 */
	protected $_element_handlers;
	
	public function __construct($db, array $element_handler)
	{
		$this->_db = $db;
		$this->_element_handlers = $element_handler;
	}

	/**
	 * 编译多个子表达式
	 * @return string
	 */
	public function compile()
	{
		if (empty($this->_subexps))
			return NULL;
		
		// 逐个子表达式编译+合并
		// 1 子表达式的连接符是一样的
		//return implode($this->_default_delimiter, array_map(array($this, 'compile_row'), $this->_subexps));
		
		// 2 每个子表达式拼接的连接符不一样
		$str = '';
		foreach ($this->_subexps as $i => $subexp)
			$str .= $this->compile_subexp($subexp);
		return $str;
	}

    /**
     * 添加一个子表达式+连接符
     *
     * @param array $subexp 子表达式
     * @param string $delimiter 当前子表达式的连接符
     * @return Sk_Db_Query_Builder_Expression
     */
    public abstract function add_subexp(array $subexp, $delimiter = ', ');

    /**
     * 编译一个子表达式
     * @param unknown $subexp
     * @return string
     */
    public abstract function compile_subexp($subexp);
	
	public function __toString()
	{
		return $this->compile();
	}
	
	public function _int($value)
	{
		return (int) $value;
	}
	
	public function _str($value)
	{
		return $value;
	}
	
	public function _table($table)
	{
		return $this->_db->quote_table($table);
	}
	
	public function _column($column)
	{
		return $this->_db->quote_column($column);
	}
	
	public function _value($value)
	{
		return $this->_db->quote($value);
	}
	
	public function _order_direction($value)
	{
		if($value !== NULL){
			$value = strtoupper($value);
			if (in_array($value, array('ASC', 'DESC')))
				return $value;
		}
		return NULL;
	}
	
	public function _join_type($value)
	{
		if($value !== NULL){
			$value = strtoupper($value);
			if (in_array($value, array('LEFT', 'RIGHT', 'INNER')))
				return $value;
		}
		return NULL;
	}
	
}