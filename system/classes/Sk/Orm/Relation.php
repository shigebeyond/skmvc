<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之关联关系
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34 
 *
 */
class Sk_Orm_Relation extends Orm_Persistent
{
	/**
	 * 关联关系 - 有一个
	 * @var array
	 */
	protected static $_has_one = array();
	
	/**
	 * 关联关系 - 从属于
	 * @var array
	*/
	protected static $_belongs_to = array();
	
	/**
	 * 关联关系 - 有多个
	 * @var array
	*/
	protected static $_has_many = array();
	
	//重写try_get/try_set
	
	//关联查询
}