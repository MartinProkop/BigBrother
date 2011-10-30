<?php
abstract class BB_Db_Query_Operator_Abstract implements BB_Db_Query_Interface {
	const OPERATOR_KEY = "op";
	
	protected $_operator;
	
	protected $_operands = array();
	
	protected $_minOperands = 0;
	
	protected $_maxOperands = -1;
	
	public function __construct($data) {
		// kontrola vstupnich dat
		if (!is_array($data))
			throw new BB_Db_Query_Exception("Input data must be type of array");
		
		if (!isset($data[self::OPERATOR_KEY]))
			throw new BB_Db_Query_Exception("Key 'operator' not found in input array");
		
		// kontrola poctu operandu
		$count = count($data) - 1;
		
		if ($count < $this->_minOperands || ($count > $this->_maxOperands && $this->_maxOperands != -1))
			throw new BB_Db_Query_Exception("Wrong count of operands.");
		
		// zapis dat operandu
		foreach ($data as $key => $value)
			if (strcmp($key, self::OPERATOR_KEY))
				$this->_operands[] = BB_Db_Query_Factory::factory($value);
	}
	
	public function toArray($recursive = true) {
		$retVal = array();
		
		foreach ($this->_operands as $operand) {
			$retVal[] = $operand->toArray();
		}
		
		$operator = explode("_", get_class($this));
		
		$retVal[self::OPERATOR_KEY] = array_pop($operator);
		
		return $retVal;
	}
	
	public function addOperand($operand) {
		$count = count($this->_operands) + 1;
		
		if ($this->_maxOperands != -1 && $count > $this->_maxOperands)
			throw new BB_Db_Query_Exception("Wrong count of operands.");
		
		// pridani operandu
		$this->_operands[] = BB_Db_Query_Factory::factory($operand);
		
		return $this;
	}
	
	public function count() {
		return count($this->_operands);
	}
	
	public function deleteOperand($index) {
		$buffer = array();
		
		$maxI = count($this->_operands);
		
		for ($i = 0; $i < $maxI; $i++) {
			if ($i != $index)
				$buffer[] = $this->_operands[$i];
		}
		
		$this->_operands = $buffer;
		
		return $this;
	}
	
	public function getOperand($index) {
		return isset($this->_operands[$index]) ? $this->_operands[$index] : null;
	}
	
	public function updateOperand($operand, $index) {
		if (!isset($this->_operands[$index]))
			return $this;
		
		$this->_operands[$index] = BB_Db_Query_Factory::factory($operand);
		
		return $this;
	}
}
