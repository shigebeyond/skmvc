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
			if($type == 'belongs_to')
				$this->$foreign_key = $value->pk();
			return TRUE;
		}
		
		return parent::try_set($column, $value);
	}
	
	/**
	 * 获得关联对象
	 *
	 * @param string $name 关联对象名
	 * @return Orm
	 */
	public function _related($name)
	{
		if(!isset($this->_related[$name]))
		{
			// 根据关联关系来构建查询
			extract(static::$_relations[$name]);
			$class = 'Model_'.ucfirst($model);
			switch ($type)
			{
				case 'belongs_to': // belongs_to: 查主表
					$this->_related[$name] = $this->_query_master($class, $foreign_key)->find();
					break;
				case 'has_many': // has_xxx: 查从表
					$this->_related[$name] = $this->_query_slave($class, $foreign_key)->limit(1)->find();
					break;
				case 'has_one': // has_xxx: 查从表
					$this->_related[$name] = $this->_query_slave($class, $foreign_key)->find_all();
					break;
			}
		}
		return $this->_related[$name];
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
	
}