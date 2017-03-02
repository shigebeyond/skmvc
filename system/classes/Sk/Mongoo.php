<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 封装mongodb连接
 *     代理 MongoClient
 * 
 *  关系型数据库与mongodb的3个层次的兼容：
 *    1 Db层：Db 与 Mongoo 不用兼容
 *    2 Query_Builder层：Db_Query_Builder 与 Mongoo_Query_Builder 尽量兼容
 *    3 ORM层：ORM 与 ODM 完全兼容，终极目标
 *  
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-28 下午9:44:43 
 *
 */
class Sk_Mongoo extends Container_Component_Configurable implements Interface_Mongoo
{
	/**
	 *  获得Mongodb连接单例
	 *  
	 * @param string $group 数据库配置的分组名
	 * @return Mongoo
	 */
	public static function instance($group = 'default')
	{
		return Container::component_config('Mongoo', $group);
	}
	
	/**
	 * Mongodb连接
	 * @var MongoClient
	 */
	protected $_conn;
	
	/**
	 * Mongodb的db
	 * @var MongoDB
	 */
	protected $_db;
	
	public function __construct($config, $name)
	{
		parent::__construct($config, $name);
		
		// 创建mongodb连接
		try{
			$server = $this->_config['server'];
			$options = $this->_config['options'];
			$this->_conn = new MongoClient($server, $options); //　创建连接
			$this->_db = $this->_conn->selectDB($options['db']); // 选择db
		}
		catch (MongoConnectionException $e)
		{
			throw new Db_Exception("不能连接Mongodb: {$e->getMessage()}", $e->getCode(), $e);
		}
	}
	
	/**
	 * 是否调试
	 * @return bool
	 */
	public function is_debug()
	{
		return Arr::get($this->_config, 'config', FALSE);
	}
	
	/**
	 * 选择集合
	 * @return MongoCollection
	 */
	public function __get($collectionName) 
	{
		return $this->_db->selectCollection($collectionName);
// 		return new MongoCollection($this->_db, $collectionName);
	}
	
	/**
	 * Mongodb查询构建器
	 *
	 * @param string $collection 集合名
	 * @return Mongoo_Query_Builder
	 */
	public function query_builder($collection = NULL)
	{
		return new Mongoo_Query_Builder($this, $collection);
	}
}
