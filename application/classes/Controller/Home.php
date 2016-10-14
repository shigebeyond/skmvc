<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * 主页
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-8 下午8:02:47 
 *
 */
class Controller_Home extends Controller
{
	public function action_index()
	{
		$sql = "SELECT * FROM user";
		$db = Db::instance();
		$result = $db->query($sql);
		
		$this->res->body("result is $result");
	}
}