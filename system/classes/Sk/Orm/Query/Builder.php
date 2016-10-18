<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 面向orm对象的sql构建器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-16 下午8:02:28 
 *
 */
class Sk_Orm_Query_Builder extends Db_Query_Builder 
{
	/**
	 * model的类
	 * @var string
	 */
	protected $_class;
	
	/**
	 * 构造函数
	 *
	 * string $class model类名，其基类为Orm
	 * @param string $action sql动作：select/insert/update/delete
	 * @param string|Db $db 数据库配置的分组名/数据库连接
	 * @param string $table 表名
	 * @param string $data 数据
	 */
	public function __construct($class, $action = 'select', $db = 'default', $table = NULL, $data = NULL)
	{
		parent::__construct($action, $db, $table, $data);
		
		// 检查是否是orm子类
		if(!is_subclass_of($class, 'Orm'))
			throw new Exception('Orm_Query_Builder::_class 必须是 Orm 的子类');
		$this->_class = $class;
	}
	
	/**
	 * 查询单个
	 * @return
	 *
	 */
	public function find() 
	{
		$rows = $this->limit(1)->find_all();
		return Arr::get($rows, 0);
	}
	
	/**
	 * 查询多个
	 * @return array
	 */
	public function find_all() 
	{
		$rows = $this->execute();
		foreach ($rows as $key => $row)
		{
			$orm = new $this->_class;
			$orm->original($row); // 设置原始字段值
			$rows[$key] = $orm;
		}
		return $rows;
	}
	
	public function join_has_one($config)
	{
		return $this->join_has_many($config)->limit(1);
	}
	
	public function join_has_many($config)
	{
		$r = new Relation($this->_class, 'Model_'.$config['model'], $config['foreign_key']);
		return $this->join($slave_table, 'LEFT')->on($r->master_pk(), '=', $master_table.'.'.$master_pk);
	}
	
	public function join_belongs_to($config)
	{
		// 获得主表及其主键
		$slave_class = $this->_class;
		$slave_table = $slave_class::table();
		$slave_fk = $config['foreign_key'];
		// 获得从表及其外键
		$master_class = 'Model_'.$config['model'];
		$master_table = $master_class::table();
		$master_pk = $slave_class::primary_key();
		
		return $this->join($master_table, 'LEFT')->on($class::$_primary_key, '=', $this->$fk);
	}
	
	function query_user()
	{
		return (new Orm_Relation($this, 'Model_User', 'class_id'))->query_slave($this);
		
		
	}
	
	function query_has_many($model)
	{
		return (new Orm_Relation($this, $model, 'class_id'))->query_slave($this);
	}
	
	
	// ------------------
	
	/**
	 * 查询从表
	 *     从表.外键 = 主表.主键
	 *
	 * @param Orm_Query_Builder $query 如果不为空, 则为联表查询, 否则为单表查询
	 * @return Orm_Query_Builder
	 */
	public function query_slave_xxxxx($master, $slave, $foreign_key, Orm_Query_Builder $query = NULL)
	{
		// 获得从表及其外键
		$master_class = ($master instanceof Orm) ? get_class($master) : $master;
		$master_table = $master_class::table();
		$master_pk = $master_class::primary_key();
	
		// 获得主表及其主键
		$slave_class = $slave;
		$slave_table = $slave_class::table();
		$slave_fk = $foreign_key;
	
		// 在原有的查询上, 加上联查从表
		if($query)
			return $query->join(call_user_func(array($slave, 'table')))->on($this->slave_fk(TRUE), $this->master_pk(TRUE)); // 从表.外键 = 主表.主键
	
		// 直接查询从表
		$class = $this->_slave_class;
		return $class::query_builder()->where($this->slave_fk(), $this->master_pk()); // 从表.外键 = 主表.主键
	}
	
	/**
	 * 获得字段/字段值
	 * 
	 * @param string|Orm $orm 类名|orm对象
	 * @param string $column 字段名
	 * @param string $with_table 是否带表名
	 * @return mixed
	 */
	public function orm_column($orm, $column, $with_table = FALSE)
	{
		// 1 返回值
		if($orm instanceof Orm)
			return $orm->{$column};
		
		// 2 返回字段
		if($with_table) // 带表名
			return $orm::table().'.'.$column;
		return $column;
	}
	
	/**
	 * 获得主键/主键值
	 * 
	 * @param string|Orm $orm $orm
	 * @param string $with_table 是否带表名
	 * @return mixed
	 */
	public function orm_pk($orm, $with_table = FALSE)
	{
		$primary_key = call_user_func(array($orm, 'primary_key'));
		return $this->orm_column($orm, $primary_key, $with_table);
	}
	
	/**
	 * 获得主表的主键
	 * @param string $with_table 是否带表名
	 * @return string
	 */
	public function master_pk($with_table = FALSE)
	{
		// 1 返回值
		if($this->_master_object)
			return $this->_master_object->pk();
	
		// 2 返回字段
		$class = $this->_master_class;
		if($with_table) // 带表名
			return $class::table().'.'.$class::primary_key();
		return $class::primary_key();
	}
	
	/**
	 * 获得从表
	 * @return string
	 */
	public function slave_table()
	{
		$class = $this->_master_class;
		return $class::table();
	}
	
	/**
	 * 获得从表的外键
	 * @param string $with_table 是否带表名
	 * @return string
	 */
	public function slave_fk($with_table = FALSE)
	{
		// 1 返回值
		if($this->_slave_object)
			return $this->_slave_object->{$this->_foreign_key}();
	
		// 2 返回字段
		if($with_table) // 带表名
		{
			$class = $this->_slave_class;
			return $class::table().'.'.$this->_foreign_key;
		}
	
		return $this->_foreign_key;
	}
	
}
