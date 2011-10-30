<?php
class BB_Db_Query_SequenceTest extends PHPUnit_Framework_TestCase {
	protected $_data = array(
		"ahoj",
		"AND",
		"nazdar"
	);
	
	protected $_obj;
	
	public function setUp() {
		$this->_obj = new BB_Db_Query_Sequence($this->_data);
	}
	
	public function testOutput() {
		$this->assertEquals($this->_data, $this->_obj->toArray());
		$this->assertEquals("('ahoj' AND 'nazdar')", $this->_obj->__toString());
	}
	
	public function testGet() {
		$obj = new BB_Db_Query_Sequence($this->_data);
		
		// get vsech operandu (pokud funguje ten, funguje i get jednohoo operandu)
		$operands = $obj->getOperands();
		
		foreach ($operands as &$operand)
			$operand->operand = $operand->operand->getValue();
		
		$expected = array((object) array("operator" => null, "operand" => "ahoj"), (object) array("operator" => "AND", "operand" => "nazdar"));
		
		$this->assertEquals($expected, $operands);
	}
	
	public function testAddRemove() {
		$obj = new BB_Db_Query_Sequence($this->_data);
		
		// test add
		$obj->addOperand("5", "AND");
		$expected = (object) array("operator" => "AND", "operand" => "5");
		
		$operand = $obj->getOperand(2);
		$operand->operand = $operand->operand->getValue();
		
		$this->assertEquals($expected, $operand);
		
		// test remove
		$obj->removeOperand(2);
		
		$count = count($obj->getOperands());
		$this->assertEquals(2, $count);
		
		// test pridani prvniho operandu
		$obj = new BB_Db_Query_Sequence(array("5"));
		$obj->removeOperand(0);
		
		$obj->addOperand("2", null);
		
		$expected = (object) array("operator" => null, "operand" => "2");
		$operand = $obj->getOperand(0);
		$operand->operand = $operand->operand->getValue();
		
		$this->assertEquals($expected, $operand);
	}
	
	public function testSetOperatorOperand() {
		$obj = new BB_Db_Query_Sequence($this->_data);
		
		// zmena prvniho operandu
		$obj->setOperand("test", 0);
		
		// zmena druheho operatoru
		$obj->setOperator("OR", 1);
		
		// zmena prviho operatoru
		$obj->setOperator("AND", 0);
		
		$expected = array(
			(object) array("operator" => null, "operand" => "test"),
			(object) array("operator" => "OR", "operand" => "nazdar")
		);
		
		$operators = $obj->getOperands();
		foreach ($operators as $operator)
			$operator->operand = $operator->operand->getValue();
		
		$this->assertEquals($expected, $operators);
	}
	
	/**
	 * @expectedException BB_Db_Query_Exception
	 */
	public function testException() {
		$data = $this->_data;
		$data[1] = array();
		
		new BB_Db_Query_Sequence($data);
	}
}
