<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder extends DB_Query_Builder_Decoration
{
	/**
	 * 编译sql: 延迟拼接sql, 因为调用方法时元素无序, 但生成sql时元素有序
	 * @return array(sql, 参数)
	 */
	public function compile()
	{
		// 动作子句 + 修饰子句
		$action = $this->compile_action();
		list($decoration, $params) = $this->compile_decoration();
		return array($action.$decoration, $params);
	}
	
	public function get($table = NULL)
	{
		// 1 设置模板
		if($table !== NULL)// 表名
			$this->_table = $table; 
		$this->_action_template = static::ACTION_TEMPLATE_SELECT; // 模板
		
		// 2 编译
		list($sql, $params) = $this->compile();
		
		// 3 执行
		return $this->execute($sql, $params);
	}
	
	public function insert($table = NULL)
	{
		// 1 设置模板
		if($table !== NULL)// 表名
			$this->_table = $table; 
		$this->_action_template = static::ACTION_TEMPLATE_insert; // 模板
		
		// 2 编译
		list($sql, $params) = $this->compile();
		
		// 3 执行
		return $this->_db->execute($sql, $params);
	}
	
}
