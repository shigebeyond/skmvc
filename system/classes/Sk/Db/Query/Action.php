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
	 * sql动作: 增删改查
	 * 	如 select :columns from :table / update :table set :column = :value, 
	 * @var string
	 */
	protected $_action;
	
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
		}, $this->_action);
		
		// 编译谓语形式的字段
		// 针对 update :table set :column = :value
		preg_replace_callback('/:column(.+):value/', function($mathes){
			return $this->compile_key($mathes[1]);
		}, $this->_action);
			
		return $action;
	}
	
	/**
	 * 编译表名
	 * @return string
	 */
	public function compile_table($table = NULL)
	{
		return $this->_db->quote_table($this->_table);
	}
	
	/**
	 * 编译多个字段名
	 * @return string
	 */
	public function compile_columns()
	{
		return $this->_db->quote_column(array_keys($this->_data));
	}
	
	/**
	 * 编译多个字段值
	 * @return string
	 */
	public function compile_values()
	{
		return $this->_db->quote(array_values($this->_data));
	}
	
	/**
	 * 编译谓语形式的字段
	 * @return string
	 */
	public function compile_predicate($operator, $delimiter = ', ')
	{
		$sql = '';
		foreach ($this->_values as $column => $value)
			$sql = "$column $operator $value$delimiter";
		
		return rtrim($sql, $delimiter);
	}
}