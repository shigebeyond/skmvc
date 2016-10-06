<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Router extends PHPUnit_Framework_TestCase
{
	public function test_parse(){
		$router = Router::instance();
		$params = $router->parse('blog/show/12');
		$this->assertNotNull($params);
// 		print_r($params);
		$this->assertEquals('blog', $params['controller']);
		$this->assertEquals('show', $params['action']);
		$this->assertEquals('12', $params['id']);
	}
	
}