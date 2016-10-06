<?php defined('SYSPATH') OR die('No direct script access.');

class Singleton_Configurable_Demo extends Singleton_Configurable{
	public $config = null;
	public function __construct($config){
		echo "call construct";
// 		print_r($config);
		$this->config = $config;
	}
}

class Test_Singleton_Configurable extends PHPUnit_Framework_TestCase
{
	public function test_instance(){
		$inst = Singleton_Configurable_Demo::instance('router');
		$this->assertNotNull($inst);
		$this->assertInstanceOf('Singleton_Configurable_Demo', $inst);
		$this->assertArrayHasKey('/.+/', $inst->config);
	}
	
}