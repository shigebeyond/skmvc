<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Request extends PHPUnit_Framework_TestCase
{
	public function test_route(){
		$req = new Request('blog/show/12');
		// 解析路由
		$req->parse_route();
		print_r($req->param());
		$this->assertNotNull($req->param());
		$this->assertEquals('blog', $req->param('controller'));
		$this->assertEquals('blog', $req->controller());
		$this->assertEquals('show', $req->param('action'));
		$this->assertEquals('show', $req->action());
		$this->assertEquals('12', $req->param('id'));
	}
	
}