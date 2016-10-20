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
		
// 		$query = Db::instance()->delete('user')->where('id', '=', '1');
// 		$result = $query->execute();
		
		/* $query = Db::instance()->select('user')->where('user.id', '=', 2);
// 		$query = Db::instance()->select('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		$msg = "sql: ".$query->compile()[0];
		$result = $query->execute();
		$msg .= "select result: ".print_r($result); */
		
		/* $columns = Db::instance()->list_columns('user');
		print_r($columns); */
		
		/* $user = new Model_User(7);
		$user->name = 'shi';
		$user->age = 24;
		$result = $user->update();
		print_r($user->as_array()); */
		
// 		$users = Db::instance()->query('select * from user', array($this, 'handle_user'));

		/* $users = Model_User::query_builder()->find_all();
		print_r($users); */
		
		$user = Model_User::query_builder()->with('contacts')->find();
		$result = print_r($user->as_array());
		$this->res->body("result is $result");
	}
	
	public function handle_user($name, $age) {
		return "{$name}: {$age}";
	}
	
}