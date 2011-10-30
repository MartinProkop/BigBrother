<?php
class BB_Db_Query_Operator_OperatorTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		
	}
	
	public function testAddRemove() {
		$data = array(BB_Db_Query_Operator_Abstract::OPERATOR_KEY => "AND", 5, 6, 7);
		
		$obj = new BB_Db_Query_Operator_AND($data);
		
		$obj->addOperand("6");
		$this->assertEquals(4, $obj->count());
		
		$obj->deleteOperand(2);
		$this->assertEquals(3, $obj->count());
	}
	
	public function testGet() {
		$data = array(BB_Db_Query_Operator_Abstract::OPERATOR_KEY => "AND", 5, 6, 7);
		
		$obj = new BB_Db_Query_Operator_AND($data);
		
		$this->assertEquals(5, $obj->getOperand(0)->getValue());
	}
	
	public function testUpdate() {
		$data = array(BB_Db_Query_Operator_Abstract::OPERATOR_KEY => "AND", 5, 6, 7);
		
		$obj = new BB_Db_Query_Operator_AND($data);
		$obj->updateOperand(12, 0);
		
		$this->assertEquals(12, $obj->getOperand(0)->getValue());
	}
}
