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
	 * @var Db
	 */
	protected $_db;
	
	/**
	 * 表名
	 * @var string
	 */
	protected $_table;
	
	/**
	 * 要插入/更新字段: <column => value>
	 * 要查询的字段名: [column]
	 * @var string
	 */
	protected $_data;
	
	/**
	 * 设置表名
	 * @param string $table
	 * @return Sk_Db_Query
	 */
	public function table($table)
	{
		$this->_table = $table;
		return $this;
	}
	
	/**
	 * 设置插入/更新的值
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query
	 */
	public function data($column, $value)
	{
		if(is_array($column))
			$this->_data = $column;
		else
			$this->_data[$column] = $value;
	
		return $this;
	}
	
	/**
	 * 编译sql
	 * @return string
	 */
	public function compile()
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