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
abstract class Sk_Orm_MetaData extends Orm_Entity
{
	//todo： 抽取单独的metadata类，跟当前类类似
	// 当前类直接代理metadata类的方法
	// 当前类缓存model类名与medata的映射
	
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
	 * 缓存所有model类的表名: <类名 => 表名>
	 * @var string
	 */
	protected static $_class_tables = array();
	
	/**
	 * 自定义的表字段
	 *     默认不一样, 基类不能给默认值, 但子类可自定义
	 * @var array
	 */
	//protected static $_columns;
	
	/**
	 * 缓存所有model类的字段列表: <类名 => 字段列表>
	 * @var string
	 */
	protected static $_class_columns = array();
	
	/**
	 * 主键
	 *     默认一样, 基类给默认值, 子类可自定义
	 * @var string
	 */
	protected static $_primary_key = 'id';
	
	/**
	 * 获得数据库
	 * @param string $action sql动作：select/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Db
	 */
	public static function db($action = 'select')
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
		if(static::$_name === NULL)
			static::$_name = strtolower(substr(get_called_class(), 6));
		
		return static::$_name;
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
		if (!isset(static::$_class_tables[$class]))
		{
			if (property_exists($class, '_table')) // 自定义表名
				static::$_class_tables[$class] = static::$_table;
			else // 默认表名 = 模型名
				static::$_class_tables[$class] = static::name();
		}
	
		return static::$_class_tables[$class];
	}
	
	
	/**
	 * 获得字段列表
	 * @return array
	 */
	public static function columns()
	{
		$class = get_called_class();
		
		// 先查缓存
		if (!isset(static::$_class_columns[$class]))
		{
			if (property_exists($class, '_columns')) // 自定义字段列表
				static::$_class_columns[$class] = $class::$_columns;
			else // 默认字段列表 = 直接查db
				static::$_class_columns[$class] = static::db()->list_columns(static::table());
		}
	
		return static::$_class_columns[$class];
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