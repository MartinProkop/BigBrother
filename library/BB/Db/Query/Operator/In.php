<?php
class BB_Db_Query_Operator_In extends BB_Db_Query_Operator {
	const OPERATOR_STRING = "IN";
	
	public function __toString() {
		$left = $this->_operands[0];
		
		$operands = $this->_operands;
		$operands = array_shift($operands);
		$operandsString = "(" . implode(",", $operands) . ")";
		
		return "(" . $left . " " . self::OPERATOR_STRING . " (" . $operandsString . "))";
	}
}