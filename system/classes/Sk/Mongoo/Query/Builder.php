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
	 * 命令模板
	 * @var array
	 */
	public static $command_templates = array(
			'findOne' => "db.:collection.findOne(:where)",
			'find' => "db.:collection.find(:where).limit(:limit).skip(:skip).sort(:sort).count(:apply_skip_limit)",
			'insert' => "db.:collection.insert(:data)",
			'update' => "db.:collection.update(:where, :data, false, :multiple)",
			'delete' => "db.:collection.remove(:where, :justOne)",
	);
	
	/**
	 * 命令动作: find/find_all/insert/update/delete
	 * @var string 
	 */
	protected $_action;
	
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
	 * 要插入的多行: [<column => value>]
	 * 要更新字段值: <column => value>
	 * 要查询的字段名: [alias => column]
	 * @var array
	 */
	protected $_data = array ();
	
	/**
	 * 查询条件
	 * @var  array
	*/
	public $_where = array();
	
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
	 */
	public function __construct($db = 'default', $collection = NULL)
	{
		// 获得db
		if (! $db instanceof Mongoo)
			$db = Mongoo::instance ( $db );
		$this->_db = $db;
	
		if ($collection)
			$this->collection ( $collection );
	}
	
	/**
	 * 清空条件
	 * @return Mongoo_Query_Builder
	 */
	public function clear()
	{
		$this->_data = $this->_where = $this->_sort = array();
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
	 * 设置插入的单行, insert时用
	 *
	 * @param array $row
	 * @return Mongoo_Query_Builder
	 */
	public function value(array $row)
	{
		$this->_data[] = $row;
		return $this;
	}
	
	/**
	 * 设置插入的多行, insert时用
	 *
	 * @param array $rows
	 * @return Mongoo_Query_Builder
	 */
	public function values(array $rows)
	{
		$this->_data += $rows;
		return $this;
	}
	
	/**
	 * 设置更新的单个值, update时用
	 *
	 * @param string $column
	 * @param string $value
	 * @return Mongoo_Query_Builder
	 */
	public function set($column, $value)
	{
		Arr::set_path($this->_data, array('$set', $column), $value);
		return $this;
	}
	
	/**
	 * 设置更新的多个值, update时用
	 *
	 * @param array $row
	 * @param bool $partial 是否部分更新
	 * @return Mongoo_Query_Builder
	 */
	public function sets(array $row, $partial = FALSE)
	{
		if($partial)
			$this->_data['$set'] = $row;
		else 
			$this->_data = $row;
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
		$this->_where += $wheres;
		return $this;
	}
	
	/**
	 * 判断是否运算符
	 * 
	 * @param mixed $op 
	 * @return bool
	 */
	public function is_operator($op)
	{
		return is_string($op) && Text::start_with($op, '$');
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
		if($this->is_operator($op)) // 有运算符
			Arr::set_path($this->_where, array($column, $op), $value);
		else // 无运算符
			$this->_where[$column] = $op;
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
		if($this->is_operator($op)) // 有运算符
			$path = array('$or', $column, $op);
		else // 无运算符
		{
			$path = array('$or', $column, '');
			$value = $op;
		}
		
		Arr::set_path($this->_where, $path, $value);
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
	 *	设置结果集的位移
	 *
	 *	@param	int $offset
	 *	@return Mongoo_Query_Builder
	 */
	public function offset($offset)
	{
		$this->_skip = (int)$offset;
		return $this;
	}
	
	/**
	 * 设置命令动作
	 * 
	 * @param string $action 动作: find/find_all/insert/update/delete
	 *	@return Mongoo_Query_Builder
	 */
	public function action($action)
	{
		$this->_action = $action;
		return $this;
	}
	
	/**
	 * 编译为命令
	 * 
	 * @param array $options
	 * @return string
	 */
	public function compile(array $options = array())
	{
		// 获得命令模板
		$command = Arr::get(static::$command_templates, $this->_action);
		// 编译模板: 替换参数
		$command = preg_replace_callback('/:(\w+)/', function($mathes) use ($options){
			$key = $mathes[1];
			//１查询参数
			if(isset($this->{"_$key"}))
				return $this->quote($this->{"_$key"}, $key);
			
			// 2 命令选项
			if(isset($options[$key]))
				return $this->quote($options[$key]);
				
			return $key;
		}, $command);
		//　删除多余代码
		return preg_replace('/\.limit\(0\)|\.skip\(0\)|\.sort\(\[\]\)/', '', $command);
	}
	
	/**
	 * 转义值
	 * 
	 * @param mixed $value
	 * @param string $key
	 * @return string
	 */
	public function quote($value, $key = NULL)
	{
		//　对bool
		if(is_bool($value))
			return $value ? 'true' : 'false';
			
		//　对数组
		if(is_array($value))
		{
			//　如果是insert的data，则取第一条数据
			if($this->_action == 'insert' && $key == 'data')
				$value = $value[0];
			
			// 对where/data中的MongId数值转为字符串
			$has_id = FALSE;
			if (in_array($key, array('where', 'data')))
				array_walk_recursive($value, function(&$v, $k) use (&$has_id){
					if($v instanceof MongoId)
					{
						$v = "ObjectId('$v')";
						$has_id = TRUE;
					}
				});
			
			$json = json_encode($value);
			if($has_id)
				return preg_replace('/\"(ObjectId\(.+\))\"/', '$1', $json);
			
			return $json;
		}
		
		return $value;
	}
	
	/**
	 * 查找一个
	 * @return object
	 */
	public function find()
	{
		die($this->action('findOne')->compile());
		return $this->_db->{$this->_collection}->findOne($this->_where, $this->_data);
	}
	
	/**
	 * 查找多个
	 * @return MongoCursor
	 */
	public function find_all()
	{
		die($this->action('find')->compile());
		//　获得游标
		$cursor = $this->_db->{$this->_collection}->find($this->_where, $this->_data);
		//　限制游标
		foreach (array('limit', 'skip', 'sort') as $name)
		{
			$value = $this->{"_$name"};
			if($value)
				$cursor->$name($value);
		}
		
		//return iterator_to_array($cursor);
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
		die($this->action('find')->compile(array('apply_skip_limit' => $apply_skip_limit)));
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
			if(empty($this->_data))
				throw new Db_Exception("插入Mongodb的数据为空");
			
			die($this->action('insert')->compile());
			
			// 插入一行: insert + 返回新增id
			if(count($this->_data) === 1)
			{
				$row = &$this->_data[0];
				$this->_db->{$this->_collection}->insert($row, array('fsync' => true));
				return Arr::get($row, '_id', FALSE);
			}
			
			// 插入多行: batchInsert + 返回bool
			return $this->_db->{$this->_collection}->batchInsert($this->_data, array('fsync' => true));
		}
		catch (MongoCursorException $e)
		{
			throw new Db_Exception("插入Mongodb的数据出错: {$e->getMessage()}", $e->getCode(), $e);
		}
	}

	/**
	 * 判断是否操作单条记录
	 * 	　查询条件为：主键=某个值
	 * @return boolean
	 */
	public function is_single()
	{
		return isset($this->_where['_id']) //　有主键条件
			&& (!is_array($this->_where['_id']) || isset($this->_where['_id']['$eq'])); //　主键＝某个值
	}

	/**
	 * 判断是否操作多条记录
	 * @return boolean
	 */
	public function is_multiple()
	{
		return !$this->is_single();
	}
	
	/**
	 *	更新
	 *	@return	bool
	 */
	public function update()
	{
		try
		{
			if(empty($this->_data))
				throw new Db_Exception("更新Mongodb的数据为空");
			
			//　是否更新多条记录
			$multiple = $this->is_multiple();
			// fix bug: multi update only works with $ operators
			$data = $this->_data;
			if($multiple && !isset($data['$set'])) 
				$data = array('$set' => $data);
			//　更新
			$this->_db->{$this->_collection}->update($this->_where, $data, array('fsync' => true, 'multiple' => $multiple));
			
			die($this->action('update')->compile(array('multiple' => $multiple)));
			return TRUE;
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
			$this->_db->{$this->_collection}->remove($this->_where, array('fsync' => true, 'justOne' => $this->is_single()));
			
			die($this->action('delete')->compile(array('justOne' => $this->is_single())));
			return TRUE;
		}
		catch (MongoCursorException $e)
		{
			throw new Db_Exception("删除Mongodb的数据出错: {$e->getMessage()}", $e->getCode(), $e);
		}
	}
}