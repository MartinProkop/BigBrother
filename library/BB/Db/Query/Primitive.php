<?php
class BB_Db_Query_Primitive implements BB_Db_Query_Interface {
	protected $_value;
	
	public function __construct($data) {
		$this->_value = $data;
	}
	
	public function __toString() {
		return "'" . mysql_escape_string($this->_value) . "'";
	}
	
	public function toArray($recursive = true) {
		return $this->_value;
	}
	
	public function getValue() {
		return $this->_value;
	}
	
	public function setValue($value) {
		$this->_value = $value;
		
		return $this;
	}
}
