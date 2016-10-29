<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Odm之持久化，主要是负责数据库的增删改查
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-29 下午10:01:52 
 *
 */
abstract class Sk_Odm_Persistent extends Odm_MetaData implements Interface_Odm_Persistent
{
	/**
	 * 获得mongo操作构建器
	 *
	 * @param string $action mongo操作：find/findOne/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Odm_Query_Builder
	 */
	public static function query_builder($action = 'find')
	{
		return new Odm_Query_Builder(get_called_class(), static::db($action), static::collection());
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
			$query->where('_id', $id);
		$rows = $query->find_all();
		$this->_original = Arr::get($rows, 0, array());
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
	 * 插入数据: insert
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
		static::query_builder('insert')->data($this->_dirty)->insert();

		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_original['_id'] = $pk = static::db()->last_insert_id(); // 主键
		$this->_dirty = array();

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
		$result = static::query_builder('update')->data($this->_dirty)->where('_id', $this->pk())->update();

		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_dirty = array();

		return $result;
	}

	/**
	 * 删除数据: delete sql
	 *
	 *　<code>
	 *    $user = Model_User::query_builder()->where('id', 1)->find();
	 *    $user->delete();
	 *　</code>
	 *
	 * @return int 影响行数
	 */
	public function delete()
	{
		if(!$this->exists())
			throw new Orm_Exception('删除对象['.static::$_name.'#'.$this->pk().']前先检查是否存在');

		if(!$this->check())
			return;

		// 删除数据
		$result = static::query_builder('delete')->where('_id', $this->pk())->delete();

		// 更新内部数据
		$this->_original = $this->_dirty = array();

		return $result;
	}
}
