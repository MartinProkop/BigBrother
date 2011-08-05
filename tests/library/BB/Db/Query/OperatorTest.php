<?php
class BB_Db_Query_OperatorTest extends PHPUnit_Framework_TestCase {
	protected $_data = array(
		BB_Db_Query_Factory::OPERATOR => "as",
		"[sloupec]",
		"column"
	);
	
	protected $_string = "(`sloupec` as 'column')";
	
	protected $_operator;
	
	public function setUp() {
		$this->_operator = new BB_Db_Query_Operator($this->_data);
	}
	
	public function testToArray() {
		$this->assertEquals($this->_data, $this->_operator->toArray());
	}
	
	public function testToString() {
		$this->assertEquals($this->_string, $this->_operator->__toString());
	}
}
