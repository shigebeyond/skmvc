<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 动作子句: 由动态select/insert/update/delete来构建的子句
 *   通过字符串模板来实现
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
interface Interface_Db_Query_Builder_Action
{
	/**
	 * 设置表名: 一般是单个表名
	 * @param tables 表名数组: array($table1, $table2, $alias => $table3), 
	 * 								  如 array('user', 'contact', 'addr' => 'user_address'), 其中 user 与 contact 表不带别名, 而 user_address 表带别名 addr
	 * @return Db_Query_Builder
	 */
	public function table($tables);
	
	/**
	 * 设置表名: 可能有多个表名
	 * @param tables 表名数组: array($table1, $table2, $alias => $table3), 
	 * 								  如 array('user', 'contact', 'addr' => 'user_address'), 其中 user 与 contact 表不带别名, 而 user_address 表带别名 addr
	 * @return Db_Query_Builder
	 */
	public function from($tables);

	/**
	 * 设置插入的单行, insert时用
	 *
	 * @param array $row
	 * @return Db_Query_Builder
	 */
	public function value(array $row);
	
	/**
	 * 设置插入的多行, insert时用
	 *
	 * @param array $rows
	 * @return Db_Query_Builder
	 */
	public function values(array $rows);
	
	/**
	 * 设置更新的单个值, update时用
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Builder
	 */
	public function set($column, $value);
	
	/**
	 * 设置更新的多个值, update时用
	 *
	 * @param array $row
	 * @return Db_Query_Builder
	 */
	public function sets($row);
	
	/**
	 * 设置查询的字段, select时用
	 *
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Db_Query_Builder
	 */
	public function select(array $columns);
	
	/**
	 * 设置查询结果是否去重唯一
	 *
	 * @param boolean $value
	 * @return Sk_Db_Query_Builder_Action
	 */
	public function distinct($value);
	
	/**
	 * 编译动作子句
	 * @return string
	 */
	public function compile_action();
}