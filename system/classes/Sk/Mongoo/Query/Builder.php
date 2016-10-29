<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Mongodb查询构建器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-28 下午10:18:27 
 *
 */
class Sk_Mongoo_Query_Builder
{
	/**
	 * 数据库连接
	 * @var string|MongoClient|Mongoo
	 */
	protected $_db;
	
	/**
	 * 当前查询的集合
	 * @var  string
	 */
	protected $_collection;
	
	/**
	 * 要插入/更新字段: <column => value>
	 * 要查询的字段名: [column]
	 * @var  array
	 */
	protected $_data = array();
	
	/**
	 * 查询条件
	 * @var  array
	*/
	public $_wheres = array();
	
	/**
	 * 排序字段
	 * @var  array
	*/
	protected $_sort = array();
	
	/**
	 * 限制结果集行数
	 * @var  int
	*/
	protected $_limit = 0;
	
	/**
	 * 结果集的偏移量
	 * @var  int
	 */
	protected $_skip = 0;
	
	/**
	 * 构造函数
	 *
	 * @param string|Db $db 数据库配置的分组名/数据库连接
	 * @param string $collection 
	 * @param string $data 数据
	 */
	public function __construct($db = 'default', $collection = NULL, $data = NULL)
	{
		// 获得db
		if (! $db instanceof Mongoo)
			$db = Mongoo::instance ( $db );
		$this->_db = $db;
	
		if ($collection)
			$this->collection ( $collection );
	
		if ($data)
			$this->data ( $data );
	}
	
	/**
	 * 清空条件
	 * @return Mongoo_Query_Builder
	 */
	public function clear()
	{
		$this->_data = $this->_wheres = $this->_sort = array();
		$this->_limit = $this->_skip = 0;
		return $this;
	}
	
	/**
	 * 设置当前查询的集合
	 * 
	 * @param string $collection
	 * @return Mongoo_Query_Builder
	 */
	public function collection($collection)
	{
		$this->_collection = $collection;
		return $this;
	}
	
	/**
	 * 设置查询的字段
	 * 
	 * @param array $columns
	 * @return Mongoo_Query_Builder
	 */
	public function select(array $columns)
	{
		foreach ($columns as $col)
			$this->_data[$col] = 1;
		return $this;
	}
	
	/**
	 * 设置插入/更新的值
	 *
	 * @param array $data
	 * @param　bool $partial 是否部分更新
	 * @return Db_Query_Builder
	 */
	public function data(array $data, $partial = FALSE)
	{
		$this->_data = $partial ? array('$set' => $data) : $data;
		return $this;
	}
	
	/**
	 * 设置插入/更新的值
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Builder
	 */
	public function set($column, $value)
	{
		Arr::set_path($this->_data, array('$set', $column), $value);
		return $this;
	}
	
	/**
	 *	$and多个条件
	 *
	 *	@param	array $wheres		
	 *	@return	 Mongoo_Query_Builder
	 */
	public function wheres($wheres = array())
	{
		$this->_wheres += $wheres;
		return $this;
	}
	
	/**
	 * $and一个条件
	 * 
	 * @param string $column
	 * @param string $op
	 * @param string $value
	 * @return Mongoo_Query_Builder
	 */
	public function where($column, $op, $value = NULL)
	{
		if($value === NULL && $op[0] !== '$') // 无运算符
			$this->_wheres[$column] = $op;
		else // 有运算符
			Arr::set_path($this->_wheres, array($column, $op), $value);
		return $this;
	}
	
	/**
	 *	$or条件
	 *
	 *	@param	array	 $where
	 *	@return Mongoo_Query_Builder
	 */
	public function or_where($column, $op, $value = NULL)
	{
		if($value === NULL && $op[0] !== '$') // 无运算符
		{
			$path = array('$or', $column, '');
			$value = $op;
		}
		else // 有运算符
			$path = array('$or', $column, $op);;
		
		Arr::set_path($this->_wheres, $path, $value);
		return $this;
	}
	
	/**
	 * between条件
	 * 
	 * @param string $column
	 * @param mixed $min
	 * @param mixed $max
	 * @return Mongoo_Query_Builder
	 */
	public function where_between($column, $min, $max)
	{
		return $this->where($column, '$gte', $min)->where($column, '$lte', $max);
	}
	
	/**
	 * 正则匹配
	 * 
	 * @param string $column
	 * @param string $regex
	 * @return Mongoo_Query_Builder
	 */
	public function like($column, $regex)
	{
		return $this->where($column, new MongoRegex($regex));
	}
	
	/**
	 * 排序 
	 * 
	 * @param string $column
	 * @param number $direction
	 * @return Mongoo_Query_Builder
	 */
	public function order_by($column, $direction = 1)
	{
		$this->_sort[$column] = $direction;
		return $this;
	}
	
	/**
	 *	限制结果集的行数
	 *
	 *	@param	int $limit
	 *	@param	int $offset
	 *	@return Mongoo_Query_Builder
	 */
	public function limit($limit, $offset = 0)
	{
		$this->_limit = (int)$limit;
		$this->_skip = (int)$offset;
		return $this;
	}
	
	/**
	 * 查找一个
	 * @return object
	 */
	public function find()
	{
		return $this->_db->{$this->_collection}->findOne($this->_wheres, $this->_data);
	}
	
	/**
	 * 查找多个
	 * @return array
	 */
	public function find_all()
	{
		//　获得游标
		$cursor = $this->_db->{$this->_collection}->find($this->_wheres, $this->_data);
		//　限制游标
		foreach (array('limit', 'skip', 'sort') as $name)
		{
			$value = $this->{"_$name"};
			if($value)
				$cursor->$name($value);
		}
		
		return $cursor;
	}
	
	/**
	 * 统计行数
	 * 
	 * @param boolean $apply_skip_limit
	 * @return int
	 */
	public function count($apply_skip_limit = FALSE)
	{
		return $this->find_all()->count($apply_skip_limit);
	}
	
	/**
	 * 插入
	 * @return MongoId|boolean
	 */
	public function insert()
	{
		try
		{
			$this->_db->{$this->_collection}->insert($this->_data, array('fsync' => true));
			$this->clear();
			return Arr::get($this->_data, '_id', FALSE);
		}
		catch (MongoCursorException $e)
		{
			throw new Db_Exception("插入Mongodb的数据出错: {$e->getMessage()}", $e->getCode(), $e);
		}
	}

	
	/**
	 *	更新
	 *	@return	bool
	 */
	public function update()
	{
		try
		{
			$this->_db->{$this->_collection}->update($this->_where, $this->_data, array('fsync' => true, 'multiple' => true));
			$this->clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			throw new Db_Exception("更新Mongodb的数据出错: {$e->getMessage()}", $e->getCode(), $e);
		}
	}
	
	/**
	 *	删除
	 *	@return	bool
	 */
	public function delete()
	{
		try
		{
			$this->_db->{$this->_collection}->remove($this->_where, array('fsync' => true, 'justOne' => false));
			$this->clear();
			return true;
		}
		catch (MongoCursorException $e)
		{
			throw new Db_Exception("删除Mongodb的数据出错: {$e->getMessage()}", $e->getCode(), $e);
		}
	}
}