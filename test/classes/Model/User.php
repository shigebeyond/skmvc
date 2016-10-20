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