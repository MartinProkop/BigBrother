<?php
class BB_Db_QueryTest extends PHPUnit_Framework_TestCase {
	protected $_query;
	
	protected $_queryArray;
	
	protected $_queryString;
	
	public function setUp() {
		$this->_queryString = "(`column` AND 4 AND 'čon\'sa') AND (CONCAT('jmeno','petr')) OR (NOT (`id` IN (6,7,9)))";
		
		$this->_queryArray = array(
			array(
				"operator" => "and", 
				"[column]", 
				"4", 
				"čon'sa"
			), 
			"AND", 
			array(
				"expresion" => "CONCAT(?)",
				"jmeno",
				"petr"
			),
			"OR",
			array(
				"operator" => "NOT",
				array (
					"operator" => "IN",
					"[id]",
					6,
					7,
					9
				)
			)
		);
		
		$this->_query = new BB_Db_Query($this->_queryArray);
	}
	
	public function testArray() {
		$this->assertSame($this->_queryArray, $this->_query->toArray());
	}
	
	public function testOffsetGet() {
		$this->assertEquals("AND", $this->_query[1]);
		
		$this->assertTrue(in_array("BB_Db_Query_Operator", class_parents($this->_query[0])));
	}
	
	public function testArrayAccess() {
		$this->assertEquals("AND", $this->_query[1]);
		$this->assertTrue(isset($this->_query[0]));
		$this->assertTrue(isset($this->_query[1]));
		$this->assertFalse(isset($this->_query[10]));
		
		$this->assertEquals(10, $this->_query[1] = 10);
		unset($this->_query[4]);
		
		$this->assertNull($this->_query->offsetUnset(7));
	}
	
	public function testStringTo() {
		$this->assertSame($this->_queryString, $this->_query->__toString());
	}
}
