<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Container extends PHPUnit_Framework_TestCase
{
	public function test_component(){
		$db = Container::component_config('Db', 'default');
		$this->assertNotNull($db);
	}
	
}