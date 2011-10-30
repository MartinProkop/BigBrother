<?php
class BB_Db_Query_ExpresionTest extends PHPUnit_Framework_TestCase {
	protected $_expresion;
	
	protected $_data = array(
		BB_Db_Query_Expresion::EXPR_KEY => "CONCAT(?,?,?)",
		"polozka",
		"polozka",
		4
	);
	
	protected $_string = "(CONCAT('polozka','polozka','4'))";
	
	public function setUp() {
		$this->_expresion = new BB_Db_Query_Expresion($this->_data);
	}
	
	public function testToArray() {
		$this->assertEquals($this->_data, $this->_expresion->toArray());
	}
	
	public function testToString() {
		$this->assertEquals($this->_string, $this->_expresion->__toString());
	}
	
	public function testGetSet() {
		$obj = new BB_Db_Query_Expresion($this->_data);
		
		$key = BB_Db_Query_Expresion::EXPR_KEY;
		$newExp = "MAX(?,?,?)";
		
		// test setu a getu vyrazu
		$obj->setExpr($newExp);
		$this->assertEquals($newExp, $obj->getExpr());
		
		// test setu a getu parametru
		$params = array(6,7,8);
		$obj->setParams($params);
		
		$paramsPrim = $obj->getParams();
		$newParams = array();
		
		foreach ($paramsPrim as $key => $val)
			$newParams[$key] = $val->getValue();
		
		$this->assertEquals($params, $newParams);
	}
}
