<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Orm extends PHPUnit_Framework_TestCase
{
	/* public function test_insert(){
		$user = new Model_User();
		$user->name = 'shi';
		$user->age = 24;
		$user->create();
		print_r($user->as_array());
	} */
	
	/* public function test_update(){
		$user = new Model_User(7);
		$user->name = 'wang';
		$user->age = 124;
		$user->update();
		print_r($user->as_array());
	} */
	
	public function test_find(){
		$user = Model_User::query_builder()->where('id', '=', 5)->find();
		if($user)
		{
			print_r($user->as_array());
			$this->assertEquals(7, $user->id);
		}
		else 
		{
			echo '没有找到记录';
		}
	} 
	
	public function test_find_all(){
		$users = Model_User::query_builder()->find_all();
		print_r($users);
	}
	
}