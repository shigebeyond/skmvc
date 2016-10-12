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
	 * 编译动作子句
	 * @see Sk_Db_Query::compile_action()
	 */
	public function compile_action()
	{
		return preg_replace_callback('/:(table|keys|values)/', function($mathes){
			// 调用对应的方法: table() / keys() / values()
			$method = $mathes[1];
			return $this->$method();
		}, $this->_action);
	}
	
	/**
	 * 获得多个字段名
	 * @return string
	 */
	public function keys()
	{
		return NULL;
	}
	
	/**
	 * 获得字段值
	 * @return string
	 */
	public function values()
	{
		return NULL;
	}
}