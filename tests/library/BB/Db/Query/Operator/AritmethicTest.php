<?php
class BB_Db_Query_Operator_ArithmeticTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		
	}
	
	public function testCommon() {
		$key = BB_Db_Query_Operator_Abstract::OPERATOR_KEY;
		
		$data = array($key => "PLS", 5, 7, 8);
		$plus = new BB_Db_Query_Operator_PLS($data);
		$this->assertEquals("('5' + '7' + '8')", $plus->__toString());
		$this->assertEquals($data, $plus->toArray());
		
		$data = array($key => "MNS", 5, 7, 8);
		$mns = new BB_Db_Query_Operator_MNS($data);
		$this->assertEquals("('5' - '7' - '8')", $mns->__toString());
		$this->assertEquals($data, $mns->toArray());
		
		$data = array($key => "TMS", 5, 7, 8);
		$TMS = new BB_Db_Query_Operator_TMS($data);
		$this->assertEquals("('5' * '7' * '8')", $TMS->__toString());
		$this->assertEquals($data, $TMS->toArray());
		
		$data = array($key => "DVD", 5, 7, 8);
		$DVD = new BB_Db_Query_Operator_DVD($data);
		$this->assertEquals("('5' / '7' / '8')", $DVD->__toString());
		$this->assertEquals($data, $DVD->toArray());
	}
}
