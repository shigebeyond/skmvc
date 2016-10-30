<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 面向orm对象的sql构建器
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-16 下午8:02:28
 *
 */
class Sk_Orm_Query_Builder extends Db_Query_Builder implements Interface_Orm_Query_Builder
{
	/**
	 * model的类
	 * @var string
	 */
	protected $_class;

	/**
	 * 构造函数
	 *
	 * string $class model类名，其基类为Orm
	 */
	public function __construct($class)
	{
		// 检查是否是orm子类
		if(!is_subclass_of($class, 'Orm'))
			throw new Orm_Exception('Orm_Query_Builder::_class 必须是 Orm 的子类');
		
		parent::__construct(array($class, 'db')/* 获得db的回调  */, $class::table());
		$this->_class = $class;
	}

	/**
	 * 查询单个: select　语句
	 * 
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_COLUMN，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return Orm
	 */
	public function find($fetch_value = FALSE)
	{
		$data = parent::find();
		if(!$data)
			return NULL;
		
		if($fetch_value instanceof Orm) // 已有对象
			$model = $fetch_value;
		else // 新对象
			$model = new $this->_class;
		
		//　设置原始属性值
		return $model->original($data);
	}

	/**
	 * 查找多个： select 语句
	 *
	 * @param bool|int|string|Orm $fetch_value $fetch_value 如果类型是int，则返回某列FETCH_COLUMN，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return array
	 */
	public function find_all($fetch_value = FALSE)
	{
		$rows = parent::find_all($fetch_value);
		foreach ($rows as $key => $row)
			$rows[$key] = (new $this->_class)->original($row);
		return $rows;
	}

	/**
	 * 联查表
	 *
	 * @param string $name 关联关系名
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Sk_Orm_Query_Builder
	 */
	public function with($name, array $columns = NULL)
	{
		$class = $this->_class;

		// select当前表字段
		if(empty($this->_data))
			$this->select(array($class::table().'.*'));

		// 获得关联关系
		$relation = $class::relation($name);
		if($relation)
		{
			// 根据关联关系联查表
			extract($relation);
			$class = 'Model_'.ucfirst($model);
			switch ($type)
			{
				case 'belongs_to': // belongs_to: 查主表
					$this->_join_master($class, $foreign_key, $name);
				case 'has_many': // has_xxx: 查从表
				case 'has_one': // has_xxx: 查从表
					$this->_join_slave($class, $foreign_key, $name);
			}
			// select关联表字段
			$this->_select_related($class, $name);
		}

		return $this;
	}

	/**
	 * 联查从表
	 *     从表.外键 = 主表.主键
	 *
	 * @param string $slave 从类
	 * @param string $foreign_key 外键
	 * @param string $table_alias 表别名
	 * @return Orm_Query_Builder
	 */
	protected function _join_slave($slave, $foreign_key, $table_alias)
	{
		// 联查从表
		$master = $this->_class;
		$master_pk = $master::table().'.'.$master::primary_key();
		$slave_fk = $table_alias.'.'.$foreign_key;
		return $this->join(array($table_alias => $slave::table()), 'LEFT')->on($slave_fk, '=', $master_pk); // 从表.外键 = 主表.主键
	}

	/**
	 * 联查主表
	 *     主表.主键 = 从表.外键
	 *
	 * @param string $master 主类
	 * @param string $foreign_key 外键
	 * @param string $table_alias 表别名
	 * @return Orm_Query_Builder
	 */
	protected function _join_master($master, $foreign_key, $table_alias)
	{
		// 联查从表
		$slave = $this->_class;
		$master_pk = $master::table().'.'.$master::primary_key();
		$slave_fk = $slave::table().'.'.$foreign_key;
		return $this->join($master::table(), 'LEFT')->on($master_pk, '=', $slave_fk); // 主表.主键 = 从表.外键
	}

	/**
	 * select关联表的字段
	 *
	 * @param string $class 关联类
	 * @param string $table_alias 表别名
	 * @param array $columns 查询的列
	 */
	protected function _select_related($class, $table_alias, array $columns = NULL)
	{
		// 默认查询全部列
		if($columns === NULL)
			$columns = array_keys($class::columns());

		// 构建列别名
		$select = array();
		foreach ($columns as $column)
		{
			$column_alias = $table_alias.':'.$column; // 列别名 = 表别名 : 列名，以便在设置orm对象字段值时，可以逐层设置关联对象的字段值
			$column = $table_alias.'.'.$column;
			$select[$column_alias] = $column;
		}
		return $this->select($select);
	}

}
