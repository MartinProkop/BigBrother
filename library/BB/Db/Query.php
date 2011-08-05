<?php
class BB_Db_Query implements BB_Db_Query_Interface, ArrayAccess {
	protected $_operators = array();
	
	protected $_operands = array();
	
	protected $_pureData = array();
	
	public function __construct(array $data = null) {
		if ($data) $this->set($data);
	}
	
	public function set(array $data) {
		if (!(count($data) % 2)) throw new BB_Db_Query_Exception("Count of parameters must be odd");
		
		$isEven = true;
		
		foreach ($data as $item) {
			if ($isEven) {
				$this->_operands[] = BB_Db_Query_Factory::factory($item);
			} else {
				$this->_operators[] = mysql_escape_string($item);
			}
			
			
			$isEven = !$isEven;
		}
		
		$this->_pureData = $data;
		
		return $this;
	}
	
	public function toArray($recursive = true) {
		return $this->_pureData;
	}
	
	public function __toString() {
		$maxI = count($this->_operands);
		
		if (!$maxI)
			return "1";
		
		$strings = array();
		
		// zapsani prvku
		$maxI = count($this->_operands);
		
		for ($i = 0; $i < $maxI; $i++) {
			if ($i) $strings[] = $this->_operators[$i - 1];
			
			$strings[] = is_object($this->_operands[$i]) ? $this->_operands[$i]->__toString() : $this->_operands[$i];
		}
		
		return implode(" ", $strings);
	}
	
	public function offsetExists($offset) {
		$isOdd = $offset % 2;
		$index = ceil($offset / 2);
		
		if ($isOdd) {
			return isset($this->_operators[$index - 1]);
		}
		
		return isset($this->_operands[$index]);
	}
	
	public function offsetGet($offset) {
		$isOdd = $offset % 2;
		$index = ceil($offset / 2);
		
		if ($isOdd) {
			return $this->_operators[$index - 1];
		}
		
		return $this->_operands[$index];
	}
	
	public function offsetSet($offset, $value) {

	}
	
	public function offsetUnset($offset) {
		return null;
	}
}
