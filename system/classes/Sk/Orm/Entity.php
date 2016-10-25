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
abstract class Sk_Orm_Entity implements ArrayAccess, Interface_Orm_Entity 
{
	/**
	 * 获得字段
	 * @return array
	 */
	public static function columns()
	{
		throw new Orm_Exception("必须重写该方法");
	}

	/**
	 * 原始的字段值：<字段名 => 字段值>
	 * @var array
	*/
	protected $_original = array();

	/**
	 * 变化的字段值：<字段名 => 字段值>
	 * @var array
	*/
	protected $_dirty = array();

	/**
	 * 判断对象是否存在指定字段
	 *
	 * @param  string $column Column name
	 * @return boolean
	 */
	public function __isset($column)
	{
		return array_key_exists($column, $this->columns());
	}

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
		throw new Orm_Exception("类 $class 没有字段 $column");
	}

	/**
	 * 尝试获得对象字段
	 *
	 * @param   string $column 字段名
	 * @param   mixed $value 字段值，引用传递，用于获得值
	 * @return  bool
	 */
	public function try_get($column, &$value)
	{
		// 判断是否是字段
		if (!array_key_exists($column, $this->columns()))
			return FALSE;

		// 先找dirty，后找original
		if(isset($this->_dirty[$column]))
			$value = $this->_dirty[$column];
		elseif(isset($this->_original[$column]))
			$value = $this->_original[$column];
		else
			$value = NULL;
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
			throw new Orm_Exception("类 $class 没有字段 $column");
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
		if (!array_key_exists($column, $this->columns()))
			return FALSE;

		// 判断字段值是否真正有改变
		if ($value === Arr::get($this->_original, $column))
			unset($this->_dirty[$column]);
		else
			$this->_dirty[$column] = $value; // 记录变化字段

		return TRUE;
	}

	/**
	 * 删除某个字段值
	 *
	 * @param  string $column 字段名
	 * @return
	 */
	public function __unset($column)
	{
		unset($this->_original[$column], $this->_dirty[$column]);
	}

	/**
	 * 设置多个字段值
	 *
	 * @param  array $values   字段值的数组：<字段名 => 字段值>
	 * @param  array $expected 要设置的字段名的数组
	 * @return ORM
	 */
	public function values(array $values, array $expected = NULL)
	{
		if ($expected === NULL)
			$expected = array_keys($values);

		foreach ($expected as $column)
			$this->$column = $values[$column];

		return $this;
	}

	/**
	 * 获得/设置原始的字段值
	 * @param array $original
	 * @return Orm|array
	 */
	public function original(array $original = NULL)
	{
		// getter
		if ($original === NULL)
			return $this->_original;

		// setter
		$this->_original = $original;
		return $this;
	}

	/**
	 * 获得变化的字段值
	 * @return array
	 */
	public function dirty()
	{
		return $this->_dirty;
	}

	/**
	 * 获得字段值
	 * @return array
	 */
	public function as_array()
	{
		return $this->_dirty + $this->_original;
	}

	/**
	 * 判断数组是否存在指定key
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	/**
	 * 获得数组的元素
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * 设置数组的元素
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	/**
	 * 删除数组的元素
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		$this->__unset($offset);
	}


}
