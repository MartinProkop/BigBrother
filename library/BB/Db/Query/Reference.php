<?php
class BB_Db_Query_Reference implements BB_Db_Query_Interface {
	const SYMBOL = "`";
	
	const REG_EXP = '/^\[[[:alnum:]]+\.[[:alnum:]]+\]$/';
	
	public static $objectType;
	
	private static $_tables;
	
	private $_columnName;
	
	private $_tableName;
	
	public function __construct($data) {
		// odstraneni zavorek
		$data = trim($data, "[]");
		
		// rozlozeni na tabulku a sloupec
		list($table, $column) = explode(".", $data);
		
		// zapis dat
		$this->_tableName = $table;
		$this->_columnName = $column;
		
		// zapis do seznamu pouzitych tabulek
		self::$_tables[] = $table;
	}
	
	public function __toString() {
		// sestaveni jmena tabulky
		$tableName = self::SYMBOL . self::$objectType . "_" . mysql_escape_string($this->_tableName) . self::SYMBOL;
		
		// sestaveni jmena sloupce
		$columnName = self::SYMBOL . $this->_columnName . self::SYMBOL;
		
		// kompletni sestaveni reference a jeji vraceni
		return $tableName . "." . $columnName;
	}
	
	public function toArray($recursive = true) {
		return "[" . $this->_tableName . "." . $this->_columnName . "]";
	}
	
	public static function clearTables() {
		self::$_tables = array();
	}
	
	public static function getTables() {
		$retVal = array();
		
		foreach (self::$_tables as $tableName) {
			// pokud tabulka jeste neni v seznamu pro vraceni, prida se
			if (!in_array($tableName, $retVal))
				$retVal[] = $tableName;
		}
		
		return $retVal;
	}
	
	public function getColumn() {
		return $this->_columnName;
	}
	
	public function getTable() {
		return $this->_tableName;
	}
	
	public function setColumn($columnName) {
		$this->_columnName = $columnName;
		
		return $this;
	}
	
	public function setTableName($tableName) {
		// prejmenovani tabulky v seznamu
		if (($key = array_search($this->_tableName, self::$_tables)) !== false)
			self::$_tables[$key] = $tableName;
		
		$this->_tableName = $tableName;
		
		return $this;
	}
}
