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
class Sk_Mongoo extends MongoClient
{
	/**
	 * 配置信息
	 * @var array
	 */
	protected $_config;
	
	/**
	 * 组件名
	 * @var string
	 */
	protected $_name;
	
	/**
	 *  获得db单例
	 * @param string $group 数据库配置的分组名
	 * @return Db
	 */
	public static function instance($group = 'default')
	{
		return Container::component_config('Mongoo', $group);
	}

	/**
	 * 接收配置信息
	 * 
	 * @param array $config
	 * @param string $name
	 */
	public function __construct($config, $name)
	{
		parent::__construct($config['server'], $config['options']);
		
		$this->_config = $config;
		$this->_name = $name;
	}
	
	/**
	 * Mongodb查询构建器
	 *
	 * @param string $collection 集合名
	 * @param string $data 数据
	 * @return Mongoo_Query_Builder
	 */
	public function query_builder($collection = NULL, $data = NULL)
	{
		return new Mongoo_Query_Builder($this, $collection, $data);
	}
}
