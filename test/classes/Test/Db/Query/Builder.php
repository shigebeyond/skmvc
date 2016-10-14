<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db_Query_Builder extends PHPUnit_Framework_TestCase
{
	public function test_connenct(){
	  $result = Db::insert('user', array('name' => 'kkk', 'age' => 12))->execute();
		$this->assertNotNull($db);
	}
	
}