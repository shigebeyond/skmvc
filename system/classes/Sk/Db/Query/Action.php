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
abstract class Sk_Db_Query_Action extends Db_Query 
{
	/**
	 * sql动作: 增删改查
	 * 	如 select :keys from :table / update :table set :key = :value, 
	 * @var string
	 */
	protected $_action;
	
	/**
	 * 要插入/更新的数据: <column => value>
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * 编译动作子句
	 * @see Sk_Db_Query::compile_action()
	 */
	public function compile_action()
	{
		// 编译模式 select :keys from :table / insert into :table :keys values :values
		$action = preg_replace_callback('/:(table|keys|values)/', function($mathes){
			// 调用对应的方法: table() / keys() / values()
			$method = $mathes[1];
			return $this->$method();
		}, $this->_action);
		
		// 编译模式 update :table set :key = :value
		preg_replace_callback('/:key(.+):value/', function($mathes){
			
		}, $this->_action);
			
		return $action;
	}
	
	/**
	 * 获得多个字段名
	 * @return string
	 */
	public function keys()
	{
		return $this->_db->quote_column(array_keys($this->_data));
	}
	
	/**
	 * 获得字段值
	 * @return string
	 */
	public function values()
	{
		return $this->_db->quote(array_values($this->_data));
	}
	
	/**
	 * 编译set子句
	 * @return string
	 */
	public function compile_set()
	{
		$set = '';
		foreach ($this->_values as $key => $value)
		{
			$set = "$key = $value, ";
		}
		return rtrim($set, ', ');
	}
}