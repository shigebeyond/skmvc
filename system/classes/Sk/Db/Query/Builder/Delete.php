<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- delete
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Builder_Delete extends Sk_Db_Query_Builder
{
	/**
	 * 编译sql
	 * @see Sk_Db_Query_Builder::compile()
	 */
	public function compile()
	{
		return "DELETE FROM `$this->_table` WHERE ".$this->compile_where();
	}
}