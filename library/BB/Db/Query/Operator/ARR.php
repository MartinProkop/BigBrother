<?php
class BB_Db_Query_Operator_ARR extends BB_Db_Query_Operator_Abstract {
	protected $_operator = ",";

	public function __toString() {
		return implode($this->_operator, $this->_operands);
	}
}
