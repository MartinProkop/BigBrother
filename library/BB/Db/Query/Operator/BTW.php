<?php
class BB_Db_Query_Operator_BTW extends BB_Db_Query_Operator_Compare {
	protected $_maxOperands = 3;

	protected $_minOperands = 3;

	protected $_operator = "BETWEEN";

	protected $_operator2 = "AND";

	public function __toString() {
		$retVal = $this->_operands[0] . " " . $this->_operator;
		$retVal .= " " . $this->_operands[1] . " ";
		$retVal .= $this->_operator2 . " " . $this->_operands[2];

		return "(" . $retVal . ")";
	}
}
