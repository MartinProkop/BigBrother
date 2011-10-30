<?php
class BB_Db_Query_Expresion implements BB_Db_Query_Interface {
	const PLACEHOLDER = "?";
	
	const EXPR_KEY = "ex";
	
	protected $_expresion = "";
	
	protected $_params = array();
	
	public function __construct($data) {
		// kontrola dat
		if (!is_array($data) || !isset($data[self::EXPR_KEY]))
			throw new BB_Db_Query_Exception("Invalid format of input data. Expr key not set");
		
		// zapis vyrazu
		$this->_expresion = $data[self::EXPR_KEY];
		
		// zapis dat
		foreach ($data as $key => $val) {
			
			if (strcmp($key, self::EXPR_KEY))
				$this->_params[] = BB_Db_Query_Factory::factory($val);;
		}
	}
	
	public function toArray($recursive = true) {
		$retVal = array();
		
		foreach ($this->_params as $operand) {
			$retVal[] = $operand->toArray();
		}
		
		$retVal[self::EXPR_KEY] = $this->_expresion;
		
		return $retVal;
	}
	
	public function __toString() {
		// nahrazeni placeholderu parametry
		$retVal = $this->_expresion;
		
		$index = 0;
		
		while (strpos($retVal, self::PLACEHOLDER) !== false) {
			if (!isset($this->_params[$index]))
				throw new BB_Db_Query_Exception("Wrong count of parameters");
			
			$retVal = $this->_replaceFirst(self::PLACEHOLDER, $this->_params[$index], $retVal);
			$index++;
		}
		
		return "(" . $retVal . ")";
	}
	
	public function getExpr() {
		return $this->_expresion;
	}
	
	public function getParams() {
		return $this->_params;
	}
	
	public function setExpr($expr) {
		$this->_expresion = $expr;
		
		return $this;
	}
	
	public function setParams(array $params) {
		// vymazani starych parametru
		$this->_params = array();
		
		foreach ($params as $param) {
			$this->_params[] = BB_Db_Query_Factory::factory($param);
		}
		
		return $this;
	}
	
	protected static function _replaceFirst($placeholder, $replace, $subject) {
		// nacteni pozice placeholderu
		$start = strpos($subject, $placeholder);
		
		if ($start === false)
			return $subject;
		
		// nacteni konce placeholderu
		$end = $start + strlen($placeholder);
		
		// rozdeleni retezce na casti a vlozeni dat
		$startPart = substr($subject, 0, $start);
		$endPart = substr($subject, $end);
		
		return $startPart . $replace . $endPart;
	}
}
