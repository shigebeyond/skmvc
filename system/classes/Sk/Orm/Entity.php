<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之实体对象
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34 
 *
 */
abstract class Sk_Orm_Entity
{
	/**
	 * 原始的数据：<字段名 => 字段值>
	 * @var array
	*/
	protected $_original = array();
	
	/**
	 * 变化的数据：<字段名 => 字段值>
	 * @var array
	*/
	protected $_dirty = array();
	
	/**
	 * 获得对象字段
	 *
	 * @param   string $column 字段名
	 * @return  mixed
	 */
	public function __get($column)
	{
		// 尝试获得对象字段
		if($this->try_get($column, $value))
			return $value;
		
		// 如果获得失败,则抛出异常
		$class = get_class($this);
		throw new Exception("类 $class 没有字段 $column");
	}
	
	/**
	 * 尝试获得对象字段
	 *
	 * @param   string $column 字段名
	 * @param   unknow $value 字段值，引用传递，用于获得值
	 * @return  bool
	 */
	public function try_get($column, &$value)
	{
		if(!isset($this->_original, $column))
			return FALSE;
		
		// 先找dirty，后找original
		$value = isset($this->_dirty[$column]) ? $this->_dirty[$column] : $this->_original[$column];
		return TRUE;
	}
	
	/**
	 * 设置对象字段值
	 *
	 * @param  string $column 字段名
	 * @param  mixed  $value  字段值
	 */
	public function __set($column, $value)
	{
		// 尝试设置对象字段, 如果失败, 则跑出异常
		if(!$this->try_set($column, $value))
		{
			$class = get_class($this);
			throw new Exception("类 $class 没有字段 $column");
		}
	}
	
	/**
	 * 尝试设置字段值
	 *
	 * @param  string $column 字段名
	 * @param  mixed  $value  字段值
	 * @return ORM
	 */
	public function try_set($column, $value)
	{
		// 判断是否是字段
		if (!isset($this->columns(), $column))
			return FALSE;
		
		// 判断字段值是否真正有改变
		if ($value === $this->_original[$column])
			unset($this->_dirty[$column]);
		else 
			$this->_dirty[$column] = $value; // 记录变化字段
		
		return TRUE;
	}
	
	/**
	 * 获得字段
	 * @return array
	 */
	abstract public static function columns();
}