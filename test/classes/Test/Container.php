<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db extends PHPUnit_Framework_TestCase
{
	public function test_query(){
		$db = Container::component_config('Db', 'default');
		$this->assertNotNull($db);
	}
	
}