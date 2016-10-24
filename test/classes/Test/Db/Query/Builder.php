<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db_Query_Builder extends PHPUnit_Framework_TestCase
{
	/* public function test_insert()
	{
		$query = Db::instance()->insert('user', array('name' => 'kkk', 'age' => 12));
		list($sql, $params) = $query->compile();
		echo "insert sql: $sql, params: ".implode(',', $params);
		echo "insert result: ".$query->execute();
	} */

	/* public function test_update()
	{
		$query = Db::instance()->update('user')->set('age', 22)->where('name', '=', 'kkk');
		list($sql, $params) = $query->compile();
		echo "update sql: $sql, params: ".implode(',', $params);
		echo "update result: ".$query->execute();
	} */

	/* public function test_delete()
	{
		$query = Db::instance()->delete('user')->where('id', '=', 1);
		list($sql, $params) = $query->compile();
		echo "delete sql: $sql, params: ".implode(',', $params);
		echo "delete result: ".$query->execute();
	} */

	/* public function test_selete()
	{
		$query = Db::instance()->select('user')->where('id', '=', 2);
		list($sql, $params) = $query->compile();
		echo "select sql: $sql, params: ".implode(',', $params);
		$result = $query->execute();
		echo "select result: ".print_r($result);
	} */

	public function test_where()
	{
		$query = Db::instance()->select('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		list($sql, $params) = $query->compile();
		echo "where sql: $sql, params: ".implode(',', $params);
		$result = $query->execute();
		echo "select result: ".print_r($result);
	}

	public function test_join()
	{
		$query = Db::instance()->select('user')->select(['user.*', 'contact.email'])->where('user.id', '=', 2)->join('contact', 'left')->on('user.id', '=', 'contact.user_id');
		list($sql, $params) = $query->compile();
		echo "join sql: $sql, params: ".implode(',', $params);
		$result = $query->execute();
		echo "select result: ".print_r($result);
	}

}
