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
abstract class Sk_Db_Query_Builder_Action
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
	 * 要插入/更新字段: <column => value>
	 * 要查询的字段名: [column]
	 * @var string
	 */
	protected $_data;
	
	/**
	 * select语句中, 控制查询结果是否去重唯一
	 * @var bool
	 */
	protected $_distinct = FALSE;

	/**
	 * 构造函数
	 * 
	 * @param string $action sql动作：select/insert/update/delete
	 * @param string|Db $db 数据库配置的分组名/数据库连接
	 * @param string $table 表名
	 * @param string $data 数据
	 */
    public function __construct($action, $db = 'default', $table = NULL, $data = NULL)
    {
        $this->_action = $action;

        // 获得db
        if(!$db instanceof Db)
            $db = Db::instance($db);
        $this->_db = $db;

        if($table)
            $this->table($table);

        if($data)
            $this->data($data);
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
	 * 设置插入/更新的值
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Builder
	 */
	public function data($column, $value = NULL)
	{
		if(is_array($column))
			$this->_data = $column;
		else
			$this->_data[$column] = $value;
	
		return $this;
	}
	
	/**
	 * 设置更新的值, update时用
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Builder
	 */
	public function set($column, $value)
	{
		return $this->data($column, $value);
	}
	
	/**
	 * 设置查询的字段, select时用
	 *
	 * @param array $columns 字段名数组: array($column1, $column2, $alias => $column3), 
	 * 													如 array('name', 'age', 'birt' => 'birthday'), 其中 name 与 age 字段不带别名, 而 birthday 字段带别名 birt
	 * @return Db_Query_Builder
	 */
	public function columns(array $columns)
	{
		$this->_data = $columns;
		return $this;
	}
	
	public function distinct($value)
	{
		$this->_distinct = (bool) $value;
		return $this;
	}
	
	/**
	 * 编译动作子句
	 * @return string
	 */
	public function compile_action()
	{
		// 实际上是填充子句的参数，如将行参表名替换为真实表名
		$sql = Arr::get(static::$sql_templates, $this->_action);
		
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
	 * 编译表名: 转义
	 * @return string
	 */
	protected function _fill_table()
	{
		return $this->_db->quote_table($this->_table);
	}
	
	/**
	 * 编译多个字段名: 转义
	 * @return string
	 */
	protected function _fill_columns()
	{
		// select
		if($this->_action == 'select')
		{
			if(empty($this->_data))
				return '*';
			
			return $this->_db->quote_column($this->_data);
		}
		
		// update/insert
		if(empty($this->_data))
			return NULL;
		
		return $this->_db->quote_column(array_keys($this->_data));
	}
	
	/**
	 * 编译多个字段值: 转义
	 * @return string
	 */
	protected function _fill_values()
	{
		if(empty($this->_data))
			return NULL;
		
		return $this->_db->quote($this->_data);
	}
	
	/**
	 * 编译字段谓句: 转义 + 拼接谓句
	 * 
	 * @param stirng $operator 谓语
	 * @param string $delimiter 拼接谓句的连接符
	 * @return string
	 */
	protected function _fill_column_predicate($operator, $delimiter = ', ')
	{
		if(empty($this->_data))
			return NULL;
		
		$sql = '';
		foreach ($this->_data as $column => $value)
		{
			$column = $this->_db->quote_column($column);
			$value = $this->_db->quote($value);
			$sql .= $column.' '.$operator.' '.$value.$delimiter;
		}
		
		return rtrim($sql, $delimiter);
	}
}