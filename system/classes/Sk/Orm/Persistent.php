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
class Sk_Orm_Persistent extends Orm_MetaData implements Interface_Orm_Persistent
{
	/**
	 * 每个字段的校验规则
	 * @var array
	 */
	protected static $_rules = array();

	/**
	 * 每个字段的标签（中文名）
	 * @var array
	 */
	protected static $_labels = array();

	/**
	 * 获得sql构建器: (select) sql
	 *
	 * @param string $action
	 * @return Orm_Query_Builder
	 */
	public static function query_builder($action = 'select')
	{
		return new Orm_Query_Builder(get_called_class(), $action, static::db($action), static::table());
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
			$query->where(static::$_primary_key, '=', $id);
		$rows = $query->execute();
		$this->_original = Arr::get($rows, 0, array());
	}

	/**
	 * 校验数据
	 * @return boolean
	 */
	public function check()
	{
		// 逐个字段校验
		foreach (static::$_rules as $column => $exp)
		{
			$value = $last = $this->$column;
			// 校验单个字段: 字段值可能被修改
			if(!Validation::execute($exp, $value, $this, $message))
			{
				$label = Arr::get(static::$_labels, $column, $column); // 字段标签（中文名）
				throw new Orm_Exception($label.$message);
			}

			// 更新被修改的字段值
			if($value !== $last)
				$this->_dirty[$column] = $value;
		}

		return TRUE;
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
	 *    $user = new Model_User();
	 *    $user->name = 'shi';
	 *    $user->age = 24;
	 *    $user->create();
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
		static::query_builder('insert')->data($this->_dirty)->execute();

		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_original[static::$_primary_key] = $pk = static::db()->last_insert_id(); // 主键
		$this->_dirty = array();

		return $pk;
	}

	/**
	 * 更新数据: update sql
	 *
	 *    $user = Model_User::query_builder()->where('id', '=', 1)->find();
	 *    $user->name = "li";
	 *    $user->update();
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
		$result = static::query_builder('update')->data($this->_dirty)->where(static::$_primary_key, '=', $this->pk())->execute();

		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_dirty = array();

		return $result;
	}

	/**
	 * 更新数据: update sql
	 *
	 *    $user = Model_User::query_builder()->where('id', '=', 1)->find();
	 *    $user->name = "li";
	 *    $user->update();
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
		$result = static::query_builder('delete')->where(static::$_primary_key, '=', $this->pk())->execute();

		// 更新内部数据
		$this->_original = $this->_dirty = array();

		return $result;
	}
}
