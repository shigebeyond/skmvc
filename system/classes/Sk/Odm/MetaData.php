<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Odm之元数据
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-29 下午9:08:12 
 *
 */
abstract class Sk_Odm_MetaData extends Orm_Valid implements Interface_Odm_MetaData
{
	/****************** 部分元数据有不一样的默认值, 不能在基类定义 => 默认值不能保存在类结构中, 因此只能缓存默认值 ********************/
	/**
	 * 缓存所有model类的集合名: <类名 => 集合名>
	 * @var string
	 */
	protected static $_collections_cached = array();
	
	/****************** 部分元数据有一样的默认值, 可在基类定义 => 默认值直接保存在类结构中 ********************/	
	/**
	 * 数据库
	 * 	默认一样, 基类给默认值, 子类可自定义
	 * @var Db
	 */
	protected static $_db = 'default';
	
	/**
	 * 自定义的集合名
	 *     默认不一样, 基类不能给默认值, 子类可自定义
	 * @var string
	 */
	//protected static $_collection;
	
	/**
	 * 获得数据库
	 * @param string $action sql动作：select/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Mongoo
	 */
	public static function db($action = 'select')
	{
		if(!static::$_db instanceof Db)
			static::$_db = Mongoo::instance(static::$_db);
		
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
	 * 获得集合名
	 * @return  string
	 */
	public static function collection()
	{
		$class = get_called_class();
	
		// 先查缓存
		if (!isset(static::$_collections_cached[$class]))
		{
			if (property_exists($class, '_collection')) // 自定义集合名
				static::$_collections_cached[$class] = static::$_collection;
			else // 默认集合名 = 模型名
				static::$_collections_cached[$class] = static::name();
		}
	
		return static::$_collections_cached[$class];
	}
	
	/**
	 * 获得主键值
	 * @return int|string
	 */
	public function pk()
	{
		$this->try_get('_id', $value);
		return $value;
	}
}