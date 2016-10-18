<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之间的关联关系
 *        表之间的关联关系: 主表.主键 = 从表.外键
 *        
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34 
 *
 */
class Sk_Orm_Relation extends Sk_Orm_Relation
{
	/**
	 * 主类
	 * @var string
	 */
	protected $_master_class;

	/**
	 * 主对象
	 * @var Orm
	 */
	protected $_master_object;

	/**
	 * 从类
	 * @var string
	 */
	protected $_slave_class;

	/**
	 * 从对象
	 * @var Orm
	 */
	protected $_slave_object;

	/**
	 * 外键
	 * @var string
	 */
	protected $_foreign_key;
	
	/**
	 * 构造函数
	 * @param string|Orm $master 主类|主对象
	 * @param string|Orm $slave 从类|从对象
	 * @param string $foreign_key 外键
	 */
	public function __construct($master, $slave, $foreign_key)
	{
		// 设置主类 + 主对象
		if($master instanceof Orm)
		{
			$this->_master_class = get_class($master);
			$this->_master_object = $master;
		}
		else 
		{
			$this->_master_class = $master;
		}

		// 设置从类 + 从对象
		if($slave instanceof Orm)
		{
			$this->_slave_class = get_class($slave);
			$this->_slave_object = $slave;
		}
		else
		{
			$this->_slave_class = $slave;
		}
	}
	
	/**
	 * 查询主表
	 *     主表.主键 = 从表.外键
	 *     
	 * @param Orm_Query_Builder $query 如果不为空, 则为联表查询, 否则为单表查询
	 * @return Orm_Query_Builder
	 */
	public function query_master(Orm_Query_Builder $query = NULL)
	{
		// 在原有的查询上, 加上联查主表
		if($query)
			return $query->join($this->slave_table())->on($this->master_pk(TRUE), $this->slave_fk(TRUE)); // 主表.主键 = 从表.外键
		
		// 直接查询主表
		$class = $this->_master_class;
		return $class::query_builder()->where($this->master_pk(), $this->slave_fk()); // 主表.主键 = 从表.外键
	}
	
	/**
	 * 查询从表
	 *     从表.外键 = 主表.主键
	 *     
	 * @param Orm_Query_Builder $query 如果不为空, 则为联表查询, 否则为单表查询
	 * @return Orm_Query_Builder
	 */
	public function query_slave1(Orm_Query_Builder $query = NULL)
	{
		// 在原有的查询上, 加上联查从表
		if($query)
			return $query->join($this->slave_table())->on($this->slave_fk(TRUE), $this->master_pk(TRUE)); // 从表.外键 = 主表.主键
		
		// 直接查询从表
		$class = $this->_slave_class;
		return $class::query_builder()->where($this->slave_fk(), $this->master_pk()); // 从表.外键 = 主表.主键
	}
	
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
			return $query->join($slave_table)->on($this->slave_fk(TRUE), $this->master_pk(TRUE)); // 从表.外键 = 主表.主键
	
		// 直接查询从表
		$class = $this->_slave_class;
		return $class::query_builder()->where($this->slave_fk(), $this->master_pk()); // 从表.外键 = 主表.主键
	}
	
	
	/**
	 * 获得主表
	 * @return string
	 */
	public function master_table()
	{
		$class = $this->_master_class;
		return $class::table();
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