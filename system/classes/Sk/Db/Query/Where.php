<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- action
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query_Where extends Db_Query 
{
	/**
	 * 编译where子句
	 * @see Sk_Db_Query::compile_where()
	 */
	public function compile_where() 
	{
		return NULL;
	}

	
}