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
	
	/* public function test_find(){
		$user = Model_User::query_builder()->where('id', '=', 7)->find();
		print_r($user->as_array());
// 		$this->assertNotNull($user->id);
	} */
	
	public function test_find_all(){
		$user = Model_User::query_builder()->find_all();
		print_r($user);
// 		$this->assertNotNull($user->id);
	}
	
}