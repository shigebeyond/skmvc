<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- update
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Builder_Update extends Db_Query_Builder
{
	/**
	 * 要更新的值: <field => value>
	 * @var array
	 */
	protected $_values = array();
	
	/**
	 * 设置更新的值
	 * 
	 * @param string $name
	 * @param string $value
	 * @return Sk_Db_Query_Builder_Update
	 */
	public function set($name, $value)
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
	 * 编译set子句
	 * @return string
	 */
	public function compile_set()
	{
		$set = '';
		foreach ($this->_values as $key => $value)
		{
			$set = "$key = $value, ";
		}
		return rtrim($set, ', ');
	}
	
	/**
	 * 编译sql
	 * @see Sk_Db_Query_Builder::compile()
	 */
	public function compile()
	{
		// set子句
		$set = $this->compile_set();
		// update
		return "UPDATE `$this->_table` SET $set WHERE ".$this->compile_where();
	}
}