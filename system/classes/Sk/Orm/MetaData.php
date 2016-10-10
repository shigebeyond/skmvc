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
class Sk_Orm_MetaData extends Orm_Entity
{
	/**
	 * 数据库
	 * @var string
	 */
	protected static $_db;
	
	/**
	 * 对象名
	 * @var string
	 */
	protected static $_name;
	
	/**
	 * 表名
	 * @var string
	 */
	protected static $_table;
	
	/**
	 * 表字段
	 * @var array
	 */
	protected static $_columns;
	
	/**
	 * 主键
	 * @var string
	 */
	protected $_primary_key = 'id';
	
	/**
	 * 获得数据库
	 * @return Database
	 */
	public static function db()
	{
		if(!static::$_db instanceof Database)
			static::$_db = Database::instance(static::$_db);
		
		return static::$_db;
	}
	
	/**
	 * 获得对象名
	 * @return string
	 */
	public static function name()
	{
		if(static::$_name === NULL)
			static::$_name = strtolower(substr(get_called_class(), 6));
		
		return static::$_name;
	}
	
	/**
	 * 获得表名
	 * @return string
	 */
	public static function table()
	{
		if(static::$_table === NULL)
		{
			// TODO 支持复数
			static::$_table = static::name();
		}
		
		return static::$_table;
	}
	
	/**
	 * 获得字段列表
	 * @return array
	 */
	public static function columns()
	{
		if(static::$_columns === NULL)
			static::$_columns = static::db()->list_columns(static::table()); 
		
		return static::$_columns;
	}
}