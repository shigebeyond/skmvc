<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db_Query_Builder extends PHPUnit_Framework_TestCase
{
	public function test_insert()
	{
		$query = Db::instance()->query_builder('user')->data(array('name' => 'kkk', 'age' => 12));
		list($sql, $params) = $query->compile('insert');
		echo "insert sql: $sql, params: ".implode(',', $params);
		echo "insert result: ".$query->insert();
	}

	public function test_update()
	{
		$query = Db::instance()->query_builder('user')->set('age', 22)->where('name', '=', 'kkk');
		list($sql, $params) = $query->compile('update');
		echo "update sql: $sql, params: ".implode(',', $params);
		echo "update result: ".$query->update();
	}

	public function test_delete()
	{
		$query = Db::instance()->query_builder('user')->where('id', '=', 1);
		list($sql, $params) = $query->compile('delete');
		echo "delete sql: $sql, params: ".implode(',', $params);
		echo "delete result: ".$query->delete();
	}

	public function test_select()
	{
		$query = Db::instance()->query_builder('user')->where('id', '=', 2);
		list($sql, $params) = $query->compile('select');
		echo "select sql: $sql, params: ".implode(',', $params);
		$result = $query->find_all();
		echo "select result: ".print_r($result);
	}

	public function test_where()
	{
		$query = Db::instance()->query_builder('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		list($sql, $params) = $query->compile('select');
		echo "where sql: $sql, params: ".implode(',', $params);
		$result = $query->find_all();
		echo "select result: ".print_r($result);
	}

	public function test_join()
	{
		$query = Db::instance()->query_builder('user')->select(['user.*', 'contact.email'])->where('user.id', '=', 2)->join('contact', 'left')->on('user.id', '=', 'contact.user_id');
		list($sql, $params) = $query->compile('select');
		echo "join sql: $sql, params: ".implode(',', $params);
		$result = $query->find_all();
		echo "select result: ".print_r($result);
	}

	public function test_count()
	{
		$query = Db::instance()->query_builder('user')->where('id', '=', 2)->where_open()->where('name', '=', 'li')->where_close();
		list($sql, $params) = $query->compile('select');
		echo "where sql: $sql, params: ".implode(',', $params);
		$result = $query->count();
		echo "count result: ".print_r($result);
	}
}
