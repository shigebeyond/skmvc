<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ODM
 * 	实现：
 *     重用ORM的代码，只是变动部分属性 $_primary_key / 方法 db()
 *     重用关系型数据的概念，mongo的collection对应db的table
 *  
 *  关系型数据库与mongodb的3个层次的兼容：
 *    1 Db层：Db 与 Mongoo 不用兼容
 *    2 Query_Builder层：Db_Query_Builder 与 Mongoo_Query_Builder 尽量兼容
 *    3 ORM层：ORM 与 ODM 完全兼容，终极目标
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-11-5 上午1:23:05 
 *
 */
class Sk_Odm extends Orm_Related
{
	/**
	 * 主键
	 *     默认一样, 基类给默认值, 子类可自定义
	 * @var string
	 */
	protected static $_primary_key = '_id';
	
	/**
	 * 获得数据库
	 * @param string $action 命令动作：find/findOne/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Mongoo
	 */
	public static function db($action = 'find')
	{
		if(!static::$_db instanceof Db)
			static::$_db = Mongoo::instance(static::$_db);
	
		return static::$_db;
	}
	
	/**
	 * 获得查询构建器
	 * @return Odm_Query_Builder
	 */
	public static function query_builder()
	{
		return new Odm_Query_Builder(get_called_class());
	}
}