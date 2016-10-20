<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Orm_Relation extends PHPUnit_Framework_TestCase
{
	/* public function test_set()
	{
		$user = new Model_User();
		$user->name = 'xie';
		$user->age = 24;
		$user->create();
		print_r($user->as_array());
		
		$contact = new Model_Contact();
		$contact->user = $user; // 设置
		$contact->email = 'shi@163.com';
		$contact->address = 'nanning';
		$contact->create();
		print_r($contact->as_array());
	} */
	
	/* public function test_get()
	{
		$user = new Model_User(2);
		print_r($user->as_array());
		if($user->contacts)
			foreach ($user->contacts as $contact)
				print_r($contact->as_array());
	} */
	
	/* public function test_query_with(){
		$user = Model_User::query_builder()->with('contacts')->where('user.id', '=', 2)->find();
		if($user)
		{
			print_r($user->as_array());
			if($user->contacts)
				print_r($user->contacts->as_array());
		}
	}
	 */

}