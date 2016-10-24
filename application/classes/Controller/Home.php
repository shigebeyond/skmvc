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
	public function action_db()
	{
// 		$result = Db::instance()->preview('select * from user where id = ?', array(3));
// 		$result = Db::instance()->preview('select * from user where id = :id', array(':id' => 3));

// 		$columns = Db::instance()->list_columns('user');
// 		$result = print_r($columns);
		
		$users = Db::instance()->query('select * from user', array($this, 'handle_user'));
		$result = print_r($users);
		
		$this->res->body($result);
	}
	
	public function handle_user($name, $age) {
		return "{$name}: {$age}";
	}
	
	public function action_query_builder()
	{
// 		$result = Db::instance()->insert('user', array('name' => 'kkk', 'age' => 12))->execute();
// 		$result = Db::instance()->delete('user')->where('id', '=', '1')->execute();
	
		$query = Db::instance()->select('user')->where('user.id', '=', 2);
// 		$query = Db::instance()->select('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		list($sql, $params) = $query->compile();
		$result = "select sql: $sql, params: ".implode(',', $params);
		$result .= " select result: ".print_r($query->execute());
	
		$this->res->body($result);
	}
	
	public function action_orm()
	{
		$user = new Model_User(7);
		$user->name = 'shi';
		$user->age = 24;
		$result = $user->update();
		print_r($user->as_array());
	
		$this->res->body($result);
	}
	
	public function action_orm_query_builder()
	{
		$users = Model_User::query_builder()->find_all();
		$result = print_r($users);
		$this->res->body($result);
	}
	
	public function action_orm_relation()
	{
		$user = Model_User::query_builder()->with('contacts')->find();
		$result = print_r($user->as_array());
		$this->res->body($result);
	}
	
	public function action_validation()
	{
// 		$exp = new Validation_Expression('not_empty | length(2)');
// 		$result = $exp->execute('1', NULL, $last_subexp);
// 		$result = "last_subexp: ".print_r($last_subexp);

// 		$exp = new Validation_Expression('trim > strtoupper > substr(2)');
// 		$result = $exp->execute(' model ', NULL);

		$exp = new Validation_Expression('trim . strtoupper . substr(2)');
		$result = $exp->execute(' model ', NULL);
		$this->res->body($result);
	}
	
	public function action_orm_rule()
	{
		$user = new Model_User(7);
		$user->name = '          shi ';
		$user->age = '26oo';
		$result = $user->update();
		print_r($user->as_array());
	
		$this->res->body($result);
	}
	
	public function action_view()
	{
		// 自定义视图
// 		$view = new View('test0', array('name' => 'shi'));
		// 默认视图
		$view = $this->view(array('name' => 'shi'));
		
		$view->set('age', 24);	
		
// 		$this->res->body($view->render()); // 显示渲染视图: 调用view::render()
		$this->res->body($view); // 隐式渲染视图
	}
}