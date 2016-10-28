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
interface Interface_Db_Query_Builder
{
	/**
	 * 编译sql
	 * @return array(sql, 参数)
	 */
	public function compile();
	
	/**
	 * 编译 + 执行
	 * 
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_NUM，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return int 影响行数
	 */
	public function execute($fetch_value = FALSE);

}