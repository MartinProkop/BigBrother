<?php
class BB_Db_Query_Operator_Or extends BB_Db_Query_Operator {
	const OPERATOR_STRING = "OR";
	
	public function __toString() {
		return "(" . implode(" " . self::OPERATOR_STRING . " ", $this->_operands) . ")";
	}
}