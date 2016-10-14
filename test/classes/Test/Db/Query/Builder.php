<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db_Query_Builder extends PHPUnit_Framework_TestCase
{
	public function test_insert()
	{
		$result = Db::instance()->insert('user', array('name' => 'kkk', 'age' => 12))->execute();
		echo "insert result: $result";
	}
	
}