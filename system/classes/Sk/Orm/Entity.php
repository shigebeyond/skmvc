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
	 * 当前数据
	 * @var array
	 */
	protected $_object = array();
	
	/**
	 * 记录变化的字段
	 * @var array
	*/
	protected $_dirty = array();
	
	/**
	 * 原始的数据
	 * @var array
	*/
	protected $_original = array();
	
	/**
	 * 获得对象属性
	 *
	 * @param   string $column 属性名
	 * @return  mixed
	 */
	public function __get($column)
	{
		// 尝试获得对象属性
		if($this->try_get($column, $value))
			return $value;
		
		// 如果获得失败,则抛出异常
		$class = get_class($this);
		throw new Exception("类 $class 没有属性 $column");
	}
	
	/**
	 * 尝试获得对象属性
	 *
	 * @param   string $column 属性名
	 * @param   unknow $value 属性值，引用传递，用于获得值
	 * @return  bool
	 */
	public function try_get($column, &$value)
	{
		if(isset($this->_object, $column))
		{
			$value = $this->_object[$column];
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * 设置对象属性值
	 *
	 * @param  string $column 属性名
	 * @param  mixed  $value  属性值
	 */
	public function __set($column, $value)
	{
		// 尝试设置对象属性, 如果失败, 则跑出异常
		if(!$this->try_set($column, $value))
		{
			$class = get_class($this);
			throw new Exception("类 $class 没有属性 $column");
		}
	}
	
	/**
	 * 尝试设置属性值
	 *
	 * @param  string $column 属性名
	 * @param  mixed  $value  属性值
	 * @return ORM
	 */
	public function try_set($column, $value)
	{
		// 判断是否是字段
		if (isset($this->columns(), $column))
		{
			// 判断属性值是否真正有改变
			if ($value !== $this->_object[$column])
			{
				$this->_object[$column] = $value;
				$this->_dirty[$column] = TRUE; // 记录变化的字段
			}
		}
		
		return TRUE;
	}
	
	/**
	 * 获得字段
	 * @return array
	 */
	abstract public static function columns();
}