<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db extends PHPUnit_Framework_TestCase
{
	public function test_connenct(){
	  	$db = Db::instance();
		$this->assertNotNull($db);
	} 
	
	public function test_create(){
		$sql = "CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `age` smallint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表'";
		$db = Db::instance();
		$result = $db->execute($sql);
		echo "create result: $result"; // 0
	}  
	
	public function test_drop(){
		$sql = "DROP TABLE IF EXISTS `user`";
		$db = Db::instance();
		$result = $db->execute($sql);
		echo "drop result: $result"; // 0
	} 
	
	public function test_insert(){
		$sql = "INSERT INTO user(id, name, age) VALUES(1, 'shi', 27)";
		$db = Db::instance();
		$result = $db->execute($sql);
		echo "insert result: $result"; // 1
	} 
	
	public function test_update(){
		$sql = "UPDATE user SET name='li', age=29 WHERE id=1";
		$db = Db::instance();
		$result = $db->execute($sql);
		echo "update result: $result"; // 1
	} 
	
	public function test_delete(){
		$sql = "DELETE FROM user WHERE id=1";
		$db = Db::instance();
		$result = $db->execute($sql);
		echo "delete result: $result"; // 1
	} 

	public function test_select(){
		$sql = "SELECT * FROM user";
		$db = Db::instance();
		$result = $db->query($sql);
		echo "select result: $result"; // 1
	} 
	
	public function test_preview(){
		echo Db::instance()->preview('select * from user where id = ?', array(3));
		echo "\n";
		echo Db::instance()->preview('select * from user where id = :id', array(':id' => 3));
	}
	
	public function test_columns(){
		$columns = Db::instance()->list_columns('user');
		print_r($columns);
		$this->assertEquals(array('id', 'name', 'age'), $columns);
	} 
	
	public function test_query_func(){
		$users = Db::instance()->query('select * from user', array($this, 'handle_user'));
		print_r($users);
	}
	
	public function handle_user($name, $age) {
		return "{$name}: {$age}";
	}
}