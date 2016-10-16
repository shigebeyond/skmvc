<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之持久化
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10
 *
 */
class Sk_Orm_Persistent extends Orm_MetaData
{
	/**
	 * 插入数据
	 *
	 *    $user = new User();
	 *    $user->name = 'shi';
	 *    $user->age = 24;
	 *    $user->create();
	 *
	 * @return int 新增数据的主键
	 */
	public function create()
	{
		if(empty($this->_dirty))
			return $this;
		
		// 插入数据库
		static::db()->insert(static::$_table)->data($this->_dirty)->execute();
		
		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_original[static::$_primary_key] = $pk = static::db()->last_insert_id(); // 主键
		$this->_dirty = array();

		return $pk;
	}
	
	/**
	 * 更新数据
	 *
	 *    $user = User::query_builder()->where('id', '=', 1)->find();
	 *    $user->name = "li";
	 *    $user->update();
	 * 
	 * @return int 影响行数
	 */
	public function update()
	{
		if (empty($this->_dirty))
			return $this;

		// 更新数据库
		$result = static::db()->update(static::$_table)->data($this->_dirty)->where(static::$_primary_key, '=', $this->pk())->execute();
		
		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_dirty = array();
		
		return $result;
	}
}