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
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_NUM，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return int 影响行数
	 */
	public function execute($fetch_value = FALSE) 
	{
		// 1 编译
		list ($sql, $params) = $this->compile();
		
		// 2 执行
		// select
		if($this->_action == 'select') 
			return $this->_db->query($sql, $params, $fetch_value);
		
		// insert/update/delete 
		return $this->_db->execute($sql, $params);
	}
	
}
