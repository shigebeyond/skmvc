<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Config extends PHPUnit_Framework_TestCase
{
	public function test_load(){
		$config = Config::load('test');
		$this->assertArrayHasKey('name', $config);
		$this->assertInstanceOf('Closure', $config['fun']); // 函数
		$this->assertEquals(1, $config->get('a.b.c')); // 多级路径
	}
}