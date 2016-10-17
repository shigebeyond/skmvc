<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ORM之关联关系
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-10 上午12:52:34 
 *
 */
class Sk_Orm_Relation extends Orm_Persistent
{
	/**
	 * 关联关系 - 有一个
	 *    主表.主键 = 关联表.外键
	 *    master.id = other.master_id
	 *  
	 * @var array
	 */
	protected static $_has_one = array(
		'model' => 'user',
		'foreign_key' => 'user_id',
	);
	
	/**
	 * 关联关系 - 有多个
	 * 	  主表.主键 = 关联表.外键
	 *    master.id = other.master_id
	 *  
	 * @var array
	*/
	protected static $_has_many = array();
	
	/**
	 * 关联关系 - 从属于
	 *    主表.主键 = 关联表.外键
	 *  master.id = other.master_id
	 * @var array
	 */
	protected static $_belongs_to = array();
	
	public function has_one($config)
	{
		$class = 'Model_'.$config['model'];
		return $class::query_builder()->where($config['foreign_key'], '=', $this->pk())->find();
	}
	
	public function has_many($config)
	{
		$class = 'Model_'.$config['model'];
		return $class::query_builder()->where($config['foreign_key'], '=', $this->pk())->find();
	}
	
	
	public function belongs_to($config)
	{
		$class = 'Model_'.$config['model'];
		$pk = $config['foreign_key'];
		return $class::query_builder()->where($class::$_primary_key, '=', $this->$pk)->find();
	}
	
	//重写try_get/try_set
	
	//关联查询
}