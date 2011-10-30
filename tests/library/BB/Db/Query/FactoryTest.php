<?php
class BB_Db_Query_FactoryTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		
	}
	
	public function testExpresion() {
		$value = array(
			BB_Db_Query_Expresion::EXPR_KEY => "CONCAT(?)",
			"hoj"
		);
		
		$obj = BB_Db_Query_Factory::factory($value);
		$this->assertType("BB_Db_Query_Expresion", $obj);
	}
	
	public function testSequence() {
		$value = array(
			"val",
			"AND",
			"val2"
		);
		
		$obj = BB_Db_Query_Factory::factory($value);
		$this->assertType("BB_Db_Query_Sequence", $obj);
	}
	
	public function testReference() {
		$value = "[tabulka.sloupec]";
		
		$obj = BB_Db_Query_Factory::factory($value);
		$this->assertType("BB_Db_Query_Reference", $obj);
	}
	
	public function testPrimitive() {
		$value = "test";
		
		$obj = BB_Db_Query_Factory::factory($value);
		$this->assertType("BB_Db_Query_Primitive", $obj);
	}
	
	public function testOperator() {
		$value = array(
			BB_Db_Query_Operator_Abstract::OPERATOR_KEY => "AND",
			"ahoj",
			"2"
		);
		
		$obj = BB_Db_Query_Factory::factory($value);
		$this->assertType("BB_Db_Query_Operator_AND", $obj);
	}
} 