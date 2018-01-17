<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 面向odm对象的查询构建器
 *
 *
 * @Package package_name
 * @category
 * @author shijianhang
 * @date 2017-3-2
 */
class Sk_Odm_Query_Builder extends Db_Query_Builder implements Interface_Orm_Query_Builder
{
	/**
	 * model的类
	 * @var string
	 */
	protected $_class;
	
	/**
	 * 关联查询模型及字段
	 * @var array
	 */
	protected $_withs = array();

	/**
	 * 构造函数
	 *
	 * string $class model类名，其基类为Odm
	 */
	public function __construct($class)
	{
		// 检查是否是orm子类
		if(!is_subclass_of($class, 'Odm'))
			throw new Orm_Exception('Odm_Query_Builder::_class 必须是 Odm 的子类');
		
		parent::__construct(array($class, 'db')/* 获得db的回调  */, $class::table());
		$this->_class = $class;
	}
	
	/**
	 * 清空条件
	 * @return Odm_Query_Builder
	 */
	public function clear()
	{
		$this->$_withs = array();
		
		return parent::clear();
	}

	/**
	 * 查询单个: select　语句
	 * 
	 * @param bool|int|string|Odm $fetch_value $fetch_value 如果类型是int，则返回该列FETCH_COLUMN，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return Odm
	 */
	public function find($fetch_value = FALSE)
	{
		$data = parent::find();
		if(!$data)
			return NULL;
		
		if($fetch_value instanceof Odm) // 已有对象
			$model = $fetch_value;
		else // 新对象
			$model = new $this->_class;
		
		// 设置原始属性值
		$model->setOriginal($data);
		
		// 关联查询
		foreach ($this->_withs as $name => $columns)
			$model->related($name, FALSE, $columns);
		
		return $model;
	}

	/**
	 * 查找多个： select 语句
	 *
	 * @param bool|int|string|Odm $fetch_value $fetch_value 如果类型是int，则返回该列FETCH_COLUMN，如果类型是string，则返回指定类型的对象，如果类型是object，则给指定对象设置数据, 其他返回关联数组
	 * @return array
	 */
	public function find_all($fetch_value = FALSE)
	{
		$rows = parent::find_all($fetch_value);
		foreach ($rows as $key => $row)
		{
			// 设置原始属性值
			$model = new $this->_class;
			$model->setOriginal($row);
			
			// 关联查询
			foreach ($this->_withs as $name => $columns)
				$model->related($name, FALSE, $columns);
			
			$rows[$key] = $model;
		}
		return $rows;
	}

	/**
	 * 联查表
	 *
	 * @param string $name 关联关系名
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Sk_Odm_Query_Builder
	 */
	public function with($name, array $columns = NULL)
	{
		$this->_withs[$name] = $columns;
		return $this;
	}

}
