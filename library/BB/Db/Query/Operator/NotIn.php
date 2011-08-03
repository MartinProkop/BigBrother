<?php
class BB_Db_Query_Operator_NotIn extends BB_Db_Query_Operator {
	const OPERATOR_STRING = "NOT IN";
	
	public function __toString() {
		$left = $this->_operands[0];
		
		$operands = $this->_operands;
		$operands = array_shift($operands);
		$operandsString = "(" . implode(",", $operands) . ")";
		
		return "(" . $left . " " . self::OPERATOR_STRING . " (" . $operandsString . "))";
	}
}