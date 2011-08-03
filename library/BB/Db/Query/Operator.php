<?php
class BB_Db_Query_Operator implements BB_Db_Query_Interface {
	protected $_operator;
	
	protected $_operands = array();
	
	public function __construct(array $data) {
		// check of data validity
		if (!isset($data["operator"])) throw new BB_Db_Query_Operator_Exception("Operator is not set");
		
		if (!is_string($data["operator"])) throw new BB_Db_Query_Operator_Exception("Operator must be string");
		
		$this->_operator = $data["operator"];
	}
	
	public function toArray($recursive = true) {
		$params = $this->_operands;
		
		foreach ($params as & $parameter) {
			if (is_object($parameter)) $parameter = $parameter->toArray();
		}
		
		return array_merge($params, array(BB_Db_Query_Operator_Factory::OPERATOR => $this->_operator));
	}
	
	public function __toString() {
		if (count($this->_operands) < 2) throw new BB_Db_Query_Operator_Exception("Common operator needs 2 pramaters");
		
		return "(" . $this->_operands[0] . " " . $this->_operator ." " . $this->_operands[1] . ")";
	}
}
