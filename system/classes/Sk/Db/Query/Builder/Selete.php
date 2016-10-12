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
class Sk_Db_Query_Builder_Selete extends Sk_Db_Query_Builder
{
	protected $_columns = array();
	
	/**
	 * 设置查询的字段
	 *
	 * @param string... $columns
	 * @return Sk_Db_Query_Builder_Selete
	 */
	public function select($columns)
	{
		$this->_columns = func_get_args();;
		return $this;
	}
	
	/**
	 * 编译sql
	 * @see Sk_Db_Query_Builder::compile()
	 */
	public function compile()
	{
		// 字段
		$columns = $this->_columns;
		if(is_array($columns))
			$columns = implode($columns, ', ');
		// select
		return "SELECT $columns FROM `$this->_table` WHERE ".$this->compile_where();
	}
}