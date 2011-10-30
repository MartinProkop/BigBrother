<?php
class BB_Db_Query_Operator_SetTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		
	}
	
	public function testCommon() {
		$key = BB_Db_Query_Operator_Abstract::OPERATOR_KEY;
		
		$data = array($key => "IN", 1, 2, 3, 4);
		$obj = new BB_Db_Query_Operator_IN($data);
		$this->assertEquals($data, $obj->toArray());
		$this->assertEquals("'1' IN ('2','3','4')", $obj->__toString());
		
		$data = array($key => "NIN", 1, 2, 3, 4);
		$obj = new BB_Db_Query_Operator_NIN($data);
		$this->assertEquals($data, $obj->toArray());
		$this->assertEquals("'1' NOT IN ('2','3','4')", $obj->__toString());
	}
}
