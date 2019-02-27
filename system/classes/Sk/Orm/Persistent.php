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
abstract class Sk_Orm_Persistent extends Orm_Meta implements Interface_Orm_Persistent
{
	/**
	 * 获得sql构建器
	 * @return Orm_Query_Builder
	 */
	public static function query_builder()
	{
		return new Orm_Query_Builder(get_called_class());
	}

	/**
	 * 构造函数
	 * @param string|array $id 主键/查询条件
	 */
	public function __construct($id = NULL)
	{
		if($id === NULL)
			return;

		// 根据id来查询结果
		$query = static::query_builder();
		if(is_array($id)) // id是多个查询条件
			$query->wheres($id);
		else // id是主键
			$query->where(static::$_primary_key, $id);
		$query->find($this);
	}

	/**
	 * 判断当前记录是否存在于db: 有原始数据就认为它是存在的
	 */
	public function exists()
	{
		return !empty($this->_original);
	}

	/**
	 * 保存数据
	 *
	 * @return int 对insert返回新增数据的主键，对update返回影响行数
	 */
	public function save()
	{
		if($this->exists())
			return $this->update();

		return $this->create();
	}

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
	public function create()
	{
		if(empty($this->_dirty))
			throw new Orm_Exception("没有要创建的数据");

		// 校验
		$this->check();

		// 插入数据库
		$pk = static::query_builder()->value($this->_dirty)->insert();

		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original; //　原始字段值
		$this->_original[static::$_primary_key] = $pk; // 主键
		$this->_dirty = array(); // 变化的字段值

		return $pk;
	}

	/**
	 * 更新数据: update sql
	 *
	 * <code>
	 *    $user = Model_User::query_builder()->where('id', 1)->find();
	 *    $user->name = "li";
	 *    $user->update();
	 * </code>
	 * 
	 * @return int 影响行数
	 */
	public function update()
	{
		if(!$this->exists())
			throw new Orm_Exception('更新对象['.static::$_name.'#'.$this->pk().']前先检查是否存在');

		if (empty($this->_dirty))
			throw new Orm_Exception("没有要更新的数据");

		// 校验
		$this->check();

		// 更新数据库
		$result = static::query_builder()->sets($this->_dirty)->where(static::$_primary_key, $this->pk())->update();

		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_dirty = array();

		return $result;
	}

	/**
	 * 删除数据: delete sql
	 *
	 *　<code>
	 *    $user = Model_User::query_builder()->where('id', '=', 1)->find();
	 *    $user->delete();
	 *　</code>
	 *
	 * @return int 影响行数
	 */
	public function delete()
	{
		if(!$this->exists())
			throw new Orm_Exception('删除对象['.static::$_name.'#'.$this->pk().']前先检查是否存在');

		//　校验
		if(!$this->check())
			return;

		// 删除数据
		$result = static::query_builder()->where(static::$_primary_key, $this->pk())->delete();

		// 更新内部数据
		$this->_original = $this->_dirty = array();

		return $result;
	}
}
