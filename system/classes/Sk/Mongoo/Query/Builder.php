<?php defined('SYSPATH') OR die('No direct script access.');

class Sk_Mongoo_Query_Builder
{
	/**
	 * 数据库连接
	 * @var  MongoClient
	 */
	protected $_db;
	
	/**
	 * 当前查询的集合
	 * @var  string
	 */
	protected $_collection;
	
	/**
	 * 查询的字段
	 * @var  array
	 */
	protected $_select = array();
	
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
	 * 清空条件
	 * @return Mongoo_Query_Builder
	 */
	public function clear()
	{
		$this->_select	= $this->_wheres = $this->_sort = array();
		$this->_limit	= $this->_skip = 0;
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
			$this->_select[$col] = 1;
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
			Arr::set_path($this->_wheres, "$column.$op", $value);
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
			$path = "\$or.$column.";
			$value = $op;
		}
		else // 有运算符
			$path = "\$or.$column.$op";
		
		Arr::set_path($this->_wheres, $path, $value);
		return $this;
	}
	
	/**
	 *	$in条件
	 *
	 *	@param	string	$column	 字段名
	 *	@param	array	$values		多值
	 *	@return Mongoo_Query_Builder
	 */
	public function where_in($column, array $values)
	{
		return $this->where($column, '$in', $values);
	}
	
	/**
	 *	$all条件
	 *
	 *	@param	string	$column	 字段名
	 *	@param	array	$values	 多值
	 *	@return Mongoo_Query_Builder
	 */
	public function where_all($column, array $values)
	{
		return $this->where($column, '$all', $values);
	}
	
	/**
	 *	$nin条件
	 *
	 *	@param	string	$column	 字段名
	 *	@param	array	$values		多值
	 *	@return Mongoo_Query_Builder
	 */
	public function where_not_in($column, array $values)
	{
		return $this->where($column, '$nin', $values);
	}
	
	/**
	 * $gt条件: >
	 * 
	 *	@param	string $column
	 *	@param	mixed $value
	 *	@return Mongoo_Query_Builder
	 */
	public function where_gt($column, $value)
	{
		return $this->where($column, '$gt', $value);
	}
	
	/**
	 *	$gte条件: >=
	 *
	 *	@param	string $column
	 *	@param	mixed $value
	 *	@return 	Mongoo_Query_Builder
	 */
	public function where_gte($column = '', $value)
	{
		return $this->where($column, '$gte', $value);
	}
	
	/**
	 *	$lt条件: <
	 *
	 *	@param	string $column
	 *	@param	mixed $value
	 *	@return	 Mongoo_Query_Builder
	 */
	public function where_lt($column, $value)
	{
		return $this->where($column, '$lt', $value);
	}
	
	/**
	 *	$lte条件: <=
	 *
	 *	@param	string $column
	 *	@param	mixed $value
	 *	@return	Mongo_Db
	 */
	public function where_lte($column, $value)
	{
		return $this->where($column, '$lte', $value);
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
	 * not between条件
	 * 
	 * @param string $column
	 * @param mixed $min
	 * @param mixed $max
	 * @return Mongoo_Query_Builder
	 */
	public function where_between_ne($column, $min, $max)
	{
		return $this->where($column, '$gt', $max)->where($column, '$lt', $min);
	}
	
	/**
	 *	$ne条件: !=
	 *
	 *	@param	string $column
	 *	@param	mixed $value
	 *	@return	 Mongoo_Query_Builder
	 */
	public function where_ne($column, $value)
	{
		return $this->where($column, '$ne', $value);
	}
	
	/**
	 *	$near条件: 获得某个经纬度附近的数据, 但是需要建地理空间的索引(geospatial index)
	 *
	 * <code>
	 * 		$query->where_near('place', array('50','50'))->get('address');
	 * </code>
	 * 
	 *	@param	string	$column		the field name
	 *	@param	array	$coordinates	 经纬度: array(经度, 纬度)
	 *	@return Mongoo_Query_Builder
	 */
	public function where_near($column, array $coordinates)
	{
		$this->_where_init($column);
		$this->where[$column]['$near'] = $coordinates;
		return $this;
	}
	
	/**
	 *	--------------------------------------------------------------------------------
	 *	LIKE PARAMETERS
	 *	--------------------------------------------------------------------------------
	 *
	 *	Get the documents where the (string) value of a $column is like a value. The defaults
	 *	allow for a case-insensitive search.
	 *
	 *	@param	string	$column
	 *	@param	string	$value
	 *	@param	string	$flags
	 *	Allows for the typical regular expression flags:
	 *		i = case insensitive
	 *		m = multiline
	 *		x = can contain comments
	 *		l = locale
	 *		s = dotall, "." matches everything, including newlines
	 *		u = match unicode
	 *
	 *	@param	bool	$disable_start_wildcard
	 *	If this value evaluates to false, no starting line character "^" will be prepended
	 *	to the search value, representing only searching for a value at the start of
	 *	a new line.
	 *
	 *	@param	bool	$disable_end_wildcard
	 *	If this value evaluates to false, no ending line character "$" will be appended
	 *	to the search value, representing only searching for a value at the end of
	 *	a line.
	 *
	 *	@return Mongoo_Query_Builder
	 *	@usage	$mongodb->like('foo', 'bar', 'im', false, true);
	 */
	public function like($column = '', $value = '', $flags = 'i', $disable_start_wildcard = false, $disable_end_wildcard = false)
	{
		$column = (string) trim($column);
		$this->_where_init($column);
	
		$value = (string) trim($value);
		$value = quotemeta($value);
	
		(bool) $disable_start_wildcard === false and $value = '^'.$value;
		(bool) $disable_end_wildcard === false and $value .= '$';
	
		$regex = "/$value/$flags";
		$this->_wheres[$column] = new \MongoRegex($regex);
	
		return $this;
	}
	
	/**
	 * 排序 
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
	
	public function execute()
	{
		$query = $this->_db->{$this->_collection};
		// 查一个
		if($this->_limit == 1 && $this->_skip == 0)
			return $query->findOne($this->_wheres, $this->_select);
		
		// 查多个
		$cursor = $query->find($this->_wheres, $this->_select);
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
		return $this->_db->{$this->_collection}->find($this->_wheres)->limit($this->_limit)->skip($this->_skip)->count($apply_skip_limit);
	}
}