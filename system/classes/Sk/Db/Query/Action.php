<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- 动作子句: 由动态select/insert/update/delete来构建的子句
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action extends Db_Query_Decoration
{
	/**
	 * 动作子句模板: select/insert/update/delete
	 * 	如 select :columns from :table / update :table set :column = :value, 
	 * @var string
	 */
	protected static $_action_template;
	
	/**
	 * 编译动作子句
	 * @see Sk_Db_Query::compile_action()
	 */
	public function compile_action()
	{
		// 实际上是填充子句，如将行参表名替换为真实表名
		
		// 1 填充表名/多个字段名/多个字段值
		// 针对 select :columns from :table / insert into :table :columns values :values
		$action = preg_replace_callback('/:(table|columns|values)/', function($mathes){
			// 调用对应的方法: _fill_table() / _fill_columns() / _fill_values()
			$method = '_fill_'.$mathes[1];
			return $this->$method();
		}, static::$_action_template);
		
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
		if(empty($this->_data))
			return NULL;
		
		return $this->_db->quote_columns(array_keys($this->_data));
	}
	
	/**
	 * 编译多个字段值: 转义
	 * @return string
	 */
	protected function _fill_values()
	{
		if(empty($this->_data))
			return NULL;
		
		return $this->_db->quote_values($this->_data);
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