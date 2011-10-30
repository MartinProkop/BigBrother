<?php
abstract class BB_Db_Query_Operator_Compare extends BB_Db_Query_Operator_Abstract {
	protected $_minOperands = 2;
	
	protected $_maxOperands = 2;
	
	public function __toString() {
		return "(" . $this->_operands[0] . " " . $this->_operator . " " . $this->_operands[1] . ")";
	}
}
