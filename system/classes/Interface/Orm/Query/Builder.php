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
interface Interface_Orm_Query_Builder
{
	/**
	 * 查询单个
	 * @return
	 *
	 */
	public function find();

	/**
	 * 查询多个
	 * @return array
	 */
	public function find_all();

		/**
	 * 联查表
	 *
	 * @param string $name 关联关系名
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Sk_Orm_Query_Builder
	 */
	public function with($name, array $columns = NULL);
	
}
