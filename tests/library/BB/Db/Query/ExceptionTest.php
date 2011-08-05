<?php
class BB_Db_Query_ExceptionTest extends PHPUnit_Framework_TestCase {
	protected $e;
	
	public function setUp() {
		$this->e = new BB_Db_Query_Exception();
	}
	
	/**
	 * @expectedException BB_Db_Query_Exception
	 */
	public function testException() {
		throw $this->e;
	}
}
	