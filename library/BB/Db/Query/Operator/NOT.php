<?php
class BB_Db_Query_Operator_NOT extends BB_Db_Query_Operator_Abstract {
	protected $_maxOperands = 1;
	
	protected $_minOperands = 1;
	
	protected $_operator = "NOT";
	
	public function __toString() {
		return "(" . $this->_operator . " " . $this->_operands[0] . ")";
	}
}
