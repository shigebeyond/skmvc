<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之数据校验
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-10
 *
 */
interface Interface_Orm_Valid
{
	/**
	 * 校验数据
	 * @return bool
	 */
	public function check();
	
}
