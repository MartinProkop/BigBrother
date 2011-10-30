<?php
abstract class BB_Db_Query_Operator_Logic extends BB_Db_Query_Operator_Abstract {
	protected $_minOperands = 2;
	
	public function __toString() {
		$retVal = array($this->_operands[0]);
		
		$maxI = count($this->_operands);
		
		for ($i = 1; $i < $maxI; $i++) {
			$retVal[] = $this->_operator;
			$retVal[] = $this->_operands[$i];
		}
		
		return "(" . implode(" ", $retVal) . ")";
	}
} 