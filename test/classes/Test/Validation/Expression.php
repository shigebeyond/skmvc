<?php defined('SYSPATH') OR die('No direct script access.');

class Test_Validation_Expression extends PHPUnit_Framework_TestCase
{
	// &&
	public function test_short_and(){
		$exp = new Validation_Expression('not_empty && length(2) && not_empty');
		$result = $exp->execute('1', NULL, $last_subexp);
		echo "last_subexp: ".print_r($last_subexp);
		$this->assertEquals(FALSE, $result);
	}
	
	// &
	public function test_and(){
		$exp = new Validation_Expression('length(2) & not_empty');
		$result = $exp->execute('1', NULL);
		$this->assertEquals(FALSE, $result);
	}
	
	// |
	public function test_short_or(){
		$exp = new Validation_Expression('not_empty | length(2)');
		$result = $exp->execute('1', NULL, $last_subexp);
		echo "last_subexp: ".print_r($last_subexp);
		$this->assertEquals(TRUE, $result);
	}
	
	// >
	public function test_reduce(){
		$exp = new Validation_Expression('trim > strtoupper > substr(2)');
		$result = $exp->execute(' model ', NULL);
		$this->assertEquals('DEL', $result);
	}
	
	// .
	public function test_join(){
		$exp = new Validation_Expression('trim . strtoupper');
		$result = $exp->execute(' model', NULL);
// 		echo $result;
		$this->assertEquals('model MODEL', $result);
	}
	
}