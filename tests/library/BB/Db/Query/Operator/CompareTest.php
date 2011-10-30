<?php
class BB_Db_Query_Operator_CompareTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		
	}
	
	public function testCommon() {
		$key = BB_Db_Query_Operator_Abstract::OPERATOR_KEY;
		
		$data = array($key => "EQ", 1, 2);
		$obj = new BB_Db_Query_Operator_EQ($data);
		$this->assertEquals("('1' = '2')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
		
		$data = array($key => "NEQ", 1, 2);
		$obj = new BB_Db_Query_Operator_NEQ($data);
		$this->assertEquals("('1' != '2')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
		
		$data = array($key => "GT", 1, 2);
		$obj = new BB_Db_Query_Operator_GT($data);
		$this->assertEquals("('1' > '2')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
		
		$data = array($key => "GTQ", 1, 2);
		$obj = new BB_Db_Query_Operator_GTQ($data);
		$this->assertEquals("('1' >= '2')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
		
		$data = array($key => "LT", 1, 2);
		$obj = new BB_Db_Query_Operator_LT($data);
		$this->assertEquals("('1' < '2')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
		
		$data = array($key => "LTQ", 1, 2);
		$obj = new BB_Db_Query_Operator_LTQ($data);
		$this->assertEquals("('1' <= '2')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
		
		$data = array($key => "BTW", 1, 2, 3);
		$obj = new BB_Db_Query_Operator_BTW($data);
		$this->assertEquals("('1' BETWEEN '2' AND '3')", $obj->__toString());
		$this->assertEquals($data, $obj->toArray());
	}
}
