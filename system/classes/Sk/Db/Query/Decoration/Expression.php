<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Decoratoin_Expression
{
	/**
	 * 多行
	 * @var array
	 */
	protected $_rows = array();
	
	/**
	 * 每列的处理器
	 * @var array
	 */
	protected $_column_handlers;
	
	/**
	 * 合并多行时的分隔符
	 * @var string
	 */
	protected $_delimiter;
	
	public function __construct(array $column_handler, $delimiter = ', ')
	{
		$this->_column_handlers = $column_handler;
		$this->_delimiter = $delimiter; 
	}
	
	/**
	 * 添加一行数据
	 * @param unknown $row
	 * @return Sk_Db_Query_Expression
	 */
	public function add_row($row)
	{
		$this->_rows[] = $row;
		return $this;
	}
	
	/**
	 * 编译多行
	 * @return string
	 */
	public function compile()
	{
		if (empty($this->_rows))
			return NULL;
		
		// 逐行编译+合并
		return implode($this->_delimiter, array_map(array($this, 'compile_row'), $this->_rows));
	}
	
	/**
	 * 编译单行
	 * @param unknown $row
	 * @return string
	 */
	public function compile_row($row)
	{
		// 1 处理一列
		if (!is_array($row)) {
			// 获得处理函数
			$handler = Arr::get($this->_column_handlers, 0);
			if($handler)
				return $this->{"_$handler"}($row);  // 处理该值
			return $row;
		}
		
		// 2 处理多列
		// 遍历处理每一列
		foreach ($this->_column_handlers as $column => $handler)
		{
			// 处理某行某列的值
			$value = Arr::get($row, $column); // 值
			$row[$column] = $this->{"_$handler"}($value); // 处理该值
		}
		
		return implode(' ', $row); // 用空格拼接多列
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