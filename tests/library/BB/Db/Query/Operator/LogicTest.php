<?php
class BB_Db_Query_Operator_LogicTest extends PHPUnit_Framework_TestCase {
	protected $_data = array(
		"1",
		"2",
		"3"
	);
	
	public function setUp() {
		
	}
	
	public function testCommon() {
		$key = BB_Db_Query_Operator_Abstract::OPERATOR_KEY;
		
		$data = $this->_data;
		
		$data[$key] = "AND";
		$and = new BB_Db_Query_Operator_AND($data);
		$this->assertEquals("('1' AND '2' AND '3')", $and->__toString());
		$this->assertEquals($data, $and->toArray());
		
		$data[$key] = "OR";
		$or = new BB_Db_Query_Operator_OR($data);
		$this->assertEquals("('1' OR '2' OR '3')", $or->__toString());
		$this->assertEquals($data, $or->toArray());
		
		$data = array($key => "NOT", "C");
		$not = new BB_Db_Query_Operator_NOT($data);
		$this->assertEquals("(NOT 'C')", $not->__toString());
		$this->assertEquals($data, $not->toArray());
	}
}
