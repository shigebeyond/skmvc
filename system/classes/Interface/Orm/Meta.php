<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之元数据
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10
 *
 */
interface Interface_Orm_Meta
{
	/**
	 * 获得数据库
	 * @return Db
	 */
	public static function db();
	
	/**
	 * 获得模型名
	 *    假定model类名, 都是以"Model_"作为前缀
	 *    
	 * @return string
	 */
	public static function name();
	
	/**
	 * 获得表名
	 * 
	 * @return  string
	 */
	public static function table();
	
	
	/**
	 * 获得字段列表
	 * @return array
	 */
	public static function columns();
		
	/**
	 * 获得主键
	 * @return string
	 */
	public static function primary_key();
	
	/**
	 * 获得主键值
	 * @return int|string
	 */
	public function pk();
}