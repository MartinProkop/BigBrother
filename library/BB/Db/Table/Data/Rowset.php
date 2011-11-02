<?php
class BB_Db_Table_Data_Rowset extends Zend_Db_Table_Rowset_Abstract {
	public function __get($name) {
		$current = $this->current();
		
		if (!$current)
			return null;
		
		return $current->__get($name);
	}
	
	public function __set($name, $value) {
		// prochazeni radku a nastavovani hodnot
		for ($i = 0; $i < $this->_count; $i++) {
			$this->_loadAndReturnRow($i)->$name = $value;
		}
		
		return $value;
	}
	
	public function save($userId) {
		$uuids = array();
		
		// prochazeni radku a jejich ukladani
		for ($i = 0; $i < $this->_count; $i++) {
			$uuids[] = $this->_loadAndReturnRow($i)->save($userId);
		}
		
		return $uuids;
	}
	
	public function delete() {
		// smaze vsechny radku v setu
		$uuids = array(0);
		
		foreach ($this as $row) {
			$uuids[] = $row->getUuid();
		}
		
		$adapter = $this->getTable()->getAdapter();
		
		$sql = $adapter->quoteInto("uuid in (?)", $uuids);
		
		return $this->getTable()->delete($sql);
	}
}
