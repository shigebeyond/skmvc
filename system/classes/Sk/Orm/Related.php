<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之关联对象操作
 *        
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34 
 *
 */
class Sk_Orm_Related extends Orm_Persistent
{
	/**
	 * 关联关系 - 有一个
	 *    当前表是主表, 关联表是从表
	 *    
	 * @var array
	 */
	protected static $_has_one = array(
		'model' => 'user',
		'foreign_key' => 'user_id',
	);
	
	/**
	 * 关联关系 - 有多个
	 * 	当前表是主表, 关联表是从表
	 *  
	 * @var array
	*/
	protected static $_has_many = array();
	
	/**
	 * 关联关系 - 从属于
	 *    当前表是从表, 关联表是主表
	 *    
	 * @var array
	 */
	protected static $_belongs_to = array();
	
	public function get_has_one($config)
	{
		return $this->get_has_many($config)->limit(1);
	}
	
	public function get_has_many($config)
	{
		$class = 'Model_'.$config['model'];
		$relation = new Relation($this, $class, $config['foreign_key']);
		return $class::query_builder()->where($config['foreign_key'], '=', $this->pk());
	}
	
	public function get_belongs_to($config)
	{
		$class = 'Model_'.$config['model'];
		$fk = $config['foreign_key'];
		return $class::query_builder()->where($class::$_primary_key, '=', $this->$fk);
	}
	
	
	protected static $_relations = array(
		'user' => array(
			'type' => 'slave',	
			'foreign_key' => 'has'
		)
	);
	
	public static function relation($name = NULL)
	{
		return Arr::get(static::$_relations, $name);
	}
	

	public function get_user()
	{
		return (new Orm_Relation($this, 'Model_User', 'class_id'))->query_slave();
	}
	
	//重写try_get/try_set
	
	//关联查询
}