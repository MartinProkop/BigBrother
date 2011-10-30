<?php
class BB_Db_Query_PrimitiveTest extends PHPUnit_Framework_TestCase {
	const VALUE = "test";
	
	protected $_obj;
	
	public function setUp() {
		$this->_obj = new BB_Db_Query_Primitive(self::VALUE);
	}
	
	public function testString() {
		$result = $this->_obj->__toString();
		
		$this->assertEquals("'" . self::VALUE . "'", $result);
	}
	
	public function testArray() {
		$result = $this->_obj->toArray();
		
		$this->assertEquals(self::VALUE, $result);
	}
	
	public function testSetGet() {
		$obj = new BB_Db_Query_Primitive("test");
		
		$obj->setValue("a");
		$this->assertEquals("a", $obj->getValue());
	}
}
