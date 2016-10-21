<?php defined('SYSPATH') OR die('No direct script access.');

class Test_View extends PHPUnit_Framework_TestCase
{
	public function test_view()
	{
		$view = new View('test0', array('name' => 'shi'));
		$view->set('age', 24);
		
		echo $view->render(); // 显示渲染视图: 调用view::render()
	}
}