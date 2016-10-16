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
class Sk_Orm_Persistent extends Orm_MetaData
{
	/**
	 * 构造函数
	 * @param string|array $id 主键/查询条件
	 */
	public function __construct($id = NULL)
	{
		if($id === NULL)
			return;
		
		$query = static::query_builder();
		if(is_array($id))
		{
			foreach ($id as $column => $value)
				$query->where($column, '=', $value);
		}
		else 
		{
			$query->where(static::$_primary_key, '=', $id);
		}
		$rows = $query->execute();
		$this->_original = Arr::get($rows, 0, array());
	}
	
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
	 * 保存数据
	 * 
	 * @return int 对insert返回新增数据的主键，对update返回影响行数
	 */
	public function save()
	{
		if($this->pk())
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
			return $this;
		
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
		if (empty($this->_dirty))
			return $this;

		// 更新数据库
		$result = static::query_builder('update')->data($this->_dirty)->where(static::$_primary_key, '=', $this->pk())->execute();
		
		// 更新内部数据
		$this->_original = $this->_dirty + $this->_original;
		$this->_dirty = array();
		
		return $result;
	}
}