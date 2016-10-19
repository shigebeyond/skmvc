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
	protected static $_relations = array(
		'contacts' => array( // 有多个联系方式
			'type' => Orm::RELATION_BELONGS_TO,
			'model' => 'User',
			'foreign_key' => 'user_id'
		)
	);
}