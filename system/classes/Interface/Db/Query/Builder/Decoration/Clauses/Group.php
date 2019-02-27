<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 分组子句
 * 	子句中有子句, 而第二层的子句由分组来管理
 *     一个分组有多个子句, 就是使用()来包含的子句
 *     
 *  实现:
 *     子表达式还是子句
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-13
 *
 */
interface Interface_Db_Query_Builder_Decoration_Clauses_Group
{
	/**
	 * 开启一个分组
	 * 
	 * @param	$delimiter
	 * @return Sk_Db_Query_Builder_Decoration_Clauses_Group
	 */
	public function open($delimiter);
	
	/**
	 * 结束一个分组
	 * 
	 * @return Sk_Db_Query_Builder_Decoration_Clauses_Group
	 */
	public function close();

}