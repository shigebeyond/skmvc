<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 动作子句: 由动态select/insert/update/delete来构建的子句
 *   通过字符串模板来实现
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
abstract class Sk_Db_Query_Builder_Action implements Interface_Db_Query_Builder_Action
{
	/**
	 * 动作子句的sql模板
	 * @var array
	 */	
	public static $sql_templates = array(
		'select' => 'SELECT :distinct :columns FROM :table', 
		'insert' => 'INSERT INTO :table (:columns) VALUES :values', // quote_column 默认不加(), quote_value 默认加() 
		'update' => 'UPDATE :table SET :column = :value',
		'delete' => 'DELETE FROM :table'
	);
	
	/**
	 * 动作
	 * @var string
	 */
	protected $_action;
	
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
	 * 要插入的多行: [<column => value>]
	 * 要更新字段值: <column => value>
	 * 要查询的字段名: [alias => column]
	 * @var array
	 */
	protected $_data = array ();
	
	/**
	 * select语句中, 控制查询结果是否去重唯一
	 * @var bool
	 */
	protected $_distinct = FALSE;
	
	/**
	 * sql参数
	 * @var array
	 */
	protected $_params = NULL;
	
	/**
	 * 构造函数
	 *
	 * @param Db|Callable $db 数据库连接|回调
	 * @param string $table 表名
	 */
	public function __construct($db, $table = NULL) 
	{
		// 设置db
		$this->_db = $db;
		
		//　设置表
		if ($table)
			$this->table ( $table );
	}
	
	/**
	 * 设置动作
	 * 　　延时设置动作，此时可获得对应的数据库连接
	 * 
	 * @param string $action sql动作：select/insert/update/delete
	 * @return Db_Query_Builder
	 */
	public function action($action)
	{
		if(!$action)
			throw new Db_Exception('未指定sql动作');
		
		$this->_action = $action;
		
		//　如果db是回调，则调用他来根据action来获得对应的数据库连接
		if(is_callable($this->_db))
			$this->_db = call_user_func($this->_db, $action);
		
		return $this;
	}
	
	/**
	 * 设置表名: 一般是单个表名
	 * @param tables 表名数组: array($table1, $table2, $alias => $table3), 
	 * 								  如 array('user', 'contact', 'addr' => 'user_address'), 其中 user 与 contact 表不带别名, 而 user_address 表带别名 addr
	 * @return Db_Query_Builder
	 */
	public function table($tables)
	{
		return $this->_tables($tables);
	}
	
	/**
	 * 设置表名: 可能有多个表名
	 * @param tables 表名数组: array($table1, $table2, $alias => $table3), 
	 * 								  如 array('user', 'contact', 'addr' => 'user_address'), 其中 user 与 contact 表不带别名, 而 user_address 表带别名 addr
	 * @return Db_Query_Builder
	 */
	public function from($tables)
	{
		return $this->_tables($tables);
	}
	
	/**
	 * 处理多个表名的设置
	 * @param tables 表名数组: array($table1, $table2, $alias => $table3), 
	 * 								  如 array('user', 'contact', 'addr' => 'user_address'), 其中 user 与 contact 表不带别名, 而 user_address 表带别名 addr
	 */
	protected function _tables($tables) 
	 {
		$this->_table = $tables;
		return $this;
	}

	/**
	 * 设置插入的单行, insert时用
	 *
	 * @param array $row
	 * @return Db_Query_Builder
	 */
	public function value(array $row)
	{
		$this->_data[] = $row;
		return $this;
	}
	
	/**
	 * 设置插入的多行, insert时用
	 *
	 * @param array $rows
	 * @return Db_Query_Builder
	 */
	public function values(array $rows)
	{
		$this->_data += $rows;
		return $this;
	}
	
	/**
	 * 设置更新的单个值, update时用
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return Db_Query_Builder
	 */
	public function set($column, $value = NULL)
	{
		$this->_data[$column] = $value;
		return $this;
	}
	
	/**
	 * 设置更新的多个值, update时用
	 *
	 * @param array $row
	 * @return Db_Query_Builder
	 */
	public function sets(array $row)
	{
		$this->_data = $row;
		return $this;
	}
	
