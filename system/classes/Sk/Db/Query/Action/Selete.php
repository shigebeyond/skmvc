<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- select
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action_Selete extends Db_Query_Action
{
	/**
	 * sql动作: select
	 * @var string
	 */
	protected $_action = 'SELECT :keys FROM :table';
	
	/**
	 * 设置查询的字段
	 *
	 * @param string... $columns
	 * @return Db_Query_Action_Selete
	 */
	public function select($columns)
	{
		$columns = func_get_args();
		// 创建一个关联数组<column => NULL>, 字段值为NULL
		$data = array_combine(array_keys($this->_table_columns), array_fill(0, count($this->_table_columns), NULL));
		return $this->data($data);
	}
	
}