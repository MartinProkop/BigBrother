<?php
class BB_Db_Table_Index_Row extends Zend_Db_Table_Row_Abstract {
	public function getObjects(BB_Db_Table_Data_Abstract $dataTable) {
		// nacteni uuid
		$uuids = $this->getUuids();
		
		// kontorla prazdneho seznamu
		if (empty($uuids))
			$uuids[] = 0;
		
		// vraceni dat
		return $dataTable->find($uuids);
	}
	
	public function getUuids() {
		// nacteni seznamu sloupcu s UUID
		$table = $this->getTable();
		$uuidsColumns = $table->getUuidCols();
		
		$retVal = array();
		
		// prochazeni nazvu sloupcu a pridavani dat do seznamu k vraceni
		foreach ($uuidsColumns as $column)
			$retVal[] = $this->_data[$column];
		
		// vraceni dat
		return $retVal;
	}
}
