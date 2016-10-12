<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql装饰部分的过滤器
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query_Decoration_Filter
{
	/**
	 * 数据库连接
	 * @var Db
	 */
	protected $_db;
	
	/**
	 * 过滤的规则
	 * @var array
	 */
	protected $_rules;
	
	public function __construct(Db $db)
	{
		$this->_db = $db;
	}
	
	public function int($value)
	{
		return (int) $value;
	}
	
	public function str($value)
	{
		return $value;
	}
	
	public function table($table)
	{
		return $this->_db->quote_table($table);
	}
	
	public function column($column)
	{
		return $this->_db->quote_column($column);
	}
	
	public function value($value)
	{
		return $this->_db->quote_value($value);
	}
	
	public function order_direction($value)
	{
		if($value !== NULL){
			$value = strtoupper($value);
			if (in_array($value, array('ASC', 'DESC'))) 
				return $value;
		}
		return NULL;
	}
	
	public function join_type($value)
	{
		if($value !== NULL){
			$value = strtoupper($value);
			if (in_array($value, array('LEFT', 'RIGHT', 'INNER'))) 
				return $value;
		}
		return NULL;
	}
	
}