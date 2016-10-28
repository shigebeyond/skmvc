<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器
 *   依次继承 Db_Query_Builder_Action 处理动作子句 + Db_Query_Builder_Decoration 处理修饰子句
 *  提供select/where等类sql的方法, 但是调用方法时, 不直接拼接sql, 而是在compile()时才延迟拼接sql, 因为调用方法时元素可以无序, 但生成sql时元素必须有序
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
class Sk_Db_Query_Builder extends Db_Query_Builder_Decoration implements Interface_Db_Query_Builder
{
	/**
	 * 编译sql
	 * 
	 * @param string $action sql动作：select/insert/update/delete
	 * @return array(sql, 参数)
	 */
	public function compile($action = NULL)
	{
		// 动作子句 + 修饰子句
		$action_sql = $this->action($action)->compile_action();
		list($decoration_sql, $params) = $this->compile_decoration();
		return array($action_sql.$decoration_sql, $params);
	}
	
	/**
	 * 编译 + 执行
	 * 
	 * @param string $action sql动作：select/insert/update/delete
	 * @return int 影响行数
	 */
	protected function _execute($action) 
	{
		// 1 编译
		list ($sql, $params) = $this->compile($action);
		
		// 2 执行 insert/update/delete 
		return $this->_db->execute($sql, $params);
	}
	
	/**
	 * 查找多个： select 语句
	 *
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_COLUMN，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return array
	 */
	public function find_all($fetch_value = FALSE)
	{
		// 1 编译
		list ($sql, $params) = $this->compile('select');
	
		// 2 执行 select
		return $this->_db->query($sql, $params, $fetch_value);
	}
	
	/**
	 * 查找一个： select ... limit 1语句
	 *
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_COLUMN，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return object
	 */
	public function find($fetch_value = FALSE)
	{
		$rows = $this->limit(1)->find_all($fetch_value);
		return isset($rows[0]) ? $rows[0] : NULL;
	}
	
	/**
	 * 统计行数： count语句
	 * @return int
	 */
	public function count()
	{
		return $this->select(array('num' => 'count(1)'))->find(0); // 结果集是第一列
	}
	
	/**
	 * 插入：insert语句
	 * @return int 新增的id
	 */
	public function insert()
	{
		return $this->_execute('insert');
	}
	
	/**
	 *	更新：update语句
	 *	@return	bool
	 */
	public function update()
	{
		return $this->_execute('update');
	}
	
	/**
	 *	删除
	 *	@return	bool
	 */
	public function delete()
	{
		return $this->_execute('delete');
	}
}
