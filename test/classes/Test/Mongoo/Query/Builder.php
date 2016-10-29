<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Mongoo_Query_Builder extends PHPUnit_Framework_TestCase
{
// 	public function test_insert()
// 	{
// 		$query = Mongoo::instance()->query_builder('user', array('name' => 'kkk', 'age' => 12));
// 		echo "insert result: ".$query->insert();
// 	}

// 	public function test_update()
// 	{
// 		$query = Mongoo::instance()->query_builder('user')->set('age', 22)->set('name', 'wang')->where('name', 'kkk');
// 		echo "update result: ".$query->update();
// 	}

// 	public function test_delete()
// 	{
// 		$query = Mongoo::instance()->query_builder('user')->where('_id', new MongoId('5814870807be1e09358b4567'));
// 		echo "delete result: ".$query->delete();
// 	}

// 	public function test_select()
// 	{
// 		$query = Mongoo::instance()->query_builder('user')->where('_id', new MongoId('5813fe819908ccf5507b45d0'));
// // 		echo "select row: ".print_r($query->find_all()->getNext());
// 		echo "select result: ".print_r(iterator_to_array($query->find_all()));
// 	}

	public function test_count()
	{
		$query = Mongoo::instance()->query_builder('user')->where('_id', new MongoId('5813fe819908ccf5507b45d0'));
		echo "count result: ".$query->count();
	}
}