	/**
	 * 设置查询的字段, select时用
	 *
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Db_Query_Builder
	 */
	public function select(array $columns)
	{
		$this->_data = $this->_data + $columns; // 假设: 有先后, 无覆盖
		return $this;
	}
	
	/**
	 * 设置查询结果是否去重唯一
	 * 
	 * @param boolean $value
	 * @return Sk_Db_Query_Builder_Action
	 */
	public function distinct($value)
	{
		$this->_distinct = (bool) $value;
		return $this;
	}
	
	/**
	 * 清空条件
	 * @return Db_Query_Builder
	 */
	public function clear()
	{
		$this->_table = NULL;
		$this->_data = array();
		$this->_distinct = FALSE;
		$this->_params = NULL;
	}
	
	/**
	 * 编译动作子句
	 * @return string
	 */
	public function compile_action()
	{
		// 清空sql参数
		$this->_params = array();
		
		// 实际上是填充子句的参数，如将行参表名替换为真实表名
		$sql = Arr::get(static::$sql_templates, $this->_action);
		
		if(!$sql)
			throw new Db_Exception("无效sql动作: $this->_action");
		
		// 1 填充表名/多个字段名/多个字段值
		// 针对 select :columns from :table / insert into :table :columns values :values
		$sql = preg_replace_callback('/:(table|columns|values)/', function($mathes){
			// 调用对应的方法: _fill_table() / _fill_columns() / _fill_values()
			$method = '_fill_'.$mathes[1];
			return $this->$method();
		}, $sql);
		
		// 2 填充字段谓句
		// 针对 update :table set :column = :value
		$sql = preg_replace_callback('/:column(.+):value/', function($mathes){
			return $this->_fill_column_predicate($mathes[1]);
		}, $sql);
		
		// 3 填充distinct
		return str_replace(':distinct', $this->_distinct ? 'distinct' : '', $sql);
	}
	
	/**
	 * 改写转义值的方法，搜集sql参数
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function quote($value)
	{
		// 1 将参数值直接拼接到sql
		//return $this->_db->quote($value);
	
		// 2 sql参数化: 将参数名拼接到sql, 独立出参数值, 以便执行时绑定参数值
		$this->_params[] = $value;
		return '?';
	}
	
	/**
	 * 编译表名: 转义
	 * @return string
	 */
	protected function _fill_table()
	{
		return $this->_db->quote_table($this->_table);
	}
	
	/**
	 * 编译多个字段名: 转义
	 *     select/insert时用
	 *     
	 * @return string
	 */
	protected function _fill_columns()
	{
		// 1 select子句:  $this->_data是要查询的字段名, [alias => column]
		if($this->_action == 'select')
		{
			if(empty($this->_data))
				return '*';
			
			return $this->_db->quote_column($this->_data);
		}
		
		// 2 insert子句:  $this->_data是要插入的多行: [<column => value>]
		if(empty($this->_data))
			return NULL;
		
		// 取得第一行的keys
		$columns = array_keys(current($this->_data));
		return $this->_db->quote_column($columns);
	}
	
	/**
	 * 编译多个字段值: 转义
	 *     insert时用
	 *     
	 * @return string
	 */
	protected function _fill_values()
	{
		// insert子句:  $this->_data是要插入的多行: [<column => value>]
		if(empty($this->_data))
			return NULL;
		
		//对每行执行$this->quote($row);
		return implode(', ', array_map(array($this, 'quote'), $this->_data));
	}
	
	/**
	 * 编译字段谓句: 转义 + 拼接谓句
	 *    update时用
	 *    
	 * @param stirng $operator 谓语
	 * @param string $delimiter 拼接谓句的连接符
	 * @return string
	 */
	protected function _fill_column_predicate($operator, $delimiter = ', ')
	{
		// update子句:  $this->_data是要更新字段值: <column => value>
		if(empty($this->_data))
			return NULL;
		
		$sql = '';
		foreach ($this->_data as $column => $value)
		{
			$column = $this->_db->quote_column($column);
			$value = $this->quote($value);
			$sql .= $column.' '.$operator.' '.$value.$delimiter;
		}
		
		return rtrim($sql, $delimiter);
	}
}