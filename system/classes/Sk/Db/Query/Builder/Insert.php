<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- insert
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Builder_Insert extends Db_Query_Builder
{
	/**
	 * 要插入的值: <column => value>
	 * @var array
	 */
	protected $_values = array();
	
	/**
	 * 设置插入的值
	 * 
	 * @param string $name
	 * @param string $value
	 * @return Sk_Db_Query_Builder_Insert
	 */
	public function value($name, $value)
	{
		if(is_array($name))
		{
			$this->_values = $name;
		}
		else 
		{
			$this->_values[$name] = $value;
		}
		
		return $this;
	}
	
	/**
	 * 编译sql
	 * @see Sk_Db_Query_Builder::compile()
	 */
	public function compile()
	{
		// 字段
		$columns = implode(array_keys($this->_values), ', ');
		// 字段值
		$values = $this->quote_value(array_values($this->_values));
		// insert
		return "INSERT INTO `$this->_table` $columns VALUES $values";
	}
}