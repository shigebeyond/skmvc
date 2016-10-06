<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Arr extends PHPUnit_Framework_TestCase
{
	public function test_path(){
		$arr = array(
			'a' => array(
				'b' => array(
					'c' => 12
				)
			)
		);
		$this->assertEquals('12', Arr::path($arr, 'a.b.c'));
		$this->assertEquals(array('c' => 12), Arr::path($arr, 'a.b'));
		$this->assertNull(Arr::path($arr, 'd'));
	}
	
}