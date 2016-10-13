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
abstract class Sk_Db_Query_Action extends Db_Query_Decoration
{
	// 动作子句模板: select
	const ACTION_TEMPLATE_SELECT = 'SELECT :keys FROM :table';
	
	// 动作子句模板: insert
	const ACTION_TEMPLATE_INSERT = 'INSERT INTO :table (:keys) VALUES (:values)';
	
	// 动作子句模板: update
	const ACTION_TEMPLATE_UPDATE = 'UPDATE :table SET :key = :value';
	
	// 动作子句模板: delete
	const ACTION_TEMPLATE_DELETE = 'DELETE FROM :table';
	
	/**
	 * 动作子句模板: select/insert/update/delete
	 * @var string
	 */
	protected $_action_template;
	
	/**
	 * 设置更新的值, update时用
	 *
	 * @param string $column
	 * @param string $value
	 * @return Db_Query_Action
	 */
	public function set($column, $value)
	{
		return $this->data($column, $value);
	}
	
	/**
	 * 设置查询的字段, select时用
	 *
	 * @param string... $columns
	 * @return Db_Query_Action
	 */
	public function select($columns)
	{
		$columns = func_get_args();
		foreach ($columns as $key => $column)
		{
			// 对有别名的字段,如 array('column', 'alias'), 将alias转换关联数组的键, column为值 
			if(is_array($column)){
				unset($columns[$key]);
				$columns[$column[0]] = $column[1];
			}
		}
		return $this->data($columns);
	}
	
	/**
	 * 编译动作子句
	 * @see Sk_Db_Query::compile_action()
	 */
	public function compile_action()
	{
		// 实际上是填充子句的参数，如将行参表名替换为真实表名
		
		// 1 填充表名/多个字段名/多个字段值
		// 针对 select :columns from :table / insert into :table :columns values :values
		$action = preg_replace_callback('/:(table|columns|values)/', function($mathes){
			// 调用对应的方法: _fill_table() / _fill_columns() / _fill_values()
			$method = '_fill_'.$mathes[1];
			return $this->$method();
		}, $this->_action_template);
		
		// 2 填充字段谓句
		// 针对 update :table set :column = :value
		return preg_replace_callback('/:column(.+):value/', function($mathes){
			return $this->_fill_column_predicate($mathes[1]);
		}, $action);
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
		if($this->_action_template == static::ACTION_TEMPLATE_SELECT)
		{
			if(empty($this->_data))
				return '*';
			
			return $this->_db->quote_column($this->_data, ', ', NULL, NULL);
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
	 * @param string $delimiter 拼接谓句的分隔符
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