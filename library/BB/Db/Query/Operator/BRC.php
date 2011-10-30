<?php
class BB_Db_Query_Operator_BRC extends BB_Db_Query_Operator_Abstract {
	protected $_maxOperands = 1;

	protected $_minOperands = 1;

	protected $_operator = "()";

	public function __toString() {
		return $this->_operator[0] . $this->_operands[0] . $this->_operator[1];
	}
}
