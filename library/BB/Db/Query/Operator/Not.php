<?php
class BB_Db_Query_Operator_Not extends BB_Db_Query_Operator {
	const OPERATOR_STRING = "NOT";
	
	public function __toString() {
		return "(" . self::OPERATOR_STRING . " " . $this->_operands[0] . ")";
	}
}
