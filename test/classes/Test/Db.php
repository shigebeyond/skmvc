<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Db extends PHPUnit_Framework_TestCase
{
	public function test_query(){
	  $result = Db::instance();
		$this->assertEquals('Admin_Blog', Text::ucfirst('admin_blog'));
	}
	
}