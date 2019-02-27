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
interface Interface_Orm_Entity
{
	/**
	 * 判断是否有某字段
	 * 
	 * @param string $column
	 * @return 
	 */
	public static function has_column($column);

	/**
	 * 判断对象是否存在指定字段
	 *
	 * @param  string $column Column name
	 * @return boolean
	 */
	public function __isset($column);

	/**
	 * 获得对象字段
	 *
	 * @param   string $column 字段名
	 * @return  mixed
	 */
	public function __get($column);

	/**
	 * 尝试获得对象字段
	 *
	 * @param   string $column 字段名
	 * @param   mixed $value 字段值，引用传递，用于获得值
	 * @return  bool
	 */
	public function try_get($column, &$value);

	/**
	 * 设置对象字段值
	 *
	 * @param  string $column 字段名
	 * @param  mixed  $value  字段值
	 */
	public function __set($column, $value);

	/**
	 * 尝试设置字段值
	 *
	 * @param  string $column 字段名
	 * @param  mixed  $value  字段值
	 * @return ORM
	 */
	public function try_set($column, $value);

	/**
	 * 删除某个字段值
	 *
	 * @param  string $column 字段名
	 * @return
	 */
	public function __unset($column);

	/**
	 * 设置多个字段值
	 *
	 * @param  array $values   字段值的数组：<字段名 => 字段值>
	 * @param  array $expected 要设置的字段名的数组
	 * @return ORM
	 */
	public function values(array $values, array $expected = NULL);

	/**
	 * 设置原始的字段值
	 * @param array $original
	 * @return Orm
	 */
	public function setOriginal(array $original);

	/**
	 * 获得字段值
	 * @return array
	 */
	public function as_array();

}
