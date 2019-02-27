<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 封装mongodb连接
 *    代理MongoClient
 *  
 *  关系型数据库与mongodb的3个层次的兼容：
 *    1 Db层：Db 与 Mongoo 不用兼容
 *    2 Query_Builder层：Db_Query_Builder 与 Mongoo_Query_Builder 尽量兼容
 *    3 ORM层：ORM 与 ODM 完全兼容，终极目标
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-28 下午9:44:43 
 *
 */
interface Interface_Mongoo
{
	/**
	 *  获得Mongodb连接单例
	 *
	 * @param string $group 数据库配置的分组名
	 * @return Mongoo
	 */
	public static function instance($group = 'default');

	/**
	 * Mongodb查询构建器
	 *
	 * @param string $collection 集合名
	 * @return Mongoo_Query_Builder
	*/
	public function query_builder($collection = NULL);
}