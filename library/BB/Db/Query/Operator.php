<?php
class BB_Db_Query_Operator implements BB_Db_Query_Interface {
	protected $_operator;
	
	protected $_operands = array();
	
	protected $_pureData = array();
	
	public function __construct(array $data) {
		// check of data validity
		if (!isset($data["operator"])) throw new BB_Db_Query_Operator_Exception("Operator is not set");
		
		if (!is_string($data["operator"])) throw new BB_Db_Query_Operator_Exception("Operator must be string");
		
		$this->_operator = $data["operator"];
		
		$this->_pureData = $data;
		
		foreach ($data as $index => $value) {
			if (is_numeric($index)) {
				$this->_operands[] = BB_Db_Query_Factory::factory($value);
			}
		}
	}
	
	public function toArray($recursive = true) {
		return $this->_pureData;
	}
	
	public function __toString() {
		if (count($this->_operands) < 2) throw new BB_Db_Query_Exception("Common operator needs 2 pramaters");
		
		return "(" . $this->_operands[0] . " " . $this->_operator ." " . $this->_operands[1] . ")";
	}
}
