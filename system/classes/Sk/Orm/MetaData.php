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
	 * @var Db
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
	 * @param string $action sql动作：select/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Db
	 */
	public static function db($action)
	{
		if(!static::$_db instanceof Db)
			static::$_db = Db::instance(static::$_db);
		
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
	
	/**
	 * 获得sql构建器
	 * 
	 * @param string $action
	 * @return Orm_Query_Builder
	 */
	public static function query_builder($action = 'select')
	{
		return new Orm_Query_Builder($action, get_called_class());
	}
}