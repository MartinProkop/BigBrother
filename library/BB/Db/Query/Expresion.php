<?php
class BB_Db_Query_Expresion implements BB_Db_Query_Interface {
	const PLACEHOLDER = "?";
	
	protected $_expresion = "";
	
	protected $_params = array();
	
	public function __construct($expresion = "", array $params = array()) {
		$this->_expresion = $expresion;
		
		foreach ($params as $value) $this->_params[] = BB_Db_Query_Operator_Factory::factory($value);
	}
	
	public function toArray($recursive = true) {
		$params = $this->_params;
		
		if ($recursive) {
			foreach ($params as & $parameter) {
				if (is_object($parameter)) $parameter = $parameter->toArray($recursive);
			}
		}
		
		return array_merge($params, array(BB_Db_Query_Operator_Factory::EXPRESION => $this->_expresion));
	}
	
	public function __toString() {
		$params = implode(",", $this->_params);
		
		return str_replace(self::PLACEHOLDER, $params, $this->_expresion);
	}
}
