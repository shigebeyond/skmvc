<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Mongodb查询构建器
 * 	方法与 Interface_Db_Query_Builder_Action + Interface_Db_Query_Builder 的差不多，兼容部分 Db_Query_Builder 的api
 *  
 * 关系型数据库与mongodb的3个层次的兼容：
 *    1 Db层：Db 与 Mongoo 不用兼容
 *    2 Query_Builder层：Db_Query_Builder 与 Mongoo_Query_Builder 尽量兼容
 *    3 ORM层：ORM 与 ODM 完全兼容，终极目标
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-28 下午10:18:27 
 *
 */
interface Interface_Mongoo_Query_Builder
{
	/**
	 * 清空条件
	 * @return Mongoo_Query_Builder
	 */
	public function clear();
	
	/**
	 * 设置当前查询的集合
	 *
	 * @param string $table
	 * @return Mongoo_Query_Builder
	 */
	public function table($table);
	
	/**
	 * 设置查询的字段
	 *
	 * @param array $columns
	 * @return Mongoo_Query_Builder
	 */
	public function select(array $columns);
	
	/**
	 * 设置插入的单行, insert时用
	 *
	 * @param array $row
	 * @return Mongoo_Query_Builder
	 */
	public function value(array $row);
	
	/**
	 * 设置插入的多行, insert时用
	 *
	 * @param array $rows
	 * @return Mongoo_Query_Builder
	 */
	public function values(array $rows);
	
	/**
	 * 设置更新的单个值, update时用
	 *
	 * @param string $column
	 * @param string $value
	 * @return Mongoo_Query_Builder
	 */
	public function set($column, $value);
	
	/**
	 * 设置更新的多个值, update时用
	 *
	 * @param array $row
	 * @param bool $partial 是否部分更新
	 * @return Mongoo_Query_Builder
	 */
	public function sets(array $row, $partial = FALSE);
	
	/**
	 *	$and多个条件
	 *
	 *	@param	array $wheres
	 *	@return	 Mongoo_Query_Builder
	 */
	public function wheres($wheres = array());
	
	/**
	 * $and一个条件
	 *
	 * @param string $column
	 * @param string $op
	 * @param string $value
	 * @return Mongoo_Query_Builder
	 */
	public function where($column, $op, $value = NULL);
	
	/**
	 *	$or条件
	 *
	 *	@param	array	 $where
	 *	@return Mongoo_Query_Builder
	 */
	public function or_where($column, $op, $value = NULL);
	
	/**
	 * between条件
	 *
	 * @param string $column
	 * @param mixed $min
	 * @param mixed $max
	 * @return Mongoo_Query_Builder
	 */
	public function where_between($column, $min, $max);
	
	/**
	 * 正则匹配
	 *
	 * @param string $column
	 * @param string $regex
	 * @return Mongoo_Query_Builder
	 */
	public function like($column, $regex);
	
	/**
	 * 排序
	 *
	 * @param string $column
	 * @param number $direction
	 * @return Mongoo_Query_Builder
	 */
	public function order_by($column, $direction = 1);
	
	/**
	 *	限制结果集的行数
	 *
	 *	@param	int $limit
	 *	@param	int $offset
	 *	@return Mongoo_Query_Builder
	 */
	public function limit($limit, $offset = 0);
	
	/**
	 *	设置结果集的位移
	 *
	 *	@param	int $offset
	 *	@return Mongoo_Query_Builder
	 */
	public function offset($offset);
	
	/**
	 * 设置命令动作
	 *
	 * @param string $action 动作: findOne/find/insert/update/delete
	 *	@return Mongoo_Query_Builder
	 */
	public function action($action);
	
	/**
	 * 编译为命令
	 *
	 * @param array $options
	 * @return string
	 */
	public function compile(array $options = array());
	
	/**
	 * 查找一个
	 * @return object
	 */
	public function find();
	
	/**
	 * 查找多个
	 * @return MongoCursor
	 */
	public function find_all();
	
	/**
	 * 统计行数
	 *
	 * @param boolean $apply_skip_limit
	 * @return int
	 */
	public function count($apply_skip_limit = FALSE);
	
	/**
	 * 插入
	 * @return MongoId|boolean
	 */
	public function insert();
	
	/**
	 *	更新
	 *	@return	bool
	 */
	public function update();
	
	/**
	 *	删除
	 *	@return	bool
	 */
	public function delete();
}