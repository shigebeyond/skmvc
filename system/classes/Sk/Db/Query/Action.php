<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- action
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action extends Db_Query_Where 
{
	/**
	 * 动作子句模板: 增删改查
	 * 	如 select :columns from :table / update :table set :column = :value, 
	 * @var string
	 */
	protected $_action_template;
	
	/**
	 * 编译动作子句
	 * @see Sk_Db_Query::compile_action()
	 */
	public function compile_action()
	{
		// 1 编译表名/多个字段名/多个字段值
		// 针对 select :columns from :table / insert into :table :columns values :values
		$action = preg_replace_callback('/:(table|columns|values)/', function($mathes){
			// 调用对应的方法: table() / columns() / values()
			$method = 'compile_'.$mathes[1];
			return $this->$method();
		}, $this->_action_template);
		
		// 编译字段谓句
		// 针对 update :table set :column = :value
		return preg_replace_callback('/:column(.+):value/', function($mathes){
			return $this->compile_column_predicate($mathes[1]);
		}, $action);
	}
	
	/**
	 * 编译表名: 转义
	 * @return string
	 */
	public function compile_table($table = NULL)
	{
		return $this->_db->quote_table($this->_table);
	}
	
	/**
	 * 编译多个字段名: 转义
	 * @return string
	 */
	public function compile_columns()
	{
		if(empty($this->_data))
			return NULL;
		
		return $this->_db->quote_column(array_keys($this->_data));
	}
	
	/**
	 * 编译多个字段值: 转义
	 * @return string
	 */
	public function compile_values()
	{
		if(empty($this->_data))
			return NULL;
		
		return $this->_db->quote(array_values($this->_data));
	}
	
	/**
	 * 编译字段谓句: 转义 + 拼接谓句
	 * 
	 * @param stirng $operator 谓语
	 * @param string $delimiter 拼接谓句的分隔符
	 * @return string
	 */
	public function compile_column_predicate($operator, $delimiter = ', ')
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