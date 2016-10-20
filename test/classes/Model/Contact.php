<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 联系方式模型
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 上午9:12:10  
 *
 */
class Model_Contact extends Orm
{
	/**
	 * 每个字段的校验规则
	 * @var array
	 */
	protected static $_rules = array(
			'email' => 'trim > not_empty && email',
			'address' => 'trim > not_empty && length(1, 10)',
	);
	
	/**
	 * 关联关系
	 * @var array
	 */
	protected static $_relations = array(
		'user' => array( // 从属于某个用户
			'type' => Orm::RELATION_BELONGS_TO, // 关联类型: 从属于
			'model' => 'User', // 关联模型
			'foreign_key' => 'user_id' // 外键
		)
	);
}