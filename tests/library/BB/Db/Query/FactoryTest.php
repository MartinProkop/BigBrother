<?php
class BB_Db_Query_FactoryTest extends PHPUnit_Framework_TestCase {
	protected $_columnOriginal = "[sloupec]";
	
	protected $_column = "`sloupec`";
	
	protected $_escapedOriginal = "\[vzavorkach]";
	
	protected $_escaped = "'[vzavorkach]'";
	
	protected $_scalarOriginal = "text";
	
	protected $_scalar = "'text'";
	
	protected $_operatorData = array(
		BB_Db_Query_Factory::OPERATOR => "as",
		"[column]",
		"sloupec"
	);
	
	public function setUp() {
		
	}
	
	/**
	 * @expectedException BB_Db_Query_Exception
	 */
	public function testExceptions() {
		BB_Db_Query_Factory::factory(array());
		
		BB_Db_Query_Factory::factory(array(BB_Db_Query_Factory::EXPRESION => "s", BB_Db_Query_Factory::OPERATOR => "b"));
	}
	
	public function testScalars() {
		// test sloupce
		$this->assertEquals($this->_column, BB_Db_Query_Factory::factory($this->_columnOriginal));
		
		// test nesloupce
		$this->assertEquals($this->_escaped, BB_Db_Query_Factory::factory($this->_escapedOriginal));
		
		//skalarni
		$this->assertEquals($this->_scalar, BB_Db_Query_Factory::factory($this->_scalarOriginal));
	}
	
	public function testUnknown() {
		// test obecneho operatoru
		$this->assertType("BB_Db_Query_Operator", BB_Db_Query_Factory::factory($this->_operatorData));
	}
} 