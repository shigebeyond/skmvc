<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db_Query_Builder extends PHPUnit_Framework_TestCase
{
	/* public function test_insert()
	{
		$query = Db::instance()->insert('user', array('name' => 'kkk', 'age' => 12));
		echo "insert sql: ".$query->compile()[0];
		echo "insert result: ".$query->execute();
	} */

	/* public function test_update()
	{
		$query = Db::instance()->update('user')->set('age', 22)->where('name', '=', 'kkk');
		echo "update sql: ".$query->compile()[0];
		echo "update result: ".$query->execute();
	} */

	/* public function test_delete()
	{
		$query = Db::instance()->delete('user')->where('id', '=', 1);
		echo "delete sql: ".$query->compile()[0];
		echo "delete result: ".$query->execute();
	} */
	
	/* public function test_selete()
	{
		$query = Db::instance()->select('user')->where('id', '=', 2);
		echo "select sql: ".$query->compile()[0];
		$result = $query->execute();
		echo "select result: ".print_r($result);
	} */
	
	public function test_where()
	{
		$query = Db::instance()->select('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		echo "select sql: ".$query->compile()[0];
		$result = $query->execute();
		echo "select result: ".print_r($result);
	}
	
	public function test_join()
	{
		$query = Db::instance()->select('user')->columns('user.*', 'contact.email')->where('user.id', '=', 2)->join('contact', 'left')->on('user.id', '=', 'contact.user_id');
		echo "select sql: ".$query->compile()[0];
		$result = $query->execute();
		echo "select result: ".print_r($result);
	}
	
}