<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- update
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action_Update extends Db_Query_Action
{
	/**
	 * sql动作: update
	 * @var string
	 */
	protected $_action = 'UPDATE :table SET :key = :value';
	
	/**
	 * 设置更新的值
	 * 
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Action_Update
	 */
	public function set($column, $value)
	{
		return $this->data($column, $value);
	}
	
}