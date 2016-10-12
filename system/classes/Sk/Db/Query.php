<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器
 * 	延迟拼接sql, 因为调用方法时元素无序, 但生成sql时元素有序
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query
{
	/**
	 * 数据库连接
	 * @var Sk_Db
	 */
	protected $_db;
	
	/**
	 * 表名
	 * @var string
	 */
	protected $_table;
	
	/**
	 * 获得/设置表名
	 * @param string $table
	 * @return string|Sk_Db_Query
	 */
	public function table($table = NULL)
	{
		// getter
		if($table === NULL)
			return $this->_db->quote_table($this->_table);
		
		// setter
		$this->_table = $table;
		return $this;
	}
	
	/**
	 * 编译sql
	 * @return string
	 */
	public abstract function compile()
	{
		// 动作
		$sql = $this->compile_action();
		// where
		if($where = $this->compile_where())
			$sql = "$sql WHERE $where";
		
		return $sql;
	}
	
	/**
	 * 编译动作子句
	 * @return string
	 */
	public abstract function compile_action();
	
	/**
	 * 编译where子句
	 * @return string
	 */
	public abstract function compile_where();
}