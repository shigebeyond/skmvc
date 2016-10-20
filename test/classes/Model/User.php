<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 用户模型
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 上午9:11:51  
 *
 */
class Model_User extends Orm
{
	/**
	 * 每个字段的校验规则
	 * @var array
	 */
	protected static $_rules = array(
		'name' => 'trim > not_empty && length(1, 10)',
		'age' => 'trim > is_numeric && range(0, 100)',
	);
	
	/**
	 * 关联关系
	 * @var array
	 */
	protected static $_relations = array(
		'contacts' => array( // 有多个联系方式
			'type' => Orm::RELATION_HAS_MANY, // 关联类型: 有多个
			'model' => 'Contact', // 关联模型
			'foreign_key' => 'user_id'	//外键
		)
	);
}