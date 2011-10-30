<?php
abstract class BB_Db_Query_Operator_Set extends BB_Db_Query_Operator_Abstract {
	protected $_minOperands = 2;

	public function __toString() {
		// naklonovani operandu a zapsani testovaneho operandu
		$operands = $this->_operands;

		$retVal = array_shift($operands);
		$retVal .= " " . $this->_operator . " ";

		// sepsani mnoziny universa
		$universum = implode(",",$operands);

		return $retVal . "(" . $universum . ")";
	}
}
