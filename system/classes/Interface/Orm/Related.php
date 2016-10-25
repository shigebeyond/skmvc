<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之关联对象操作
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34
 *
 */
interface Interface_Orm_Related
{
	/**
	 * 获得关联关系
	 *
	 * @param string $name
	 * @return array
	 */
	public static function relation($name = NULL);

	/**
	 * 获得关联对象
	 *
	 * @param string $name 关联对象名
	 * @param boolean $new 是否创建新对象：在查询db后设置原始字段值original()时使用
	 * @return Orm
	 */
	public function related($name, $new = FALSE);
	
}
