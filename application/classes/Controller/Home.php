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
// 		$result = Db::instance()->preview('select * from user where id = ?', array(3));
// 		$result = Db::instance()->preview('select * from user where id = :id', array(':id' => 3));

		//$result = Db::instance()->insert('user', array('name' => 'kkk', 'age' => 12))->execute();
		
		$query = Db::instance()->delete('user')->where('id', '=', '1');
		$result = $query->execute();
		
		$this->res->body("result is $result");
	}
}