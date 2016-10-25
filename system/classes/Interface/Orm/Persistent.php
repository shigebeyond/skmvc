<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之持久化，主要是负责数据库的增删改查
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-10
 *
 */
interface Interface_Orm_Persistent
{

	/**
	 * 获得sql构建器: (select) sql
	 *
	 * @param string $action
	 * @return Orm_Query_Builder
	 */
	public static function query_builder($action = 'select');

	/**
	 * 校验数据
	 * @return boolean
	 */
	public function check();

	/**
	 * 判断当前记录是否存在于db: 有原始数据就认为它是存在的
	 */
	public function exists();

	/**
	 * 保存数据
	 *
	 * @return int 对insert返回新增数据的主键，对update返回影响行数
	 */
	public function save();

	/**
	 * 插入数据: insert sql
	 *
	 * <code>
	 *    $user = new Model_User();
	 *    $user->name = 'shi';
	 *    $user->age = 24;
	 *    $user->create();
	 * </code>
	 * 
	 * @return int 新增数据的主键
	 */
	public function create();

	/**
	 * 更新数据: update sql
	 *
	 * <code>
	 *    $user = Model_User::query_builder()->where('id', '=', 1)->find();
	 *    $user->name = "li";
	 *    $user->update();
	 * </code>
	 * 
	 * @return int 影响行数
	 */
	public function update();

	/**
	 * 删除数据: delete sql
	 *
	 * <code>
	 *    $user = Model_User::query_builder()->where('id', '=', 1)->find();
	 *    $user->delete();
	 * </code>
	 * 
	 * @return int 影响行数
	 */
	public function delete();
	
}
