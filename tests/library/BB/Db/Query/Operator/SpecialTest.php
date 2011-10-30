<?php
class BB_Db_Query_Operator_SpecialTest extends PHPUnit_Framework_TestCase {
	public function setUo() {
		
	}
	
	public function testArray() {
		$data = array(BB_Db_Query_Operator_Abstract::OPERATOR_KEY => "ARR", 5, 7, 8);
		$obj = new BB_Db_Query_Operator_ARR($data);
		
		$this->assertEquals($data, $obj->toArray());
		$this->assertEquals("'5','7','8'", $obj->__toString());
	}
	
	public function testBracket() {
		$data = array(BB_Db_Query_Operator_Abstract::OPERATOR_KEY => "BRC", 5);
		$obj = new BB_Db_Query_Operator_BRC($data);
		
		$this->assertEquals($data, $obj->toArray());
		$this->assertEquals("('5')", $obj->__toString());
	}
}
