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
class Sk_Db_Query_Builder_Decoratoin_Expression
{
	/**
	 * 多个子表达式, 可视为行
	 * @var array
	 */
	protected $_subexps = array();
	
	/**
	 * 每个元素的处理器, 可视为列的处理
	 * @var array
	 */
	protected $_element_handlers;
	
	/**
	 * 合并多个子表达式时的分隔符
	 * @var string
	 */
	protected $_delimiter;
	
	public function __construct(array $element_handler, $delimiter = ', ')
	{
		$this->_element_handlers = $element_handler;
		$this->_delimiter = $delimiter; 
	}
	
	/**
	 * 添加一个子表达式数据
	 * @param string|array $row
	 * @return Sk_Db_Query_Builder_Expression
	 */
	public function add_subexp($row)
	{
		$this->_subexps[] = $row;
		return $this;
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
		return implode($this->_delimiter, array_map(array($this, 'compile_row'), $this->_subexps));
	}
	
	/**
	 * 编译一个子表达式
	 * @param unknown $subexp
	 * @return string
	 */
	public function compile_subexp($subexp)
	{
		// 1 处理一个元素
		if (!is_array($subexp)) {
			// 获得处理函数
			$handler = Arr::get($this->_element_handlers, 0);
			if($handler)
				return $this->{"_$handler"}($subexp);  // 处理该值
			return $subexp;
		}
		
		// 2 处理多个元素
		// 遍历处理每一个元素
		foreach ($this->_element_handlers as $i => $handler)
		{
			// 处理某个元素的值
			$value = Arr::get($subexp, $i); // 值
			$subexp[$i] = $this->{"_$handler"}($value); // 处理该值
		}
		
		return implode(' ', $subexp); // 用空格拼接多个元素
	}
	
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