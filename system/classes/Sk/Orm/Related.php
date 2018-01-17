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
abstract class Sk_Orm_Related extends Orm_Persistent implements Interface_Orm_Related
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
	protected static $_relations = array();
	
	/**
	 * 检查是否有关联关系
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function has_relation($name = NULL){
		return isset(static::$_relations[$column]);
	}

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
	 * 返回要序列化的属性
	 * @return array
	 */
	public function __sleep()
	{
		$props = parent::__sleep();
		$props[] = '_related';
		return $props;
	}

	/**
	 * 判断对象是否存在指定字段
	 *
	 * @param  string $column Column name
	 * @return boolean
	 */
	public function __isset($column)
	{
		return static::has_relation($column) && parent::__isset($column);
	}
	
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
		if (static::has_relation($column))
		{
			$value = $this->related($column);
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
		if (static::has_relation($column))
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
	 * 删除某个字段值
	 *
	 * @param  string $column 字段名
	 * @return
	 */
	public function __unset($column)
	{
		parent::__unset($column);
		unset($this->_related[$column]);
	}

	/**
	 * 设置原始的字段值
	 *
	 * @param array $original
	 * @return Orm
	 */
	public function setOriginal(array $original)
	{
		foreach ($original as $column => $value)
		{
			// 关联查询时，会设置关联表字段的列别名（列别名 = 表别名 : 列名），可以据此来设置关联对象的字段值
			if(strpos($column, ':') === FALSE) // 自身字段
			{
				$this->_original[$column] = $value;
			}
			elseif($value !== NULL) // 关联对象字段: 不处理NULL的值, 因为left join查询时, 关联对象可能没有匹配的行
			{
				list($name, $column) = explode(':', $column);
				$obj = $this->related($name, TRUE); // 创建关联对象
				$obj->_original[$column] = $value;
			}
		}

		return $this;
	}

	/**
	 * 获得关联对象
	 *
	 * @param string $name 关联对象名
	 * @param boolean $new 是否创建新对象：在查询db后设置原始字段值setOriginal()时使用
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Orm
	 */
	public function related($name, $new = FALSE, $columns = NULL)
	{
		// 已缓存
		if(isset($this->_related[$name]))
			return $this->_related[$name];

		// 获得关联关系
		extract(static::$_relations[$name]);
		
		// 获得关联模型类
		$class = 'Model_'.ucfirst($model);

		// 创建新对象
		if($new)
			return $this->_related[$name] = new $class;

		// 根据关联关系来构建查询
		$result = NULL;
		
		if(!isset($conditions))// 关联查询的额外条件
			$conditions = array();
		
		switch ($type)
		{
			case static::RELATION_BELONGS_TO: // belongs_to: 查主表
				$result = $this->_query_master($class, $foreign_key, $conditions)->select($columns)->find();
				break;
			case static::RELATION_HAS_ONE: // has_xxx: 查从表
				$result = $this->_query_slave($class, $foreign_key, $conditions)->select($columns)->find();
				break;
			case static::RELATION_HAS_MANY: // has_xxx: 查从表
				$result = $this->_query_slave($class, $foreign_key, $conditions)->select($columns)->find_all();
				break;
		}

		return $this->_related[$name] = $result;
	}

	/**
	 * 查询关联的从表
	 *
	 * @param string $class 从类
	 * @param string $foreign_key 外键
	 * @param array $conditions 关联查询的额外条件
	 * @return Orm_Query_Builder
	 */
	protected function _query_slave($class, $foreign_key, $conditions)
	{
		return $class::query_builder()->wheres($conditions)->where($foreign_key, $this->pk()); // 从表.外键 = 主表.主键
	}

	/**
	 * 查询关联的主表
	 *
	 * @param string $class 主类
	 * @param string $foreign_key 外键
	 * @param array $conditions 关联查询的额外条件
	 * @return Orm_Query_Builder
	 */
	protected function _query_master($class, $foreign_key, $conditions)
	{
		return $class::query_builder()->wheres($conditions)->where($class::$_primary_key, $this->$foreign_key); // 主表.主键 = 从表.外键
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
