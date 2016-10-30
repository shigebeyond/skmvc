<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 封装mongodb连接
 * 	１　扩展MongoClient
 * 	２　读取配置
 * 	３　单例
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-28 下午9:44:43 
 *
 */
class Sk_Mongoo extends Container_Component_Configurable
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
     * 选择集合
     * @return MongoCollection
     */
    public function __get($collectionName)
    {
        return $this->_db->selectCollection($collectionName);
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
