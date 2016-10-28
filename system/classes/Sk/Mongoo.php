<?php

class Mongoo extends Container_Component_Configurable
{
	/**
	 *  获得db单例
	 * @param string $group 数据库配置的分组名
	 * @return Db
	 */
	public static function instance($group = 'default')
	{
		return Container::component_config('Db', $group);
	}
	
	/**
	 * 获得mongo驱动类
	 * @return string|boolean
	 */
	public static function mongo_driver()
	{
		if(class_exists('MongoClient'))
			return 'MongoClient';
		
		if(class_exists('Mongo'))
			return 'Mongo';
		
		throw new Exception("未安装mongo驱动");
	}
	
	/**
	 * Mongo连接
	 * @var  MongoClient
	 */
	protected $_conn;

	public function __construct($config, $name)
	{
		parent::__construct($config, $name);
		
		// 获得mongo驱动类
		$class = static::mongo_driver();
		$this->_conn = new $class($config['server'], $config['options']);
	}

	/**
	 * 插入
	 * @param string $collection 集合名
	 * @param array $data
	 * @throws Exception
	 * @return MongoId|boolean
	 */
	public function insert($collection, array $data)
	{
		try
		{
			$this->db->{$collection}->insert($data, array('fsync' => true));
			return Arr::get($data, '_id', FALSE);
		}
		catch (MongoCursorException $e)
		{
			throw new Exception("Insert of data into MongoDB failed: {$e->getMessage()}", $e->getCode());
		}
	}

	/**
	 * 更新
	 * @param string $collection
	 * @param array $data
	 * @param $options $options
	 * @param string $partial 是否部分更新
	 * @throws Exception
	 * @return boolean
	 */
	public function update($collection, array $criteria, array $data, array $options = array(), $partial = false)
	{
		try
		{
			$options = array_merge($options, array('fsync' => true, 'multiple' => false));
			$this->db->{$collection}->update($criteria, (($partial) ? array('$set' => $data): $data), $options);
			return true;
		}
		catch (MongoCursorException $e)
		{
			throw new Exception("Update of data into MongoDB failed: {$e->getMessage()}", $e->getCode());
		}
	}

	/**
	 *	Updates a collection of documents
	 *
	 *	@param	string	$collection		the collection name
	 *	@param	array	$data			an associative array of values, array(field => value)
	 *	@param	bool	$literal
	 *	@return	bool
	 *	@throws	Exception
	 *	@usage	$mongodb->update_all('foo', $data = array());
	 */
	public function update_all($collection = "", array $criteria, $data, $literal = false)
	{
		try
		{
			$this->db->{$collection}->update($criteria, (($literal) ? $data : array('$set' => $data)), array('fsync' => true, 'multiple' => true));

			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			throw new Exception("Update of data into MongoDB failed: {$e->getMessage()}", $e->getCode());
		}
	}

	/**
	 *	Delete a document from the passed collection based upon certain criteria
	 *
	 *	@param	string	$collection		the collection name
	 *	@return	bool
	 *	@throws	Exception
	 *	@usage	$mongodb->delete('foo');
	 */
	public function delete($collection, $criteria)
	{
		try
		{
			$this->db->{$collection}->remove($criteria, array('fsync' => true, 'justOne' => true));
			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			throw new Exception("Delete of data into MongoDB failed: {$e->getMessage()}", $e->getCode());
		}
	}

	/**
	 *	Delete all documents from the passed collection based upon certain criteria.
	 *
	 *	@param	string	$collection		the collection name
	 *	@return	bool
	 *	@throws	Exception
	 *	@usage	$mongodb->delete_all('foo');
	 */
	public function delete_all($collection, $criteria)
	{
		try
		{
			$this->db->{$collection}->remove($criteria, array('fsync' => true, 'justOne' => false));

			$this->_clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			throw new Exception("Delete of data from MongoDB failed: {$e->getMessage()}", $e->getCode());
		}
	}
	
}
