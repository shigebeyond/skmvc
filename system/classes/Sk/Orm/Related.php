<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之关联对象操作
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34
 *
 */
class Sk_Orm_Related extends Orm_Persistent
{
	/**
	 * 关联关系 - 有一个
	 *    当前表是主表, 关联表是从表
	 */
	const RELATION_BELONGS_TO = 'belongs_to';

	/**
	 * 关联关系 - 有多个
	 * 	当前表是主表, 关联表是从表
	 */
	const RELATION_HAS_MANY = 'has_many';

	/**
	 * 关联关系 - 从属于
	 *    当前表是从表, 关联表是主表
	 */
	const RELATION_HAS_ONE = 'has_one';

	/**
	 * 自定义关联关系
	 * @var array
	 */
	protected static $_relations = array(
	);

	/**
	 * 获得关联关系
	 *
	 * @param string $name
	 * @return array
	 */
	public static function relation($name = NULL)
	{
		if($name === NULL)
			return static::$_relations;

		return Arr::get(static::$_relations, $name);
	}

	/**
	 * 缓存关联对象
	 * @var array <name => Orm>
	 */
	protected $_related = array();

	/**
	 * 尝试获得对象字段
	 *
	 * @param   string $column 字段名
	 * @param   mixed $value 字段值，引用传递，用于获得值
	 * @return  bool
	 */
	public function try_get($column, &$value)
	{
		// 获得关联对象
		if (isset(static::$_relations[$column]))
		{
			$value = $this->_related($column);
			return TRUE;
		}

		return parent::try_get($column, $value);
	}

	/**
	 * 尝试设置字段值
	 *
	 * @param  string $column 字段名
	 * @param  mixed  $value  字段值
	 * @return ORM
	 */
	public function try_set($column, $value)
	{
		// 设置关联对象
		if (isset(static::$_relations[$column]))
		{
			$this->_related[$column] = $value;
			// 如果关联的是主表，则更新从表的外键
			extract(static::$_relations[$column]);
			if($type == static::RELATION_BELONGS_TO)
				$this->$foreign_key = $value->pk();
			return TRUE;
		}

		return parent::try_set($column, $value);
	}

	/**
	 * 获得/设置原始的字段值
	 *
	 * @param array $original
	 * @return Orm|array
	 */
	public function original(array $original = NULL)
	{
		// getter
		if ($original === NULL)
			return $this->_original;

		// setter
		foreach ($original as $column => $value)
		{
			$i = strpos($column, ':'); // 关联查询时，会设置关联表字段的列别名（列别名 = 表别名 : 列名），可以据此来设置关联对象的字段值
			if($i === FALSE) // 自身字段
			{
				$this->_original[$column] = $value;
			}
			else // 关联对象字段
			{
				$name = substr($column, 0, $i);
				$column = substr($column, $i + 1);
				$this->_related($name)->_original[$column] = $value;
			}
		}

		return $this;
	}

	/**
	 * 获得关联对象
	 *
	 * @param string $name 关联对象名
	 * @param boolean $new 是否创建新对象：在查询db后设置原始字段值original()时使用
	 * @return Orm
	 */
	public function _related($name, $new = FALSE)
	{
		// 已缓存
		if(isset($this->_related[$name]))
			return $this->_related[$name];

		// 获得关联关系
		extract(static::$_relations[$name]);
		$class = 'Model_'.ucfirst($model);

		// 创建新对象
		if($new)
			return $this->_related[$name] = new $class;

		// 根据关联关系来构建查询
		$obj = NULL;
		switch ($type)
		{
			case static::RELATION_BELONGS_TO: // belongs_to: 查主表
				$obj = $this->_query_master($class, $foreign_key)->find();
				break;
			case static::RELATION_HAS_ONE: // has_xxx: 查从表
				$obj = $this->_query_slave($class, $foreign_key)->limit(1)->find();
				break;
			case static::RELATION_HAS_MANY: // has_xxx: 查从表
				$obj = $this->_query_slave($class, $foreign_key)->find_all();
				break;
		}

		return $this->_related[$name] = $obj;
	}

	/**
	 * 查询关联的从表
	 *
	 * @param string $class 从类
	 * @param string $foreign_key 外键
	 * @return Orm_Query_Builder
	 */
	protected function _query_slave($class, $foreign_key)
	{
		return $class::query_builder()->where($foreign_key, '=', $this->pk()); // 从表.外键 = 主表.主键
	}

	/**
	 * 查询关联的主表
	 *
	 * @param string $class 主类
	 * @param string $foreign_key 外键
	 * @return Orm_Query_Builder
	 */
	protected function _query_master($class, $foreign_key)
	{
		return $class::query_builder()->where($class::$_primary_key, '=', $this->$foreign_key); // 主表.主键 = 从表.外键
	}

	/**
	 * 获得字段值
	 * @return array
	 */
	public function as_array()
	{
		$result = parent::as_array();

		// 包含已加载的关联对象
		foreach ($this->_related as $name => $model)
			$result[$name] = $model->as_array();

		return $result;
	}

}
