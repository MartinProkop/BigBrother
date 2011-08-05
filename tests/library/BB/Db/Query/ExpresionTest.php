<?php
class BB_Db_Query_ExpresionTest extends PHPUnit_Framework_TestCase {
	protected $_expresion;
	
	protected $_data = array(
		BB_Db_Query_Factory::EXPRESION => "CONCAT(?)",
		"polozka",
		"[sloupec]",
		4
	);
	
	protected $_string = "(CONCAT('polozka',`sloupec`,4))";
	
	public function setUp() {
		$exp = $this->_data[BB_Db_Query_Factory::EXPRESION];
		
		$params = array();
		
		foreach ($this->_data as $key => $val) {
			if (is_numeric($key)) $params[] = $val;
		}
		
		$this->_expresion = new BB_Db_Query_Expresion($exp, $params);
	}
	
	public function testToArray() {
		$this->assertEquals($this->_data, $this->_expresion->toArray());
	}
	
	public function testToString() {
		$this->assertEquals($this->_string, $this->_expresion->__toString());
	}
}
