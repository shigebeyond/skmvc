<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- insert
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action_Insert extends Db_Query_Action
{
	/**
	 * 动作子句模板: insert
	 * @var string
	 */
	protected $_action_template = 'INSERT INTO :table (:keys) VALUES (:values)';
	
	/**
	 * 设置插入的值
	 * 
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Action_Insert
	 */
	public function value($column, $value)
	{
		return $this->data($column, $value);
	}
	
}