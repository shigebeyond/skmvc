<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 封装查询结果的访问器 TODO: 支持缓存查询结果, config/db.php中的cache控制
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-11 下午11:19:20 
 *
 */
// class Sk_Db_Result extends PDOStatement // 结果访问器不应该混杂PDOStatement的api
class Sk_Db_Result implements Iterator 
{
	/**
	 * 
	 * @var PDOStatement
	 */
	protected $_statement;

	/**
	 * 当前行
	 * @var unknown
	 */
	protected $_row = FALSE;
	
	/**
	 * 当前行数
	 * @var int
	 */
	protected $_row_no = 0;

	public function __construct($statement)
	{
		$this->_statement = $statement;
	}

	/**
	 * 关闭结果集
	 */
	public function __destruct()
	{
		$this->_statement->closeCursor();
		$this->_statement = null;
	}

	/**
	 * 获得当前行
	 * @see Iterator::current()
	 */
	public function current()
	{
		return $this->valid() ? $this->_row : NULL;
	}


	/**
	 * Return all of the rows in the statement as an array.
	 *
	 *     // Indexed array of all rows
	 *     $rows = $statement->as_array();
	 *
	 *     // Associative array of rows by "id"
	 *     $rows = $statement->as_array('id');
	 *
	 *     // Associative array of rows, "id" => "name"
	 *     $rows = $statement->as_array('id', 'name');
	 *
	 * @param   string  $key_column    返回数组的key的字段名
	 * @param   string  $value_column  返回数组的value的字段名
	 * @return  array
	 */
	public function as_array($key_column = NULL, $value_column = NULL)
	{
		$this->rewind();
		
		// return $this->_statement->fetchAll(); // 不能指定要返回的 key_field 与 value_field
		
		$results = array();
		
		foreach ($this as $i => $row)
		{
			$key = $key_column === NULL ? $i : $row[$key_column];
			$value = $value_column === NULL ? $row : $row[$value_column];
			$results[] = $value;
		}
		
		return $results;
		
	}

	/**
	 * 获得当前行的字段值
	 *
	 *     // 获得字段"id"的值
	 *     $id = $result->get('id');
	 *
	 * @param   string  $name     字段名
	 * @param   mixed   $default  默认值
	 * @return  mixed
	 */
	public function get($name, $default = NULL)
	{
		$row = $this->current();

		if (isset($row[$name]))
			return $row[$name];

		return $default;
	}

	/**
	 * 获得当前行数
	 * @see Iterator::key()
	 */
	public function key()
	{
		return $this->_row_no;
	}

	/**
	 * 移动到下一行
	 * @see Iterator::next()
	 */
	public function next()
	{
		$this->_row = $this->_statement->fetch();
		$this->_row_no++;
		return $this;
	}

	/**
	 * 移动到上一行
	 * @throws Exception
	 */
	public function prev()
	{
		throw new Exception("不能移动到是上一行");
	}

	/**
	 * 重置当前行为0
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		// 未移动过
		if($this->_row === FALSE)
			return $this;
		
		// 移动过
		throw new Exception("不能重置当前行");
	}

	/**
	 * 检查当前行是否存在
	 * 注意：该方法只在内部使用
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		return $this->_row !== FALSE;
	}

}
