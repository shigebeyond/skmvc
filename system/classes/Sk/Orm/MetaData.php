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
abstract class Sk_Orm_MetaData extends Orm_Valid implements Interface_Orm_MetaData
{
	/****************** 部分元数据有不一样的默认值, 不能在基类定义 => 默认值不能保存在类结构中, 因此只能缓存默认值 ********************/
	/**
	 * 缓存所有model类的表名: <类名 => 表名>
	 * @var string
	 */
	protected static $_tables_cached = array();
	
	/**
	 * 缓存所有model类的字段列表: <类名 => 字段列表>
	 * @var string
	 */
	protected static $_columns_cached = array();
	
	/****************** 部分元数据有一样的默认值, 可在基类定义 => 默认值直接保存在类结构中 ********************/	
	/**
	 * 数据库
	 * 	默认一样, 基类给默认值, 子类可自定义
	 * @var Db
	 */
	protected static $_db = 'default';
	
	/**
	 * 自定义的表名
	 *     默认不一样, 基类不能给默认值, 子类可自定义
	 * @var string
	 */
	//protected static $_table;
	
	/**
	 * 自定义的表字段
	 *     默认不一样, 基类不能给默认值, 但子类可自定义
	 * @var array
	 */
	//protected static $_columns;
	
	/**
	 * 主键
	 *     默认一样, 基类给默认值, 子类可自定义
	 * @var string
	 */
	protected static $_primary_key = 'id';
	
	/**
	 * 获得数据库
	 * @return Db
	 */
	public static function db()
	{
		if(!static::$_db instanceof Db)
			static::$_db = Db::instance(static::$_db);
		
		return static::$_db;
	}
	
	/**
	 * 获得模型名
	 *    假定model类名, 都是以"Model_"作为前缀
	 *    
	 * @return string
	 */
	public static function name()
	{
		return strtolower(substr(get_called_class(), 6));
	}
	
	/**
	 * 获得表名
	 * 
	 * @return  string
	 */
	public static function table()
	{
		$class = get_called_class();
	
		// 先查缓存
		if (!isset(static::$_tables_cached[$class]))
		{
			if (property_exists($class, '_table')) // 自定义表名
				static::$_tables_cached[$class] = static::$_table;
			else // 默认表名 = 模型名
				static::$_tables_cached[$class] = static::name();
		}
	
		return static::$_tables_cached[$class];
	}
	
	/**
	 * 判断是否有某字段
	 *
	 * @param string $column
	 * @return
	 */
	public static function has_column($column)
	{
		return array_key_exists($column, static::columns());
	}
	
	/**
	 * 获得字段列表
	 * @return array
	 */
	public static function columns()
	{
		$class = get_called_class();
		
		// 先查缓存
		if (!isset(static::$_columns_cached[$class]))
		{
			if (property_exists($class, '_columns')) // 自定义字段列表
				static::$_columns_cached[$class] = $class::$_columns;
			else // 默认字段列表 = 直接查db
				static::$_columns_cached[$class] = static::db()->list_columns(static::table());
		}
	
		return static::$_columns_cached[$class];
	}
	
	/**
	 * 获得主键
	 * @return string
	 */
	public static function primary_key()
	{
		return static::$_primary_key;
	}
	
	/**
	 * 获得主键值
	 * @return int|string
	 */
	public function pk()
	{
		$this->try_get(static::$_primary_key, $value);
		return $value;
	}
}