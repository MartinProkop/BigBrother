<?php
class BB_Db_Query_Operator_And extends BB_Db_Query_Operator {
	const OPERATOR_STRING = "AND";
	
	public function __toString() {
		return "(" . implode(" " . self::OPERATOR_STRING . " ", $this->_operands) . ")";
	}
}
