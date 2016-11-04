<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Odm
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
}