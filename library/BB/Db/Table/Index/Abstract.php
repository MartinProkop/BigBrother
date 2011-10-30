<?php
abstract class BB_Db_Table_Index_Abstract extends Zend_Db_Table_Abstract {
	protected $_rowClass = "BB_Db_Table_Index_Row";
	
	protected $_rowsetClass = "BB_Db_Table_Index_Rowset";
	
	protected $_uuidColumns = array();
	
	public function getTranslatedUuids() {
		$retVal = array();
		$name = $this->_name;
		
		// prochazeni klicovych sloupcu a sestaveni alternaticnich jmen
		foreach ($this->_uuidColumns as $column) {
			// sestaveni skutecneho a alternativniho jmena
			$alterName = "tbl_" . $name . "_" . $column;
			$realName = "`$name`.`$column`";
			
			// sestaveni vysledku
			$retVal[$realName] = $alterName;
		}
		
		return $retVal;
	}
	
	public function getUuidCols() {
		return $this->_uuidColumns;
	}
	
	abstract public function indexRow(BB_Db_Table_Data_Row $row);
	
	abstract public function isIndexed(BB_Db_Table_Data_Row $row);
}
