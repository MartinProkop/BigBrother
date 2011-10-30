<?php
class BB_Db_Query_ReferenceTest extends PHPUnit_Framework_TestCase {
	const REFERENCE = "[tabulka.sloupec]";
	
	protected $_obj;
	
	public function setUp() {
		$this->_obj = new BB_Db_Query_Reference(self::REFERENCE);
		BB_Db_Query_Reference::$objectType = "default";
	}
	
	public function testString() {
		$result = $this->_obj->__toString();
		
		$this->assertEquals("`default_tabulka`.`sloupec`", $result);
	}
	
	public function testArray() {
		$result = $this->_obj->toArray();
		
		$this->assertEquals(self::REFERENCE, $result);
	}
	
	public function testSet() {
		$this->_obj->setColumn("sloupec2");
		$this->_obj->setTableName("table2");
		
		$this->assertEquals("sloupec2", $this->_obj->getColumn());
		$this->assertEquals("table2", $this->_obj->getTable());
	}
	
	public function testStatics() {
		BB_Db_Query_Reference::clearTables();
		
		$ref = new BB_Db_Query_Reference("[table.column]");
		$this->assertEquals(array("table"), BB_Db_Query_Reference::getTables());
		
		$ref->setTableName("table2");
		$this->assertEquals(array("table2"), BB_Db_Query_Reference::getTables());
	}
}
