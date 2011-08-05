<?php
class BB_Db_Query_Expresion implements BB_Db_Query_Interface {
	const PLACEHOLDER = "?";
	
	protected $_expresion = "";
	
	protected $_params = array();
	
	protected $_pureData = array();
	
	public function __construct($expresion = "", array $params = array()) {
		$this->_expresion = $expresion;
		
		foreach ($params as $value) $this->_params[] = BB_Db_Query_Factory::factory($value);
		
		$this->_pureData = array_merge($params, array(BB_Db_Query_Factory::EXPRESION => $expresion));
	}
	
	public function toArray($recursive = true) {
		return $this->_pureData;
	}
	
	public function __toString() {
		$params = implode(",", $this->_params);
		
		return "(" . str_replace(self::PLACEHOLDER, $params, $this->_expresion) . ")";
	}
}
