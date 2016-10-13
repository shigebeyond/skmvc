<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器
 * 	延迟拼接sql, 因为调用方法时元素无序, 但生成sql时元素有序
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query
{
	/**
	 * 数据库连接
	 * @var Db
	 */
	protected $_db;
	
	/**
	 * 表名
	 * @var string
	 */
	protected $_table;
	
	/**
	 * 要插入/更新字段: <column => value>
	 * 要查询的字段名: [column]
	 * @var string
	 */
	protected $_data;
	
	public function __construct($db, $table = NULL)
	{
		$this->_db = $db;
		$this->_table = $table;
	}
	
	/**
	 * 设置表名
	 * @param string $table
	 * @return Sk_Db_Query
	 */
	public function table($table)
	{
		$this->_table = $table;
		return $this;
	}
	
	/**
	 * 设置表名
	 * @param string $table
	 * @return Sk_Db_Query
	 */
	public function from($table)
	{
		return $this->table($table);
	}
	
	/**
	 * 设置插入/更新的值
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query
	 */
	public function data($column, $value)
	{
		if(is_array($column))
			$this->_data = $column;
		else
			$this->_data[$column] = $value;
	
		return $this;
	}
	
	/**
	 * 执行
	 */
	public function execute()
	{
		$sql = $this->compile();
		
	}
	
	/**
	 * 编译sql
	 * @return string
	 */
	public function compile()
	{
		// 动作子句 + 修饰子句
		return $this->compile_action() . $this->compile_decoration();
	}
	
	/**
	 * 编译动作子句
	 * @return string
	 */
	public abstract function compile_action();
	
	/**
	 * 编译修饰子句
	 * @return string
	 */
	public abstract function compile_decoration();
	
	public static function select($db, $table = NULL)
	{
		return new Db_Query_Action_Select($db, $table);
	}
	
	public static function insert($db, $table = NULL)
	{
		return new Db_Query_Action_Insert($db, $table);
	}
	
	public static function update($db, $table = NULL)
	{
		return new Db_Query_Action_Update($db, $table);
	}
	
	public static function delete($db, $table = NULL)
	{
		return new Db_Query_Action_Delete($db, $table);
	}
}