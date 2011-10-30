<?php
class BB_Db_Table_Index_Rowset extends Zend_Db_Table_Rowset_Abstract {
	public function getObjects(BB_Db_Table_Data_Abstract $dataTable) {
		// ziskani seznamu UUID
		$uuids = $this->getUuids();
		
		// kontrola prazdneho seznamu
		if (empty($uuids))
			$uuids[] = 0;
		
		// vraceni objektu
		return $dataTable->find($uuids);
	}
	
	public function getUuids() {
		$retVal = array();
		
		// prochazeni zaznamu a vraceni dat
		for ($i = 0; $i < $this->_count; $i++) {
			$row = $this->_loadAndReturnRow($i);
			
			// slouceni UUID radku s celkem
			$retVal = array_merge($row->getUuids(), $retVal);
		}
		
		// vraceni vysledku
		return $retVal;
	}
}
