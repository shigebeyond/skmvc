<?php defined('SYSPATH') OR die('No direct script access.');

class Singleton_Demo extends Singleton{
	public function __construct(){
		echo "call construct";
	}
}

class Test_Singleton extends PHPUnit_Framework_TestCase
{
	public function test_instance(){
		$inst = Singleton_Demo::instance();
		$this->assertNotNull($inst);
		$this->assertInstanceOf('Singleton_Demo', $inst);
	}
	
}