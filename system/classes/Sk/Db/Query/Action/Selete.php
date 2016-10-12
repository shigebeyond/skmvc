<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- select
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action_Selete extends Db_Query_Action
{
	/**
	 * sql动作: select
	 * @var string
	 */
	protected $_action = 'SELECT :keys FROM :table';
	
	/**
	 * 设置查询的字段
	 *
	 * @param string... $columns
	 * @return Db_Query_Action_Selete
	 */
	public function select($columns)
	{
		return $this->data(func_get_args());
	}
	
	/**
	 * 编译多个字段名: 转义
	 * @return string
	 */
	public function compile_columns()
	{
		if(empty($this->_data))
			return '*';
	
		return $this->_db->quote_column($this->_data, ', ', NULL, NULL);
	}
}