<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Text extends PHPUnit_Framework_TestCase
{
	public function test_path(){
		$this->assertEquals('Admin_Blog', Text::ucfirst('admin_blog'));
	}
	
}