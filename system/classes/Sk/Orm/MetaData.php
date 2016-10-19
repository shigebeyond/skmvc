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
	 * @var Db
	 */
	protected static $_db = 'default';
	
	/**
	 * 模型名/对象名
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
	 * Get the table name for this class
	 *
	 * @return  string
	 */
	public static function table()
	{
		$class = get_called_class();
	
		// Table name unknown
		if ( ! array_key_exists($class, static::$_table_names_cached))
		{
			// Table name set in Model
			if (property_exists($class, '_table_name'))
			{
				static::$_table_names_cached[$class] = static::$_table_name;
			}
			else
			{
				static::$_table_names_cached[$class] = \Inflector::tableize($class);
			}
		}
	
		return static::$_table_names_cached[$class];
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
	 * Get the class's properties
	 *
	 * @throws \FuelException Listing columns failed
	 *
	 * @return  array
	 */
	public static function properties()
	{
		$class = get_called_class();
	
		// If already determined
		if (array_key_exists($class, static::$_properties_cached))
		{
			return static::$_properties_cached[$class];
		}
	
		// Try to grab the properties from the class...
		if (property_exists($class, '_properties'))
		{
			$properties = static::$_properties;
			foreach ($properties as $key => $p)
			{
				if (is_string($p))
				{
					unset($properties[$key]);
					$properties[$p] = array();
				}
			}
		}
	
		// ...if the above failed, run DB query to fetch properties
		if (empty($properties))
		{
			try
			{
				$properties = \DB::list_columns(static::table(), null, static::connection());
			}
			catch (\Exception $e)
			{
				throw new \FuelException('Listing columns failed, you have to set the model properties with a '.
						'static $_properties setting in the model. Original exception: '.$e->getMessage());
			}
		}
	
		// cache the properties for next usage
		static::$_properties_cached[$class] = $properties;
	
		return static::$_properties_cached[$class];
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