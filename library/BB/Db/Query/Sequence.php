<?php
class BB_Db_Query_Sequence implements BB_Db_Query_Interface {
	public $allowedOperators = array(
		"AND",
		"OR",
		"NOT",
		"+",
		"-",
		"/",
		"*",
		"=",
		"!=",
		"<",
		"<=",
		">",
		">=",
		"BETWEEN",
		"IN",
		"NOT IN"
	);
	
	protected $_operands = array();
	
	protected $_operators = array();
	
	public function __construct($data) {
		// kontrola poctu argumentu
		$count = count($data);
		
		// pocet prvku pole musi byt lichy nebo 0
		if ($count && ($count % 2 == 0))
			throw new BB_Db_Query_Exception("Count of elements must be even");
		
		// pokud je pocet prvku 0, vykoci se z kontruktoru
		if (!$count)
			return;
		
		// zapis prvniho prvku
		reset($data);
		$this->_operands[] = BB_Db_Query_Factory::factory(current($data));
		next($data);
		
		// prchazeni dat
		while (current($data) !== false) {
			// nacteni operatoru a operandu
			$operator = current($data);
			next($data);
			$operand = current($data);
			next($data);
			
			// faktorizace operandu
			$operand = BB_Db_Query_Factory::factory($operand);
			
			// kontrola operatoru
			$operator = $this->_checkOperator($operator);
			
			// zapis dat
			$this->_operands[] = $operand;
			$this->_operators[] = $operator;
		}
	}

	public function __toString() {
		$retVal = "";
		
		// kotntrola poctu operatoru
		$maxI = count($this->_operands);
		
		if (!$maxI)
			return $retVal;
		
		$retVal .= $this->_operands[0];
		
		// zapis dalsich operatoru
		for ($i = 1; $i < $maxI; $i++) {
			$retVal .= " " . $this->_operators[$i - 1] . " " . $this->_operands[$i];
		}
		
		return "(" . $retVal . ")";
	}
	
	public function toArray($recursive = true) {
		$retVal = array();
		
		// kotntrola poctu operandu
		$maxI = count($this->_operands);
		
		if (!$maxI)
			return $retVal;
		
		// zapis prvniho operandu
		$retVal[] = $this->_operands[0]->toArray();
		
		for ($i = 1; $i < $maxI; $i++) {
			$retVal[] = $this->_operators[$i - 1];
			$retVal[] = $this->_operands[$i]->toArray();
		}
		
		return $retVal;
	}
	
	public function addOperand($operand, $operator) {
		if (!count($this->_operands)) {
			$this->_operands[] = BB_Db_Query_Factory::factory($operand);
			return $this;
		}
		
		// kontrola operatoru
		$operator = $this->_checkOperator($operator);
		
		// zapis dat
		$this->_operands[] = BB_Db_Query_Factory::factory($operand);
		$this->_operators[] = $operator;
	}
	
	public function removeOperand($operandIndex) {
		// kontrola existence
		if (!isset($this->_operands[$operandIndex]))
			return $this;
		
		// odebrani dat
		$operatorBuffer = array();
		$operandBuffer = array();
		
		$maxI = count($this->_operands);
		
		for ($i = 0; $i < $maxI; $i++) {
			// porovnani indexu s iteratorem i
			if ($i != $operandIndex) {
				// prvek nema byt vyrazen
				$operandBuffer[] = $this->_operands[$i];
				
				// pokud je $i vetsi nez 0, prevede se operator
				if ($i) {
					$operatorBuffer[] = $this->_operators[$i - 1];
				}
			}
		}
		
		// zapis novych dat
		$this->_operands = $operandBuffer;
		$this->_operators = $operatorBuffer;
		
		return $this;
	}
	
	public function getOperand($index) {
		$retVal = new stdClass;
		
		$retVal->operator = null;
		$retVal->operand = null;
		
		if (!isset($this->_operands[$index]))
			return $retVal;
		
		$retVal->operand = $this->_operands[$index];
		
		if ($index)
			$retVal->operator = $this->_operators[$index - 1];
		
		return $retVal;
	}
	
	public function getOperands() {
		$retVal = array();
		
		$maxI = count($this->_operands);
		
		for ($i = 0; $i < $maxI; $i++)
			$retVal[] = $this->getOperand($i);
		
		return $retVal;
	}
	
	public function setOperator($operator, $operandIndex) {
		if (!isset($this->_operands[$operandIndex]) || !$operandIndex)
			return $this;
		
		// kontrola operatoru
		$operator = $this->_checkOperator($operator);
		
		$this->_operators[$operandIndex - 1] = $operator;
		
		return $this;
	}
	
	public function setOperand($operand, $operandIndex) {
		if (!isset($this->_operands[$operandIndex]))
			return $this;
		
		$this->_operands[$operandIndex] = BB_Db_Query_Factory::factory($operand);
		
		return $this;
	}
	
	protected function _checkOperator($operator) {
		// kontrola operatoru
		if (!is_string($operator)) {
			throw new BB_Db_Query_Exception("Operator must be type of string");
		}
		
		$operator = strtoupper($operator);
		
		if (!in_array($operator, $this->allowedOperators))
			throw new BB_Db_Query_Exception("Unsupported operator " . $operator);
		
		return $operator;
	}
}
