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
class Sk_Orm_Query_Builder extends Db_Query_Builder
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
	 * @param string $action sql动作：select/insert/update/delete
	 * @param string|Db $db 数据库配置的分组名/数据库连接
	 * @param string $table 表名
	 * @param string $data 数据
	 */
	public function __construct($class, $action = 'select', $db = 'default', $table = NULL, $data = NULL)
	{
		parent::__construct($action, $db, $table, $data);

		// 检查是否是orm子类
		if(!is_subclass_of($class, 'Orm'))
			throw new Orm_Exception('Orm_Query_Builder::_class 必须是 Orm 的子类');
		$this->_class = $class;
	}

	/**
	 * 查询单个
	 * @return
	 *
	 */
	public function find()
	{
		$rows = $this->limit(1)->find_all();
		return Arr::get($rows, 0);
	}

	/**
	 * 查询多个
	 * @return array
	 */
	public function find_all()
	{
		$rows = $this->execute();
		foreach ($rows as $key => $row)
		{
			$orm = new $this->_class;
			$orm->original($row); // 设置原始字段值
			$rows[$key] = $orm;
		}
		return $rows;
	}

	/**
	 * 联查表
	 *
	 * @param string $name 关联关系名
	 * @return Sk_Orm_Query_Builder
	 */
	public function with($name)
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
