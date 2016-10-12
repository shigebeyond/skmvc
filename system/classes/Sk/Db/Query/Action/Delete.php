<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * sql构建器 -- delete
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-12
 *
 */
class Sk_Db_Query_Action_Delete extends Db_Query_Action
{
	/**
	 * 动作子句模板: delete
	 * @var string
	 */
	protected $_action_template = 'DELETE FROM :table';
}