<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器
 *   依次继承 Db_Query_Builder_Action 处理动作子句 + Db_Query_Builder_Decoration 处理修饰子句
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder extends Db_Query_Builder_Decoration
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
	
	/**
	 * 编译 + 执行
	 * 
	 * @param string $fetch_class 查询语句返回的对象类型, 如果是false, 则返回关联数组, 否则返回对应类的对象
	 * @return Db_Result
	 */
	public function execute($fetch_class = FALSE) 
	{
		// 1 编译
		list ($sql, $params) = $this->compile();
		
		// 2 执行
		// select
		if($this->_action == 'select') 
			return $this->_db->query($sql, $params, $fetch_class);
		
		// insert/update/delete 
		return $this->_db->execute($sql, $params);
	}
	
}
