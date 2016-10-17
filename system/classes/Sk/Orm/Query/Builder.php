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
			throw new Exception('Orm_Query_Builder::_class 必须是 Orm 的子类');
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
}
