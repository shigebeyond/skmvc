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
        $this->res->body("hello world");
    }

	public function action_db()
	{
// 		$result = Db::instance()->preview('select * from user where id = ?', array(3));
// 		$result = Db::instance()->preview('select * from user where id = :id', array(':id' => 3));

// 		$columns = Db::instance()->list_columns('user');
// 		$result = print_r($columns);
		
		$users = Db::instance()->query('select * from user');
		$result = print_r($users);
		$this->res->body($result);
	}

	
	public function action_query_builder()
	{
// 		$query = Db::instance()->query_builder('user', array('name' => 'kkk', 'age' => 12));
// 		list($sql, $params) = $query->compile('insert');
// 		$result = "insert sql: $sql, params: ".implode(',', $params);
// 		$result .= "insert result: ".$query->insert();
		
// 		$result = Db::instance()->query_builder('user', array('name' => 'kkk', 'age' => 12))->insert();
// 		$result = Db::instance()->query_builder('user')->where('id', '=', '1')->delete();
	
// 		$query = Db::instance()->query_builder('user')->where('id', '=', 2);
		$query = Db::instance()->query_builder('user')->where('id', 2);
// 		$query = Db::instance()->query_builder('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		list($sql, $params) = $query->compile('select');
		$result = "select sql: $sql, params: ".implode(',', $params);
		$result .= " select result: ".print_r($query->find_all());
		
// 		$query = Db::instance()->select('user')->where('name', '=', 'li');
// 		$result = print_r($query->count());
		
		$this->res->body($result);
	}
	
	public function action_orm()
	{
		$user = new Model_User(4);
		if ($user->exists()) {
			print_r($user->as_array());
			$result = $user->delete();
			echo '删除成功';
			$this->assertEquals(1, $result);
		}else {
			echo '对象不存在';
		}
	
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
		$str = ' model ';
		$result = $exp->execute($str, NULL);
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
        // 创建视图
 		$view = new View('test', array('name' => 'shi')); // 自定义视图, 视图文件 application/views/test.php
        // $view = $this->view(array('name' => 'shi')); // 默认视图, 视图文件 application/views/$controller/$action.php

        // 设置变量
        $view->set('age', 24);

        // 渲染
 		// $this->res->body($view->render()); // 显示渲染视图: 调用view::render()
        $this->res->body($view); // 隐式渲染视图
    }
	
	public function action_mongo_query_builder()
	{
		$query = Mongoo::instance()->query_builder('user')->set('age', 33)->set('name', 'uuu')->where('_id', new MongoId('581b0438980edd8f7a8b4567'))->limit(0, 0);
		$result = "update result: ".$query->count();
		$this->res->body($result);
	}
}